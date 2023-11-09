package gq.glowman554.crawler;

import java.io.IOException;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.select.Elements;

import gq.glowman554.crawler.utils.HttpClient;

public class Crawler {
	public static void crawl(String link) throws IOException {
		Document doc = Jsoup.parse(HttpClient.get(link), link);

		Elements links = doc.getElementsByTag("a");

		for (int i = 0; i < links.size(); i++) {
			String link_s = links.get(i).absUrl("href");
			Main.getLinkQueue().insert(link_s.split("#")[0]);
		}

		Elements titles = doc.getElementsByTag("title");
		String title = "none";
		if (titles.size() != 0) {
			title = titles.get(0).text();
		}

		Main.getDatabaseConnection().insertPage(link, title, doc.html());
	}
}
