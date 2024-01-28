package gq.glowman554.crawler.api;

import gq.glowman554.crawler.Crawler;
import gq.glowman554.crawler.Validator;
import spark.Request;
import spark.Response;
import spark.Route;

public class CrawlEndpoint implements Route {

    @Override
    public Object handle(Request req, Response res) throws Exception {
        String url = req.queryParams("url");

        if (url == null) {
            throw new Exception("Expected url parameter");
        }

        if (Validator.getValidator().compute(url)) {
            return "REJECTED VALIDATOR";
        } else {
            url = Validator.rebuildLink(url);
            if (url == null) {
                return "REJECTED LINK";
            } else {
                return Crawler.crawl(url).toString();
            }
        }
    }

}
