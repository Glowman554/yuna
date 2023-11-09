// @deno-types="npm:@types/express@4.17.15"
import { Request, Response, NextFunction } from 'npm:express@4.18.2';


export function loggingLayer(req: Request, res: Response, next: NextFunction) {
    console.log(`[${new Date().toLocaleString()}] ${req.method} ${req.path}`);
    next();
}