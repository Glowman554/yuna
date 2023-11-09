// @deno-types="npm:@types/express@4.17.15"
import { Request, Response } from 'npm:express@4.18.2';

export interface Route {
    path: string;
    handler: (req: Request, res: Response) => Promise<void> | void;
    method: "GET" | "POST"; 
}