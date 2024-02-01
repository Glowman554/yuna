package gq.glowman554.crawler.robots;

import java.util.ArrayList;
import java.util.HashMap;

public class RobotParser {
    public static RobotResult parse(String robots, String host) {
        ArrayList<String> sitemaps = new ArrayList<>();
        HashMap<String, RobotScope> scopes = new HashMap<>();
        String currentScope = "*";

        for (String entry : robots.split("\\n")) {
            entry = entry.trim();
            if (entry.isEmpty()) {
                continue;
            }

            String[] split = entry.split (":", 2);
            String instruction = split[0].toLowerCase();
            switch (instruction) {
                case "sitemap":
                    sitemaps.add(split[1].trim());
                    break;

                case "user-agent":
                    currentScope = split[1].trim();
                    break;

                case "disallow":
                case "allow":
                    if (!scopes.containsKey(currentScope)) {
                        scopes.put(currentScope, new RobotScope(currentScope));
                    }
                    RobotScope scope = scopes.get(currentScope);

                    if (instruction.equals("allow")) {
                        scope.getAllows().add(split[1].trim());
                    } else {
                        scope.getDisallows().add(split[1].trim());
                    }
                    break;
                default:
                    if (instruction.startsWith("#")) {
                        break;
                    }
                    System.out.println("WARNING[" + host + "]: unknown robots.txt instruction " + instruction);
                    break;
            }
        }

        return new RobotResult(sitemaps, scopes);
    }
}
