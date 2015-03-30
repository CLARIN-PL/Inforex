CREATE TABLE IF NOT EXISTS `wccl_rules` (
  `wccl_rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `rules` text COLLATE utf8_polish_ci,
  PRIMARY KEY (`wccl_rule_id`),
  UNIQUE KEY `user_id` (`user_id`,`corpus_id`),
  KEY `user_id_2` (`user_id`),
  KEY `corpus_id` (`corpus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;

ALTER TABLE `wccl_rules`
  ADD CONSTRAINT `wccl_rules_ibfk_2` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wccl_rules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
