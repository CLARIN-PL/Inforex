CREATE TABLE IF NOT EXISTS `exports` (
  `export_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `corpus_id` int(20) NOT NULL,
  `datetime_submit` datetime NOT NULL,
  `datetime_start` datetime DEFAULT NULL,
  `datetime_finish` datetime DEFAULT NULL,
  `status` enum('new','process','done','error') COLLATE utf8_polish_ci NOT NULL DEFAULT 'new',
  `description` text COLLATE utf8_polish_ci NOT NULL,
  `selectors` text COLLATE utf8_polish_ci NOT NULL,
  `extractors` text COLLATE utf8_polish_ci NOT NULL,
  `indices` text COLLATE utf8_polish_ci NOT NULL,
  `message` text COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`export_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci COMMENT='Tabela z historią zadań eksportu korpusów' AUTO_INCREMENT=1 ;

ALTER TABLE `exports`
  ADD CONSTRAINT `exports_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO  `corpus_roles` (
`role` ,
`description` ,
`description_long`
)
VALUES (
'export',  'Eksport korpusu',  'Umożliwia wyeksportowanie określonych dokumentów i danych do postaci plików.'
);