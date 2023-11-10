// @deno-types="npm:@types/express@4.17.15"
import { Response } from 'npm:express@4.18.2';

interface ErrorResponse {
    error: string;
    // deno-lint-ignore no-explicit-any
    invalidValue?: any;
}

// deno-lint-ignore no-explicit-any
export function sendGenericError(res: Response, message: string, invalidValue: any = undefined) {
    const error: ErrorResponse = {
        error: message
    };

    if (invalidValue) {
        error.invalidValue = invalidValue;
    }

    res.status(400).send(error);
}