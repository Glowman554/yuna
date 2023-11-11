import { Route } from "../../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";
import { secureRoute } from "./common.ts";

export function userDeleteEndpoint(connection: mysql.Connection): Route {
    return secureRoute(connection, "/api/user/delete", "GET", async (_req, res, username, _token) => {
        await connection.execute("DELETE FROM users WHERE username = ?", [username]);
        res.send({});
    });
}