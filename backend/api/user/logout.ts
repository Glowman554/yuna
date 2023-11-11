import { Route } from "../../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";
import { secureRoute } from "./common.ts";

export function userLogoutEndpoint(connection: mysql.Connection): Route {
    return secureRoute(connection, "/api/user/logout", "GET", async (_req, res, _username, token) => {
        await connection.execute("delete from user_sessions where token = ?", [ token ]);

        res.send({});
    });
}