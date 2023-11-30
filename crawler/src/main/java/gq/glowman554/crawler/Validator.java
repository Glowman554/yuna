package gq.glowman554.crawler;

import java.net.MalformedURLException;
import java.net.URL;

import gq.glowman554.crawler.constrain.ConstrainManager;
import gq.glowman554.crawler.constrain.impl.GithubOnlyMainRepo;
import gq.glowman554.crawler.constrain.impl.WikipediaConstrain;
import gq.glowman554.crawler.constrain.impl.WikisourceConstrain;
import gq.glowman554.crawler.constrain.impl.WiktionaryConstrain;
import gq.glowman554.crawler.utils.FileUtils;

public class Validator {
    private static ConstrainManager<String> validator = new ConstrainManager<>();

    static {
        validator.add(new WikipediaConstrain());
		validator.add(new WiktionaryConstrain());
		validator.add(new WikisourceConstrain());
		validator.add(new GithubOnlyMainRepo());
    }

    public static ConstrainManager<String> getValidator() {
        return validator;
    }

    public static String rebuildLink(String link) throws MalformedURLException {
        URL url = new URL(link);
			
		if (!(FileUtils.getFileExtension(url.getPath()).equals("html") || FileUtils.getFileExtension(url.getPath()).equals(""))) {
			return null;
		}

		String finalLink = url.getProtocol() + "://" + url.getHost() + url.getPath();
        if (!finalLink.endsWith("/")) {
            finalLink += "/";
        }

        return finalLink;
    }
}
