package gq.glowman554.crawler;

import gq.glowman554.crawler.constrain.ConstrainManager;
import gq.glowman554.crawler.constrain.impl.*;
import gq.glowman554.crawler.utils.FileUtils;

import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;

public class Validator {
    private static final ArrayList<String> allowedExtensions = new ArrayList<>();
    static {
        allowedExtensions.add("html");
        allowedExtensions.add("php");
    }

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

        if (!(allowedExtensions.contains(FileUtils.getFileExtension(url.getPath())) || FileUtils.getFileExtension(url.getPath()).isEmpty())) {
            return null;
        }

        String finalLink = url.getProtocol() + "://" + url.getHost();
        if (url.getPort() != -1) {
            finalLink += ":" + url.getPort();
        }
        finalLink += url.getPath();
        if (!finalLink.endsWith("/") && !finalLink.contains(".")) {
            finalLink += "/";
        }

        return finalLink;
    }
}
