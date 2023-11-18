
/**
 * @param {string} path 
 * @param {string[]} args 
 * @param {boolean} requireToken 
 * @param {string | undefined} token 
 * @returns {Promise<object>}
 */
export async function apiCall(path, args, requireToken = false, token = undefined) {
    let url = path;
    let initial = true;
    for (const arg of args) {
        if (initial) {
            url += "?" + arg;
            initial = false;
        } else {
            url += "&" + arg;
        }
    }


    if (token == undefined && requireToken) {
        throw new Error("Token not found!");
    } else if (token) {
        if (initial) {
            url += "?" + "token=" + token;
        } else {
            url += "&" + "token=" + token;
        }
    }

    if (localStorage.getItem("debug") == "true") {
        console.log("[API] " + url);
    }

    const result = await fetch(url);
    if (result.status != 200) {
        const text = await result.text();
        console.log(text);
        throw new Error("Api call failed!");
    } else {
        return await result.json();
    }
}