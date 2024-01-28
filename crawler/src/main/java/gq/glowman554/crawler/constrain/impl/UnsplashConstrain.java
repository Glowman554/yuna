package gq.glowman554.crawler.constrain.impl;

import gq.glowman554.crawler.constrain.Constrain;

public class UnsplashConstrain implements Constrain<String> {

    @Override
    public boolean compute(String input) {
        return input.contains("unsplash") && input.contains("download");
    }

}
