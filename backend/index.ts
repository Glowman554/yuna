// @deno-types="npm:@types/express@4.17.15"
import express, { Express } from "npm:express@4.18.2";
import mysql from "npm:mysql2@3.6.2/promise";
import { setup } from "./database.ts";
import { Route } from "./route.ts";
import { loggingLayer } from "./logger.ts";
import { searchEndpoint } from "./api/search.ts";

import * as https from "node:https";

function add(app: Express, route: Route) {
    console.log(`New endpoint ${route.method} ${route.path}`);
    switch (route.method) {
        case "GET":
            app.get(route.path, route.handler);
            break;
        case "POST":
            app.post(route.path, route.handler);
            break;
    }
}

interface SslConfig {
    key: string;
    cert: string;
}

async function main() {
    const connection = await mysql.createPool({...JSON.parse(Deno.readTextFileSync(Deno.args[0] + "database.json")),
        waitForConnections: true,
        connectionLimit: 2,
        queueLimit: 0,
        enableKeepAlive: true,
        keepAliveInitialDelay: 0
    });
    await setup(connection);


    const app = express();

    app.use(loggingLayer);
    app.use(express.static("../frontend"));

    add(app, searchEndpoint(connection));

    let sslConfig: SslConfig | undefined;
    try {
        sslConfig = JSON.parse(Deno.readTextFileSync(Deno.args[0] + "ssl.json")) as SslConfig;
    } catch (e) {
        console.log(e);
    }

    if (sslConfig) {
        const options = {
            key: Deno.readTextFileSync(sslConfig.key),
            cert: Deno.readTextFileSync(sslConfig.cert)
        };
        https.createServer(options, app).listen(443);
    } else {
        app.listen(8000);
    }
}

main();