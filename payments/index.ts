// @deno-types="npm:@types/express@4.17.15"
import express from "npm:express@4.18.2";
import { PaymentManager } from "./paymentManager.ts";

const paymentManager = new PaymentManager();
const app = express();

app.get("/api/begin/", (_req, res) => {
    res.send(paymentManager.begin());
});

app.get("/api/poll/:id", (req, res) => {
    res.send(paymentManager.poll(req.params.id));
});

app.listen(80, () => {
    console.log("Payment processor started!");
});