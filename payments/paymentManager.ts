import { TonPayments } from "./methods/ton.ts";

export interface PendingPayment {
    receivedFrom?: string;
    receivedAmount?: number;
    receivedMethod?: string;
}

export interface StartingInformation {
    walletAddress: string;
    methodName: string;
}

export interface PaymentMethod {
    getStartingInformation(): StartingInformation;
}


export interface Begin {
    paymentId: string;
    methods: StartingInformation[];
}

export class PaymentManager {
    pendingPayments: {[paymentId: string] : PendingPayment};
    paymentMethods: PaymentMethod[];
    
    constructor () {
        this.pendingPayments = {};
        try {
            this.pendingPayments = JSON.parse(Deno.readTextFileSync("./data/pending.json"));
        } catch (_e) {
            // ignored
        }
        this.paymentMethods = [
            new TonPayments(this)
        ];
    }

    save() {
        Deno.writeTextFileSync("./data/pending.json", JSON.stringify(this.pendingPayments, null, "\t"));
    }

    createPaymentId() {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    
        let str = "";
        for (let i = 0; i < 19; i++) {
            str += chars.charAt(Math.floor(Math.random() * chars.length));
        }
    
        return str;
    }

    begin() {
        const paymentId = this.createPaymentId();
        this.pendingPayments[paymentId] = {};
        this.save();

        const ret: Begin = {
            paymentId: paymentId,
            methods: []
        };
        for (const method of this.paymentMethods) {
            ret.methods.push(method.getStartingInformation());
        }

        return ret;
    }

    finish(paymentId: string, result: PendingPayment) {
        if (this.pendingPayments[paymentId]) {
            this.pendingPayments[paymentId] = result;
            this.save();
        }
    }

    poll(paymentId: string): PendingPayment {
        const payment = this.pendingPayments[paymentId];
        if (!payment) {
            throw new Error("Payment not found!");
        }
        if (payment.receivedAmount && payment.receivedFrom && payment.receivedMethod) {
            delete this.pendingPayments[paymentId];
            this.save();
        }
        return payment;
    }
}