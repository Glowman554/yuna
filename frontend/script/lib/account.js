import { apiCall } from "./api.js";

/**
 * @returns {Promise<string | undefined>}
 */
export async function validateToken() {
    const token = localStorage.getItem("token");
    if (token) {
        try {
            const result = await apiCall("/api/user/info", [], true, token);
            console.log(result);
        } catch (_e) {
            localStorage.removeItem("token");
            return undefined;
        }
        return token;
    } else {
        return undefined;
    }
}

