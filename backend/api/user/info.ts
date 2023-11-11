import { Route } from "../../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";
import { secureRoute } from "./common.ts";
import { sendGenericError } from "../error.ts";

export function userInfoEndpoint(connection: mysql.Connection): Route {
    return secureRoute(connection, "/api/user/info", "GET", async (_req, res, username, _token) => {
        const [rows, _fields] = await connection.execute("select pfp_url, premium, admin from `users` where username = ?", [ username ]) as [mysql.RowDataPacket[], mysql.FieldPacket[]];
        if (rows.length == 0) {
            sendGenericError(res, "User not found", username);
            return;
        }
        res.send(rows[0]);
    });
}