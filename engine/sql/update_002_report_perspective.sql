--
-- Struktura tabeli dla  `corpus_and_report_perspectives`
--

CREATE TABLE IF NOT EXISTS `corpus_and_report_perspectives` (
  `perspective_id` varchar(32) NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `access` enum('public','loggedin','role') NOT NULL,
  PRIMARY KEY (`perspective_id`,`corpus_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `corpus_and_report_perspectives`
--

INSERT INTO `corpus_and_report_perspectives` (`perspective_id`, `corpus_id`, `access`) VALUES
('transcription', 3, 'public'),
('preview', 1, 'public'),
('html', 1, 'public'),
('report', 1, 'public'),
('takipi', 1, 'public'),
('edit', 1, 'loggedin'),
('edit_raw', 1, 'loggedin'),
('annotator', 1, 'loggedin'),
('tei', 1, 'public'),
('preview', 5, 'public'),
('html', 5, 'public'),
('report', 5, 'public'),
('takipi', 5, 'public'),
('edit', 5, 'loggedin'),
('edit_raw', 5, 'loggedin'),
('annotator', 5, 'loggedin'),
('tei', 5, 'public');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `report_perspectives`
--

CREATE TABLE IF NOT EXISTS `report_perspectives` (
  `id` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `report_perspectives`
--

INSERT INTO `report_perspectives` (`id`, `title`, `order`) VALUES
('transcription', 'Transcription', 10),
('preview', 'Text', 20),
('html', 'HTML', 30),
('raw', 'Źródłowy dokument', 40),
('takipi', 'TaKIPI', 50),
('edit', 'Edycja', 60),
('edit_raw', 'Edycja / źródło', 70),
('annotator', 'Anotacja', 80),
('tei', 'TEI', 90);

