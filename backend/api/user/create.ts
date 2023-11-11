import { Route } from "../../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";
import { createUserToken, hashPassword } from "./common.ts";

import { sendGenericError } from "../error.ts";

export function userCreateEndpoint(connection: mysql.Connection): Route {
    return {
        path: "/api/user/create",
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
                await connection.execute("insert into `users` (username, password_hash, admin, premium, pfp_url) values (?, ?, 0, 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Anthro_vixen_colored.jpg/220px-Anthro_vixen_colored.jpg')", [ username, hashPassword(password) ]);
                await connection.execute("insert into `user_sessions` (username, token) values (?, ?)", [ username, token ]);

                res.send({
                    token: token
                });
            } catch (_e) {
                sendGenericError(res, "Failed to create user");
            }
        }
    }
}