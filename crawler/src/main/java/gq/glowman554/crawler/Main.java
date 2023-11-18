package gq.glowman554.crawler;

import java.io.IOException;
import java.sql.SQLException;

import gq.glowman554.crawler.utils.ThreadHelper;

public class Main {

	private static LinkQueue linkQueue;
	private static DatabaseConnection databaseConnection;

	private static String db_url = System.getenv("DB_URL");
	private static String db_user = System.getenv("DB_USER");
	private static String db_password = System.getenv("DB_PASSWORD");
	private static String db = System.getenv("DB");

	private static int threads = Integer.parseInt(System.getenv("THREADS"));


	public static void main(String[] args) throws ClassNotFoundException, SQLException, IOException, IllegalArgumentException, IllegalAccessException {
		linkQueue = new LinkQueue();

		for (String page : System.getenv("INITIAL").split(",")) {
			linkQueue.insert(page);
		}

		databaseConnection = new DatabaseConnection(db_url, db_user, db_password, db);

		run();
	}


	public static void run() throws ClassNotFoundException, SQLException, IOException {

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
			System.out.println(String.format("queue size: %d", linkQueue.len()));

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
