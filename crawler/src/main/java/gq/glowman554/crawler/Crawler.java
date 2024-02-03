package gq.glowman554.crawler;

import gq.glowman554.crawler.robots.RobotParser;
import gq.glowman554.crawler.robots.RobotResult;
import gq.glowman554.crawler.robots.RobotScope;
import gq.glowman554.crawler.utils.HttpClient;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.IOException;
import java.net.URL;
import java.util.HashMap;

public class Crawler {
    private static final HashMap<String, RobotResult> robotsCache = new HashMap<>();

    private static String get(String url) throws IOException {
        boolean proxy = url.contains(".onion") || "true".equals(System.getenv("PROXY_FORCE"));
        return HttpClient.get(url, proxy);
    }

    private static boolean processSitemap(String sitemapUrl) {
        try {
            LinkQueue queue = Main.getLinkQueue();

            if (queue == null) {
                return true;
            }
            String sitemap = get(sitemapUrl);
            Document jsoup = Jsoup.parse(sitemap);

            for (Element url : jsoup.getElementsByTag("url")) {
                Elements child = url.getElementsByTag("loc");
                if (child.size() == 1) {
                    String site = child.get(0).text().trim().split("#")[0];
                    System.out.println("[SITEMAP] discovered " + site);
                    queue.insert(site);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
            return true;
        }
        return false;
    }

    public static CrawlerStatus crawl(String link) throws IOException {
        if (robotsCache.size() > 500) {
            robotsCache.clear();
        }

        URL url = new URL(link);
        RobotResult robots = null;
        String robotsCacheKey = url.getHost() + ":" + url.getPort();
        if (robotsCache.containsKey(robotsCacheKey)) {
            robots = robotsCache.get(robotsCacheKey);
        } else {
            try {
                String robotsString = get(RobotParser.link(url));
                robots = RobotParser.parse(robotsString, url.getHost());
            } catch (IOException ignored) {
            }
            robotsCache.put(robotsCacheKey, robots);

            if (robots != null) {
                for (String sitemap : robots.getSitemaps()) {
                    if (processSitemap(sitemap)) {
                        robots.invalidateSitemaps();
                        break;
                    }
                }
            }
        }

        if (robots != null) {
            RobotScope scope = robots.getApplyingScope(HttpClient.getUserAgent());
            if (scope != null) {
                if (!scope.shouldCrawl(link)) {
                    return CrawlerStatus.ROBOTS_REJECT;
                }
            }
        }

        Document doc = Jsoup.parse(get(link), link);

        if (!(robots != null && !robots.getSitemaps().isEmpty())) {
            Elements links = doc.getElementsByTag("a");

            LinkQueue queue = Main.getLinkQueue();

            if (queue != null) {
                for (Element element : links) {
                    String link_s = element.absUrl("href");
                    queue.insert(link_s.split("#")[0]);
                }
            }
        }

        Elements titles = doc.getElementsByTag("title");
        String title = "none";
        if (!titles.isEmpty()) {
            title = titles.get(0).text();
        }


        String description = null;
        String keywords = null;
        Elements metas = doc.getElementsByTag("meta");
        for (Element meta : metas) {
            if (meta.attr("name").equals("description") || meta.attr("property").equals("og:description")) {
                description = meta.attr("content");
            } else if (meta.attr("name").equals("keywords")) {
                keywords = meta.attr("content");
            }
        }

        StringBuilder shortText = new StringBuilder();
        Elements shortTextElements = doc.getElementsByTag("article");
        if (shortTextElements.isEmpty()) {
            shortTextElements = doc.getElementsByTag("p");
        }
        for (Element shortTextElement : shortTextElements) {
            shortText.append(shortTextElement.text()).append(" ");
            if (shortText.length() > 175) {
                break;
            }
        }

        shortText = new StringBuilder(shortText.substring(0, Math.min(shortText.length(), 175)) + "...");

        if (Main.getDatabaseConnection().isCrawled(link)) {
            Main.getDatabaseConnection().updatePage(link, title, doc.text(), description, keywords, shortText.toString());
            return CrawlerStatus.UPDATED;
        } else {
            Main.getDatabaseConnection().insertPage(link, title, doc.text(), description, keywords, shortText.toString());
            return CrawlerStatus.INSERTED;
        }
    }

    public static HashMap<String, RobotResult> getRobotsCache() {
        return robotsCache;
    }

    public enum CrawlerStatus {
        INSERTED,
        UPDATED,
        ROBOTS_REJECT
    }
}
