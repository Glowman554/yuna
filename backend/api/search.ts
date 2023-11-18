import { Route } from "../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";

import { sendGenericError } from "./error.ts";
import { resolveUserName } from "./user/common.ts";

export function searchEndpoint(connection: mysql.Connection): Route {
    return {
        path: "/api/search",
        method: "GET",
        handler: async (req, res) => {
            const query = req.query.q;
            if (!query || typeof(query) != "string") {
                sendGenericError(res, "Missing parameter q");
                return;
            }

            let limit = req.query.limit;
            if (!limit || typeof(limit) != "string") {
                limit = "10";
            }
            const limitInt = parseInt(limit);

            let offset = req.query.offset;
            if (!offset || typeof(offset) != "string") {
                offset = "0";
            }
            const offsetInt = parseInt(offset);

            if (!isFinite(limitInt) || !isFinite(offsetInt)) {
                sendGenericError(res, "Invalid number format");
                return;
            }

            if (limitInt > 100) {
                sendGenericError(res, "Limit should not be greater than 100!", limitInt);
                return;
            }

            const token = req.query.token;
            if (token && typeof(token) == "string") {
                const username = await resolveUserName(token, connection);
                if (username) {
                    await connection.execute("insert into `search_history` (username, search) values (?, ?)", [ username, query ]);
                }
            }
    

            // passing as a integer causes crash??
            // thats a mysql bug lol
            const [rows, _fields] = await connection.execute(`SELECT site_id, link, title, description, keywords, shortText, MATCH (title, link, text, keywords) AGAINST (?) as score FROM sites WHERE MATCH (title, link, text, keywords) AGAINST (?) ORDER BY score DESC LIMIT ? OFFSET ?`,
               [ query, query, String(limitInt), String(offsetInt) ]
            );

            res.send(rows);
        }
    }
}