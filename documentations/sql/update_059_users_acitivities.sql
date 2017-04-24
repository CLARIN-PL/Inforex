CREATE TABLE IF NOT EXISTS `ips` (
  `ip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(39) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ip_id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;

CREATE TABLE IF NOT EXISTS `activity_types` (
  `activity_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(16) COLLATE utf8_bin NOT NULL,
  `name` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  KEY `activity_type_id` (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `activities` (
  `activity_page_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `ip_id` int(10) unsigned NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `corpus_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `activity_type_id` int(11) NOT NULL,
  `execution_time` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`activity_page_id`),
  KEY `ip_id` (`ip_id`),
  KEY `user_id` (`user_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `report_id` (`report_id`),
  KEY `activity_type_id` (`activity_type_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE  VIEW `activities_view_users` AS select `u`.`screename` AS `screename`,count(0) AS `COUNT(*)`,max(`a`.`datetime`) AS `last_datetime`,`a`.`activity_id` AS `activity_id`,`a`.`datetime` AS `datetime`,`a`.`ip_id` AS `ip_id`,`a`.`user_id` AS `user_id`,`a`.`corpus_id` AS `corpus_id`,`a`.`report_id` AS `report_id`,`a`.`activity_type_id` AS `activity_type_id`,`a`.`execution_time` AS `execution_time` from (`activities` `a` join `users` `u` on((`a`.`user_id` = `u`.`user_id`))) group by `a`.`user_id`;
