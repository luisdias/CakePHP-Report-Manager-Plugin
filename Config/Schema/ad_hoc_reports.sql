
CREATE TABLE ad_hoc_reports (
  id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(100) DEFAULT '',
  options TEXT NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY  (id)
);