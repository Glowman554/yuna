import * as bcrypt from "https://deno.land/x/bcrypt@v0.2.4/mod.ts";
import mysql from "npm:mysql2@3.6.2/promise";
import { Route } from "../../route.ts";

// @deno-types="npm:@types/express@4.17.15"
import { Request, Response } from 'npm:express@4.18.2';

import { sendGenericError } from "../error.ts";

export function createUserToken() {
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    let str = "";
    for (let i = 0; i < 99; i++) {
        str += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    return str;
}


export function hashPassword(password: string): string {
    return bcrypt.hashSync(password);
}

export function comparePassword(password: string, passwordHash: string): boolean {
    return bcrypt.compareSync(password, passwordHash);
}


export type SecureRouteHandler = (req: Request, res: Response, username: string, token: string) => Promise<void> | void;

export async function resolveUserName(token: string, connection: mysql.Connection): Promise<string | undefined> {
    const [rows, _fields] = await connection.execute("select username from `user_sessions` where token = ?", [ token ]) as [mysql.RowDataPacket[], mysql.FieldPacket[]];
    if (rows.length == 0) {
        return undefined;
    } else {
        return rows[0].username;
    }
}

export function secureRoute(connection: mysql.Connection, path: string, method: "GET" | "POST", handler: SecureRouteHandler): Route {
    console.log(`Creating secure route ${method} ${path}`);

    async function handle(req: Request, res: Response): Promise<void> {
        const token = req.query.token;
        if (token == null || typeof(token) != "string") {
            sendGenericError(res, "Missing or invalid token!");
            return;
        }

        const username = await resolveUserName(token, connection);
        if (username == undefined) {
            sendGenericError(res, "Invalid token!", token);
            return;
        }
        return handler(req, res, username, token);
    }

    return {
        path: path,
        handler: handle,
        method: method
    }
}