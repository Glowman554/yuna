package gq.glowman554.crawler;

import gq.glowman554.crawler.api.CrawlEndpoint;
import gq.glowman554.crawler.utils.ThreadHelper;
import spark.Spark;

import java.io.IOException;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.sql.SQLException;

public class Main {

    private static final String db_url = System.getenv("DB_URL");
    private static final String db_user = System.getenv("DB_USER");
    private static final String db_password = System.getenv("DB_PASSWORD");
    private static final String db = System.getenv("DB");
    private static LinkQueue linkQueue;
    private static DatabaseConnection databaseConnection;

    public static void main(String[] args) throws ClassNotFoundException, SQLException, IOException, IllegalArgumentException, IllegalAccessException {
        databaseConnection = new DatabaseConnection(db_url, db_user, db_password, db);

        if (System.getenv("PORT") != null) {
            runServer();
        } else {
            runStandalone();
        }
    }

    public static void runServer() {
        Spark.exception(Exception.class, (e, request, response) -> {
            e.printStackTrace();
            StringWriter sw = new StringWriter();
            PrintWriter pw = new PrintWriter(sw, true);

            e.printStackTrace(pw);

            response.status(500);
            response.body("<h1>Internal server error!</h1>\n<span style=\"white-space: pre-line\"><code>" + sw.getBuffer().toString() + "</code></span>");
        });

        Spark.port(Integer.parseInt(System.getenv("PORT")));

        Spark.get("/api/crawl", new CrawlEndpoint());
    }

    public static void runStandalone() throws ClassNotFoundException, SQLException, IOException {
        linkQueue = new LinkQueue();

        for (String page : System.getenv("INITIAL").split(",")) {
            linkQueue.insert(page);
        }

        int threads = Integer.parseInt(System.getenv("THREADS"));

        new ThreadHelper(threads, () -> {
            while (true) {
                try {
                    String link = linkQueue.fetch();
                    if (!databaseConnection.isCrawled(link) || linkQueue.len() == 0) {
                        Crawler.crawl(link);
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                    try {
                        Thread.sleep(1000);
                    } catch (InterruptedException e1) {
                    }
                }
            }
        }).start();


        while (true) {
            System.out.printf("queue size: %d%n", linkQueue.len());

            if (linkQueue.len() == 0) {
                linkQueue.insert(databaseConnection.fetchRandomSite());
            }

            try {
                Thread.sleep(1000);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }

    public static LinkQueue getLinkQueue() {
        return linkQueue;
    }

    public static DatabaseConnection getDatabaseConnection() {
        return databaseConnection;
    }
}
