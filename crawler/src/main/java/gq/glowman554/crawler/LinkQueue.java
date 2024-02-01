package gq.glowman554.crawler;

import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.Collections;

public class LinkQueue {
    private final ArrayList<String> current_links = new ArrayList<>();

    public String fetch() {
        String next;
        synchronized (current_links) {
            if (current_links.isEmpty()) {
                throw new IllegalStateException("Link list size == 0");
            }

            next = current_links.get(0);
            current_links.remove(0);
        }

        return next;
    }

    public int len() {
        synchronized (current_links) {
            return current_links.size();
        }
    }

    public void insert(String link) {
        if (Validator.getValidator().compute(link)) {
            return;
        }

        if (link.isEmpty() || !(link.startsWith("https://") || link.startsWith("http://"))) {
            // System.out.println("Dropping: " + link);
            return;
        }

        try {
            link = Validator.rebuildLink(link);
            if (link == null) {
                return;
            }
        } catch (MalformedURLException e1) {
            e1.printStackTrace();
            return;
        }


        synchronized (current_links) {
            if (current_links.size() >= 100000) {
                // System.out.println("queue size limint reached. dropping: " + link);
                return;
            }

            if (current_links.contains(link)) {
                // System.out.println("Not inserting duplicate string: " + link);
                return;
            }

            current_links.add(link);
        }
    }

    public void shuffle() {
        synchronized (current_links) {
            Collections.shuffle(current_links);
        }
    }
}
