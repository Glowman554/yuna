package gq.glowman554.crawler.constrain.impl;

import java.util.ArrayList;

import gq.glowman554.crawler.constrain.Constrain;

public class GithubOnlyMainRepo implements Constrain<String> {

    private ArrayList<String> disallowed = new ArrayList<>();

    public GithubOnlyMainRepo() {
        disallowed.add("/fork"); 
        disallowed.add("/forks");
        disallowed.add("/stargazers");
        disallowed.add("/activity");
        disallowed.add("/issues");
        disallowed.add("/pulls");
        disallowed.add("/actions");
        disallowed.add("/projects");
        disallowed.add("/security");
        disallowed.add("/pulse");
        disallowed.add("/branches");
        disallowed.add("/{{ urlEncodedRefName }}");
        disallowed.add("/tags");
        disallowed.add("/watchers");
        disallowed.add("/releases");
        disallowed.add("/search");
        disallowed.add("/contributors");
    }

    @Override
    public boolean compute(String input) {
        if (input.startsWith("https://github.com")) {
            for (String tmp : disallowed) {
                if (input.endsWith(tmp)) {
                    return true;
                }
            }
        }
        return false;
    }
    
}
