--
-- Struktura tabeli dla tabeli `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data i czas utworzenia zadania.',
  `type` varchar(32) COLLATE utf8_polish_ci NOT NULL COMMENT 'Identyfikator zadania.',
  `parameters` text COLLATE utf8_polish_ci NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `max_steps` int(11) NOT NULL,
  `current_step` int(11) NOT NULL,
  `status` enum('new','process','done','error') COLLATE utf8_polish_ci NOT NULL DEFAULT 'new',
  PRIMARY KEY (`task_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=44 ;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `tasks_reports` (
  `task_id` int(11) NOT NULL,
  `report_id` bigint(11) NOT NULL,
  `status` enum('new','process','done','error') COLLATE utf8_polish_ci NOT NULL,
  `message` TEXT,
  KEY `document_id` (`report_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


ALTER TABLE `tasks_reports`
  ADD CONSTRAINT `tasks_reports_ibfk_2` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_reports_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;


INSERT INTO `corpus_roles` (`role`, `description`) VALUES ('tasks', 'Wykonywanie masowych zadań na dokumentach');
