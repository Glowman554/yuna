// deno-lint-ignore-file no-explicit-any
import { PaymentMethod, PaymentManager, StartingInformation } from "../paymentManager.ts";
import { getRandomElement } from "../utils.ts";
// @deno-types="npm:tonweb@0.0.62"
import TonWeb from "npm:tonweb@0.0.62/dist/tonweb.js";
// let's just ignore that
TonWeb.default = TonWeb as any;

interface Config {
    node_api_url: string;
    index_api_url: string;
    api_key: string;
    wallet_addresses: string[];
}

export class BlockSubscriptionIndex {
    tonWeb: TonWeb.default;
    config: Config;
    lastProcessedMasterchainBlockNumber: number;
    onTransaction: (tx: any) => void;

    constructor(tonWeb: TonWeb.default, lastProcessedMasterchainBlockNumber: number, onTransaction: (tx: any) => void, config: Config) {
        this.tonWeb = tonWeb;
        this.config = config;
        this.lastProcessedMasterchainBlockNumber = lastProcessedMasterchainBlockNumber;
        
        try {
            this.lastProcessedMasterchainBlockNumber = parseInt(Deno.readTextFileSync("./data/ton_last_processed_masterchain_block_number.txt"));
        } catch (_e) {
            // ignored
        }

        console.log(`[TON] Starts from ${this.lastProcessedMasterchainBlockNumber} masterchain block`);


        this.onTransaction = onTransaction;
    }

    getTransactionsByMasterchainSeqno(masterchainBlockNumber: number) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-API-Key': this.config.api_key
        };

        return fetch(this.config.index_api_url + 'getTransactionsByMasterchainSeqno?seqno=' + masterchainBlockNumber, {
            method: 'GET',
            headers: headers,
        }).then((response) => response.json()).then(response => response.error ? Promise.reject(response.error) : response);
    }

    start() {
        let isProcessing = false;

        const tick = async () => {
            if (isProcessing) {
                return;
            }
            isProcessing = true;

            try {
                const masterchainInfo = await this.tonWeb.provider.getMasterchainInfo();
                const lastMasterchainBlockNumber = masterchainInfo.last.seqno;

                if (lastMasterchainBlockNumber > this.lastProcessedMasterchainBlockNumber) {
                    const masterchainBlockNumber = this.lastProcessedMasterchainBlockNumber + 1;

                    const transactions = await this.getTransactionsByMasterchainSeqno(masterchainBlockNumber); 

                    for (const tx of transactions) {
                        await this.onTransaction(tx);
                    }

                    this.lastProcessedMasterchainBlockNumber = masterchainBlockNumber;
                    Deno.writeTextFileSync("./data/ton_last_processed_masterchain_block_number.txt", String(this.lastProcessedMasterchainBlockNumber));
                }
            } catch (_e) {
                // console.error(e);
            }

            isProcessing = false;
        }

        setInterval(tick, 1000);
    }
}

export class TonPayments implements PaymentMethod {
    paymentManager: PaymentManager;
    config: Config;
    tonWeb: TonWeb.default;

    constructor (paymentManager: PaymentManager) {
        this.paymentManager = paymentManager;
        this.config = JSON.parse(Deno.readTextFileSync("./data/ton_config.json"));
        this.config.wallet_addresses = this.config.wallet_addresses.map(address => new TonWeb.default.Address(address).toString(true, true, false));
        this.tonWeb = new TonWeb.default(new TonWeb.default.HttpProvider(this.config.node_api_url, { apiKey: this.config.api_key }));

        // deno-lint-ignore no-this-alias
        const _this = this;
        (async () => {
            const masterchainInfo = await _this.tonWeb.provider.getMasterchainInfo();
            new BlockSubscriptionIndex(_this.tonWeb, masterchainInfo.last.seqno, (tx) => {
                _this.processTransaction(tx);
            }, _this.config).start();
        })();
    }

    async processTransaction(tx: any) {
        if (tx.out_msgs.length > 0) {
            return;
        }
    
        if (this.checkDepositAddress(tx.account)) {
            const results = await this.tonWeb.provider.getTransactions(tx.account, 1, tx.lt, tx.hash);
            if (results.length < 1) {
                throw new Error('no transaction in node');
            }
            for (const result of results) {
                console.log(result);
                const paymentId = result.in_msg.message.trim();
                this.paymentManager.finish(paymentId, {
                    receivedMethod: "TON",
                    receivedFrom: result.in_msg.source,
                    receivedAmount: this.convertFromNano(result.in_msg.value)
                });
            }
        }
    }

    convertFromNano(nano: number) {
        return nano / 1e9;
    }

    checkDepositAddress(address: string) {
        return this.config.wallet_addresses.includes(new TonWeb.default.Address(address).toString(true, true, false));
    }

    getStartingInformation(): StartingInformation {
        return {
            methodName: "TON",
            walletAddress: getRandomElement(this.config.wallet_addresses)
        };
    }
}