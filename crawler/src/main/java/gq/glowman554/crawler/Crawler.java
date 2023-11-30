package gq.glowman554.crawler;

import java.io.IOException;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import gq.glowman554.crawler.utils.HttpClient;

public class Crawler {

	public static CrawlerStatus crawl(String link) throws IOException {
		Document doc = Jsoup.parse(HttpClient.get(link), link);

		Elements links = doc.getElementsByTag("a");

		LinkQueue queue = Main.getLinkQueue();

		if (queue != null) {
			for (int i = 0; i < links.size(); i++) {
				String link_s = links.get(i).absUrl("href");
				queue.insert(link_s.split("#")[0]);
			}
		}

		Elements titles = doc.getElementsByTag("title");
		String title = "none";
		if (titles.size() != 0) {
			title = titles.get(0).text();
		}


		String description = null;
		String keywords = null;
		Elements metas = doc.getElementsByTag("meta");
		for (int i = 0; i < metas.size(); i++) {
			Element meta = metas.get(i);
			if (meta.attr("name").equals("description") || meta.attr("property").equals("og:description")) {
				description = meta.attr("content");
			} else if (meta.attr("name").equals("keywords")) {
				keywords = meta.attr("content");
			}
		}

		String shortText = "";
		Elements shortTextElements = doc.getElementsByTag("article");
		if (shortTextElements.size() == 0) {
			shortTextElements = doc.getElementsByTag("p");
		}
		for (int i = 0; i < shortTextElements.size(); i++) {
			shortText += shortTextElements.get(i).text() + " ";
			if (shortText.length() > 175) {
				break;
			}
		}

		shortText = shortText.substring(0, Math.min(shortText.length(), 175)) + "...";

		if (Main.getDatabaseConnection().isCrawled(link)) {
			Main.getDatabaseConnection().updatePage(link, title, doc.text(), description, keywords, shortText);
			return CrawlerStatus.UPDATED;
		} else {
			Main.getDatabaseConnection().insertPage(link, title, doc.text(), description, keywords, shortText);
			return CrawlerStatus.INSERTED;
		}
	}

	public enum CrawlerStatus {
		INSERTED,
		UPDATED
	}
}
