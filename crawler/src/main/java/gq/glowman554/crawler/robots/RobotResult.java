package gq.glowman554.crawler.robots;

import java.util.ArrayList;
import java.util.HashMap;

public class RobotResult {
    private final ArrayList<String> sitemaps;
    private final HashMap<String, RobotScope> scopes;

    public RobotResult(ArrayList<String> sitemaps, HashMap<String, RobotScope> scopes) {
        this.sitemaps = sitemaps;
        this.scopes = scopes;
    }

    public ArrayList<String> getSitemaps() {
        return sitemaps;
    }

    public RobotScope getApplyingScope(String useragent) {
        RobotScope matchingScope = null;
        RobotScope defaultScope = null;

        for (String agent : scopes.keySet()) {
            if (useragent.toLowerCase().contains(agent.toLowerCase())) {
                RobotScope scope = scopes.get(agent);

                if (matchingScope == null || scope.getPath().length() > matchingScope.getPath().length()) {
                    matchingScope = scope;
                }
            }

            if (agent.equals("*")) {
                defaultScope = scopes.get(agent);
            }
        }

        return matchingScope == null ? defaultScope : matchingScope;
    }

    public HashMap<String, RobotScope> getScopes() {
        return scopes;
    }

    public void invalidateSitemaps() {
        sitemaps.clear();
    }
}
