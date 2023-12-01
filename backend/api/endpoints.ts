import { Route } from "../route.ts";
import { app } from "../index.ts";

interface Results {
    path: string;
    doc: string | undefined;
}

const docs: { [key: string]: string } = {
    "/api/search": "Make a search query. Arguments: q. Optional arguments: token, limit, offset",
    "/api/crawl": "Ask the search engine to crawl the provided website. Arguments: token, url",
    "/api/endpoints": "List all api endpoints"
};

export function endpointsEndpoint(): Route {
    return {
        path: "/api/endpoints",
        method: "GET",
        handler: (_req, res) => {
            const result: Results[] = [];

            for (const layer of app._router.stack) {
                if (layer.route) {
                    const path = layer.route.path as string;
                    result.push({
                        path: path,
                        doc: docs[path]
                    });
                }
            }

            res.send(result);
        }
    }
}