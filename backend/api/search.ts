import { Route } from "../route.ts";
import mysql from "npm:mysql2@3.6.2/promise";

export function searchEndpoint(connection: mysql.Connection): Route {
    return {
        path: "/search",
        method: "GET",
        handler: async (req, res) => {
            const query = req.query.q;
            if (!query || typeof(query) != "string") {
                throw new Error("Missing parameter q");
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
                throw new Error("Invalid number format");
            }

            // passing as a integer causes crash??
            // thats a mysql bug lol
            const [rows, _fields] = await connection.execute(`SELECT site_id, link, title, MATCH (title, link, text) AGAINST (?) as score FROM sites WHERE MATCH (title, link, text) AGAINST (?) ORDER BY score DESC LIMIT ? OFFSET ?`,
               [ query, query, String(limitInt), String(offsetInt) ]
            );

            res.send(rows);
        }
    }
}