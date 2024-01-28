package gq.glowman554.crawler;

import gq.glowman554.crawler.constrain.ConstrainManager;
import gq.glowman554.crawler.constrain.impl.*;
import gq.glowman554.crawler.utils.FileUtils;

import java.net.MalformedURLException;
import java.net.URL;

public class Validator {
    private static final ConstrainManager<String> validator = new ConstrainManager<>();

    static {
        validator.add(new WikipediaConstrain());
        validator.add(new WiktionaryConstrain());
        validator.add(new WikisourceConstrain());
        validator.add(new GithubOnlyMainRepo());
        validator.add(new GithubUserContentConstrain());
        validator.add(new UnsplashConstrain());
        validator.add(new OnionConstrain());
    }

    public static ConstrainManager<String> getValidator() {
        return validator;
    }

    public static String rebuildLink(String link) throws MalformedURLException {
        URL url = new URL(link);

        if (!(FileUtils.getFileExtension(url.getPath()).equals("html") || FileUtils.getFileExtension(url.getPath()).isEmpty())) {
            return null;
        }

        String finalLink = url.getProtocol() + "://" + url.getHost() + url.getPath();
        if (!finalLink.endsWith("/") && !finalLink.contains(".")) {
            finalLink += "/";
        }

        return finalLink;
    }
}
