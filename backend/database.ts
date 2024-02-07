import mysql from "npm:mysql2@3.6.2/promise";

const databaseSetup = `
CREATE TABLE IF NOT EXISTS \`users\` (
  \`username\` varchar(100) NOT NULL,
  \`password_hash\` varchar(100) DEFAULT NULL,
  \`admin\` tinyint(1) DEFAULT NULL,
  \`premium\` tinyint(1) DEFAULT NULL,
  \`keep_history\` tinyint(1) DEFAULT '1',
  \`pfp_url\` text,
  PRIMARY KEY (\`username\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS \`sites\` (
  \`site_id\` int NOT NULL AUTO_INCREMENT,
  \`link\` text,
  \`title\` text,
  \`text\` LONGTEXT,
  \`description\` text,
  \`keywords\` text,
  \`shortText\` text,
  \`added\` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`site_id\`),
  FULLTEXT KEY \`title\` (\`title\`,\`link\`,\`text\`,\`keywords\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS \`click_history\` (
  \`id\` int NOT NULL AUTO_INCREMENT,
  \`username\` varchar(100) NOT NULL,
  \`site_id\` int NOT NULL,
  \`time\` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`click_history_FK\` (\`username\`),
  KEY \`click_history_FK_1\` (\`site_id\`),
  CONSTRAINT \`click_history_FK\` FOREIGN KEY (\`username\`) REFERENCES \`users\` (\`username\`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT \`click_history_FK_1\` FOREIGN KEY (\`site_id\`) REFERENCES \`sites\` (\`site_id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS \`search_history\` (
  \`id\` int NOT NULL AUTO_INCREMENT,
  \`username\` varchar(100) NOT NULL,
  \`search\` varchar(100) DEFAULT NULL,
  \`time\` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`search_history_FK\` (\`username\`),
  CONSTRAINT \`search_history_FK\` FOREIGN KEY (\`username\`) REFERENCES \`users\` (\`username\`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS \`user_sessions\` (
  \`username\` varchar(100) DEFAULT NULL,
  \`token\` varchar(100) NOT NULL,
  \`timestamp\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`token\`),
  KEY \`user_sessions_FK\` (\`username\`),
  CONSTRAINT \`user_sessions_FK\` FOREIGN KEY (\`username\`) REFERENCES \`users\` (\`username\`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS \`crawl_requests\` (
  \`id\` int NOT NULL AUTO_INCREMENT,
  \`username\` varchar(100) DEFAULT NULL,
  \`link\` text NOT NULL,
  \`status\` varchar(100) NOT NULL,
  PRIMARY KEY(\`id\`),
  CONSTRAINT \`crawl_requests_FK\` FOREIGN KEY (\`username\`) REFERENCES \`users\` (\`username\`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS \`background_history\` (
	id int auto_increment NOT NULL,
	url text NULL,
	\`time\` DATETIME DEFAULT CURRENT_TIMESTAMP NULL,
	CONSTRAINT backgound_history_PK PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS \`payments\` (
  \`id\` int NOT NULL AUTO_INCREMENT,
  \`method\` varchar(100) NOT NULL,
  \`amount\` float DEFAULT NULL,
  \`username\` varchar(100) NOT NULL,
  PRIMARY KEY (\`id\`),
  KEY \`payments_users_FK\` (\`username\`),
  CONSTRAINT \`payments_users_FK\` FOREIGN KEY (\`username\`) REFERENCES \`users\` (\`username\`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
`;

export async function setup(connection: mysql.Connection) {
    for (const command of databaseSetup.split(";")) {
        if (command.trim() == "") {
            continue;
        }
        await connection.execute(command);
    }
}