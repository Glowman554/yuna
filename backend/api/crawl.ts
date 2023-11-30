import { Route } from "../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";
import { secureRoute } from "./user/common.ts";
import { sendGenericError } from "./error.ts";

export function crawlEndpoint(connection: mysql.Connection): Route {
    return secureRoute(connection, "/api/crawl", "GET", async (req, res, username, _token) => {
        const url = req.query.url;
        if (url == null || typeof(url) != "string") {
            sendGenericError(res, "Missing or invalid url!");
            return;
        }

        const result = await fetch("http://crawler/api/crawl?url=" + url);
        if (result.status != 200) {
            sendGenericError(res, "Something went wrong!");
            return;
        }
        const status = await result.text();

        await connection.execute("insert into crawl_requests (username, link, status) values (?, ?, ?)", [ username, url, status ]);

        res.send({
            status: status
        });
    });
}