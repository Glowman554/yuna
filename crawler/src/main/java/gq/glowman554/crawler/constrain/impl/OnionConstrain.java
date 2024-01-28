package gq.glowman554.crawler.constrain.impl;

import gq.glowman554.crawler.constrain.Constrain;

public class OnionConstrain implements Constrain<String> {
    private final boolean allow = "true".equals(System.getenv("ALLOW_ONION"));

    @Override
    public boolean compute(String input) {
        if (allow) {
            return false;
        } else {
            return input.contains(".onion");
        }
    }
}
