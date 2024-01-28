package gq.glowman554.crawler.utils;

public class ThreadHelper {

    private final Thread[] threads;

    public ThreadHelper(int num_threads, ThreadHelperLambda l) {
        threads = new Thread[num_threads];

        for (int i = 0; i < threads.length; i++) {
            threads[i] = new Thread(l::run);
        }
    }

    public void start() {
        for (Thread thread : threads) {
            thread.start();
        }

    }

    public ThreadHelper join() {
        for (Thread thread : threads) {
            try {
                thread.join();
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }

        return this;
    }

    public interface ThreadHelperLambda {
        void run();
    }
}
