package gq.glowman554.crawler.utils;

import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;

import java.io.IOException;
import java.net.InetSocketAddress;
import java.net.Proxy;
import java.util.HashMap;
import java.util.Map;

public class HttpClient {
    private static final String proxy_port = System.getenv("PROXY_PORT");
    private static final String proxy_host = System.getenv("PROXY_HOST");


    public static String get(String _url, Map<String, String> headers, boolean proxy) throws IOException {

        OkHttpClient client;
        if (proxy) {
            InetSocketAddress address = new InetSocketAddress(proxy_host, Integer.parseInt(proxy_port));
            client = new OkHttpClient.Builder().proxy(new Proxy(Proxy.Type.SOCKS, address)).build();
        } else {
            client = new OkHttpClient();
        }

        var req = new Request.Builder();

        req.url(_url);

        req.addHeader("User-Agent", "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11");

        for (String key : headers.keySet()) {
            req.addHeader(key, headers.get(key));
        }

        try (Response res = client.newCall(req.build()).execute()) {

            assert res.body() != null;
            return res.body().string();
        }

    }

    public static String get(String _url) throws IOException {
        return get(_url, new HashMap<>(), false);
    }

    public static String get(String _url, boolean proxy) throws IOException {
        return get(_url, new HashMap<>(), proxy);
    }
}
