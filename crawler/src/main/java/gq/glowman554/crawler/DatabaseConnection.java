package gq.glowman554.crawler;

import gq.glowman554.crawler.utils.FileUtils;

import java.io.IOException;
import java.sql.*;

public class DatabaseConnection {
    private Connection connect = null;

    public DatabaseConnection(String url, String username, String password, String db) throws ClassNotFoundException, SQLException, IOException {
        connect = DriverManager.getConnection(String.format("jdbc:mysql://%s/%s?user=%s&password=%s", url, db, username, password));
        execute_script("database_setup");
    }

    public void execute_script(String script_name) throws SQLException, IOException {
        Statement s = connect.createStatement();
        String[] sql_commands = FileUtils.readFile(this.getClass().getResourceAsStream("/sql/" + script_name + ".sql")).split(";");

        for (String sql : sql_commands) {
            sql = sql.trim();

            if (sql.isEmpty()) {
                continue;
            }

            System.out.println("Executing: " + sql);
            s.execute(sql);
        }

        s.close();
    }

    private int linkToId(String link) throws SQLException {
        PreparedStatement s = connect.prepareStatement("SELECT `site_id` FROM `sites` WHERE link = ?");
        s.setString(1, link);

        ResultSet rs = s.executeQuery();

        rs.next();
        int result = rs.getInt("site_id");

        rs.close();
        s.close();

        return result;
    }


    public void insertPage(String url, String title, String text, String description, String keywords, String shortText) {
        System.out.println("Inserting page: " + url);
        try {
            PreparedStatement s = connect.prepareStatement("INSERT IGNORE INTO `sites` (link, title, text, description, keywords, shortText) VALUES (?, ?, ?, ?, ?, ?)");

            s.setString(1, url);
            s.setString(2, title.replace("\n", ""));
            s.setString(3, text.replace("\n", ""));
            s.setString(4, description);
            s.setString(5, keywords);
            s.setString(6, shortText);

            s.executeUpdate();
            s.close();
        } catch (SQLException e1) {
            e1.printStackTrace();
        }
    }

    public void updatePage(String url, String title, String text, String description, String keywords, String shortText) {
        try {
            PreparedStatement s = connect.prepareStatement("UPDATE `sites` SET link = ?, title = ?, text = ?, description = ?, keywords = ?, shortText = ? WHERE site_id = ?");

            s.setString(1, url);
            s.setString(2, title.replace("\n", ""));
            s.setString(3, text.replace("\n", ""));
            s.setString(4, description);
            s.setString(5, keywords);
            s.setString(6, shortText);
            s.setInt(7, linkToId(url));

            s.executeUpdate();
            s.close();
        } catch (SQLException e1) {
            e1.printStackTrace();
        }
    }


    public boolean isCrawled(String link) {
        boolean return_val = false;
        try {
            PreparedStatement s = connect.prepareStatement("SELECT `link` FROM `sites` WHERE `link` = ?");

            s.setString(1, link);

            ResultSet rs = s.executeQuery();

            if (rs.next()) {
                // System.out.println(link + " already in db!");
                return_val = true;
            }

            rs.close();
            s.close();
        } catch (SQLException e1) {
            e1.printStackTrace();
        }

        return return_val;
    }

    public String fetchRandomSite() throws SQLException {
        PreparedStatement s = connect.prepareStatement("SELECT `link` FROM `sites` ORDER BY RAND() LIMIT 1");
        ResultSet rs = s.executeQuery();

        rs.next();
        String result = rs.getString("link");

        rs.close();
        s.close();

        return result;
    }

}
