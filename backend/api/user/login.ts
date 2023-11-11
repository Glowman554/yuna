import { Route } from "../../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";
import { createUserToken, comparePassword } from "./common.ts";

import { sendGenericError } from "../error.ts";

export function userLoginEndpoint(connection: mysql.Connection): Route {
    return {
        path: "/api/user/login",
        method: "GET",
        handler: async (req, res) => {
            const username = req.query.username;
            if (username == null || typeof(username) != "string") {
                sendGenericError(res, "Missing or invalid username!");
                return;
            }
            const password = req.query.password;
            if (password == null || typeof(password) != "string") {
                sendGenericError(res, "Missing or invalid password!");
                return;
            }

            const token = createUserToken();

            try {
                const [rows, _fields] = await connection.execute("select password_hash from `users` where username = ?", [ username ]) as [mysql.RowDataPacket[], mysql.FieldPacket[]];
                if (rows.length == 0 || !comparePassword(password, rows[0].password_hash)) {
                    sendGenericError(res, "Invalid username or password!");
                    return;
                }
                    
                await connection.execute("insert into `user_sessions` (username, token) values (?, ?)", [ username, token ]);

                res.send({
                    token: token
                });
            } catch (_e) {
                sendGenericError(res, "Failed to login user");
            }
        }
    }
}