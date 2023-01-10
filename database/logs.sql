DROP TABLE IF EXISTS logs;
CREATE TABLE logs(
  hash varchar(40) NOT NULL,
  ip_address INT UNSIGNED DEFAULT NULL,
  user_agent TEXT,
  view_date DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  image_id INT UNSIGNED NOT NULL,
  views_count INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
