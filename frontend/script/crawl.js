import { validateToken } from "./lib/account.js";
import { apiCall } from "./lib/api.js";

const url = document.getElementById("url");

const crawl = document.getElementById("crawl");


const token = await validateToken();
if (!token) {
    location.href = "/login.html";
}

crawl.onclick = async () => {
    apiCall("/api/crawl", [
        "url=" + url.value
    ], true, token).then(res => {
        alert(res.status);
    }).catch(reason => {
       alert(reason); 
    });
}