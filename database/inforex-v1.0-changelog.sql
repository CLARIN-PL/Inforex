--liquibase formatted sql

--changeset czuk:0 splitStatements:false endDelimiter:#

CREATE PROCEDURE changeFlagStatus(
  IN flag_id INT(11),
  IN flag_status INT(11),
  IN report_id INT(11),
  IN user_id INT(11))
  BEGIN
    -- Previous flag status.
    DECLARE old_status INT(11);

    -- Store the previous flag status into old_status variable.
    SELECT rf.flag_id INTO old_status FROM reports_flags rf
    WHERE (rf.report_id = report_id AND rf.corpora_flag_id = flag_id);

    -- Update the document's flag status.
    REPLACE INTO reports_flags(corpora_flag_id, report_id, flag_id)
    VALUES(flag_id, report_id, flag_status);

    -- Store the change in the flag status history table.
    INSERT INTO flag_status_history (date, report_id, flag_id, user_id, new_status, old_status)
    VALUES (CURRENT_TIMESTAMP, report_id, flag_id, user_id, flag_status, IFNULL(old_status,-1));
  END
#


--changeset czuk:1

ALTER TABLE `reports_users_selection` ADD INDEX(`user_id`);

ALTER TABLE `reports_users_selection` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD INDEX(`report_id`);

ALTER TABLE `reports_users_selection` ADD  FOREIGN KEY (`report_id`) REFERENCES `reports`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD UNIQUE( `user_id`, `report_id`);


--changeset czuk:2

ALTER TABLE `tasks_reports` CHANGE `message` `message` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;


--changeset czuk:3

INSERT INTO `corpus_roles` (`role`, `description`, `description_long`) VALUES
('add_documents', 'Add new documents', ''),
('agreement_check', 'Annotation agreement', ''),
('agreement_morpho', 'Morphology agreement', ''),
('annotate', 'Annotation (final mode)', ''),
('annotate_agreement', 'Annotation (agreement mode)', ''),
('browse_annotations', 'Browse annotations', ''),
('browse_relations', 'Browse annotation relations', ''),
('delete_annotations', 'Delete annotations', ''),
('delete_documents', 'Delete documents', ''),
('edit_documents', 'Edit documents', ''),
('export', 'Export corpus', ''),
('flag_history', 'Flag history', ''),
('manager', 'Corpus management', ''),
('read', 'Corpus access', ''),
('read_limited', 'Restricted access', ''),
('run_tests', 'Tests', ''),
('tasks', 'Tasks', ''),
('wccl_match', 'Wccl Match', '');


--changeset czuk:4

ALTER TABLE `reports_users_selection` ADD INDEX(`user_id`);

ALTER TABLE `reports_users_selection` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD INDEX(`report_id`);

ALTER TABLE `reports_users_selection` ADD  FOREIGN KEY (`report_id`) REFERENCES `reports`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD UNIQUE( `user_id`, `report_id`);


--changeset czuk:5

INSERT INTO `report_perspectives` (`id`, `title`, `description`, `order`) VALUES ('annotation_table', 'Annotation table', 'Display a summary of annotations', '100');


--changeset czuk:6

INSERT INTO `tagsets` (`tagset_id`, `name`) VALUES ('4', 'Hebrew UDPipe'), ('5', 'Russian UDPipe');
INSERT INTO `tagsets` (`tagset_id`, `name`) VALUES ('6', 'Czech UDPipe'), ('7', 'Bulgarian UDPipe');


--changeset czuk:7

--
-- Struktura tabeli dla tabeli `orths`
--

CREATE TABLE `orths` (
  `orth_id` bigint(20) NOT NULL,
  `orth` varchar(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `orths`
--
ALTER TABLE `orths`
  ADD PRIMARY KEY (`orth_id`),
  ADD KEY `orth` (`orth`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `orths`
--
ALTER TABLE `orths`
  MODIFY `orth_id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orths` ADD UNIQUE(`orth`);

ALTER TABLE `tokens` ADD `orth_id` BIGINT NULL AFTER `eos`;


--changeset czuk:8

ALTER TABLE `reports_annotations_shared_attributes` DROP FOREIGN KEY `reports_annotations_shared_attributes_ibfk_4`;

ALTER TABLE `reports_annotations_shared_attributes` CHANGE `value` `value` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE `shared_attributes_enum` CHANGE `value` `value` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE `shared_attributes_enum` CHANGE `description` `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE `reports_annotations_shared_attributes`
  ADD CONSTRAINT `reports_annotations_shared_attributes_ibfk_4` FOREIGN KEY (`value`) REFERENCES `annotation_types_attributes_enum` (`value`) ON DELETE CASCADE ON UPDATE NO ACTION;


--changeset czuk:9

INSERT INTO `report_perspectives` (`id`, `title`, `description`, `order`) VALUES ('annotation_attributes', 'Annotation attributes', 'Batch editor of annotation attributes', '110');


--changeset czuk:10

ALTER TABLE `orths` CHANGE `orth` `orth` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;


--changeset czuk:12 endDelimiter:#

INSERT IGNORE INTO `lang` (`code`, `language`) VALUES
('aar', 'Afar'),
('abk', 'Abkhazian'),
('ace', 'Achinese'),
('ach', 'Acoli'),
('ada', 'Adangme'),
('ady', 'Adyghe; Adygei'),
('afa', 'Afro-Asiatic languages'),
('afh', 'Afrihili'),
('afr', 'Afrikaans'),
('ain', 'Ainu'),
('aka', 'Akan'),
('akk', 'Akkadian'),
('alb', 'Albanian'),
('ale', 'Aleut'),
('alg', 'Algonquian languages'),
('alt', 'Southern Altai'),
('amh', 'Amharic'),
('ang', 'English, Old (ca.450-1100)'),
('anp', 'Angika'),
('apa', 'Apache languages'),
('ara', 'Arabic'),
('arc', 'Official Aramaic (700-300 BCE); Imperial Aramaic (700-300 BCE)'),
('arg', 'Aragonese'),
('arm', 'Armenian'),
('arn', 'Mapudungun; Mapuche'),
('arp', 'Arapaho'),
('art', 'Artificial languages'),
('arw', 'Arawak'),
('asm', 'Assamese'),
('ast', 'Asturian; Bable; Leonese; Asturleonese'),
('ath', 'Athapascan languages'),
('aus', 'Australian languages'),
('ava', 'Avaric'),
('ave', 'Avestan'),
('awa', 'Awadhi'),
('aym', 'Aymara'),
('aze', 'Azerbaijani'),
('bad', 'Banda languages'),
('bai', 'Bamileke languages'),
('bak', 'Bashkir'),
('bal', 'Baluchi'),
('bam', 'Bambara'),
('ban', 'Balinese'),
('baq', 'Basque'),
('bas', 'Basa'),
('bat', 'Baltic languages'),
('bej', 'Beja; Bedawiyet'),
('bel', 'Belarusian'),
('bem', 'Bemba'),
('ben', 'Bengali'),
('ber', 'Berber languages'),
('bho', 'Bhojpuri'),
('bih', 'Bihari languages'),
('bik', 'Bikol'),
('bin', 'Bini; Edo'),
('bis', 'Bislama'),
('bla', 'Siksika'),
('bnt', 'Bantu (Other)'),
('bos', 'Bosnian'),
('bra', 'Braj'),
('bre', 'Breton'),
('btk', 'Batak languages'),
('bua', 'Buriat'),
('bug', 'Buginese'),
('bul', 'Bulgarian'),
('bur', 'Burmese'),
('byn', 'Blin; Bilin'),
('cad', 'Caddo'),
('cai', 'Central American Indian languages'),
('car', 'Galibi Carib'),
('cat', 'Catalan; Valencian'),
('cau', 'Caucasian languages'),
('ceb', 'Cebuano'),
('cel', 'Celtic languages'),
('cha', 'Chamorro'),
('chb', 'Chibcha'),
('che', 'Chechen'),
('chg', 'Chagatai'),
('chi', 'Chinese'),
('chk', 'Chuukese'),
('chm', 'Mari'),
('chn', 'Chinook jargon'),
('cho', 'Choctaw'),
('chp', 'Chipewyan; Dene Suline'),
('chr', 'Cherokee'),
('chu', 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic'),
('chv', 'Chuvash'),
('chy', 'Cheyenne'),
('cmc', 'Chamic languages'),
('cop', 'Coptic'),
('cor', 'Cornish'),
('cos', 'Corsican'),
('cpe', 'Creoles and pidgins, English based'),
('cpf', 'Creoles and pidgins, French-based '),
('cpp', 'Creoles and pidgins, Portuguese-based '),
('cre', 'Cree'),
('crh', 'Crimean Tatar; Crimean Turkish'),
('crp', 'Creoles and pidgins '),
('csb', 'Kashubian'),
('cus', 'Cushitic languages'),
('cze', 'Czech'),
('dak', 'Dakota'),
('dan', 'Danish'),
('dar', 'Dargwa'),
('day', 'Land Dayak languages'),
('del', 'Delaware'),
('den', 'Slave (Athapascan)'),
('dgr', 'Dogrib'),
('din', 'Dinka'),
('div', 'Divehi; Dhivehi; Maldivian'),
('doi', 'Dogri'),
('dra', 'Dravidian languages'),
('dsb', 'Lower Sorbian'),
('dua', 'Duala'),
('dum', 'Dutch, Middle (ca.1050-1350)'),
('dut', 'Dutch; Flemish'),
('dyu', 'Dyula'),
('dzo', 'Dzongkha'),
('efi', 'Efik'),
('egy', 'Egyptian (Ancient)'),
('eka', 'Ekajuk'),
('elx', 'Elamite'),
('eng', 'English'),
('enm', 'English, Middle (1100-1500)'),
('epo', 'Esperanto'),
('est', 'Estonian'),
('ewe', 'Ewe'),
('ewo', 'Ewondo'),
('fan', 'Fang'),
('fao', 'Faroese'),
('fat', 'Fanti'),
('fij', 'Fijian'),
('fil', 'Filipino; Pilipino'),
('fin', 'Finnish'),
('fiu', 'Finno-Ugrian languages'),
('fon', 'Fon'),
('fre', 'French'),
('frm', 'French, Middle (ca.1400-1600)'),
('fro', 'French, Old (842-ca.1400)'),
('frr', 'Northern Frisian'),
('frs', 'Eastern Frisian'),
('fry', 'Western Frisian'),
('ful', 'Fulah'),
('fur', 'Friulian'),
('gaa', 'Ga'),
('gay', 'Gayo'),
('gba', 'Gbaya'),
('gem', 'Germanic languages'),
('geo', 'Georgian'),
('ger', 'German'),
('gez', 'Geez'),
('gil', 'Gilbertese'),
('gla', 'Gaelic; Scottish Gaelic'),
('gle', 'Irish'),
('glg', 'Galician'),
('glv', 'Manx'),
('gmh', 'German, Middle High (ca.1050-1500)'),
('goh', 'German, Old High (ca.750-1050)'),
('gon', 'Gondi'),
('gor', 'Gorontalo'),
('got', 'Gothic'),
('grb', 'Grebo'),
('grc', 'Greek, Ancient (to 1453)'),
('gre', 'Greek, Modern (1453-)'),
('grn', 'Guarani'),
('gsw', 'Swiss German; Alemannic; Alsatian'),
('guj', 'Gujarati'),
('gwi', 'Gwich\'\'in'),
('hai', 'Haida'),
('hat', 'Haitian; Haitian Creole'),
('hau', 'Hausa'),
('haw', 'Hawaiian'),
('heb', 'Hebrew'),
('her', 'Herero'),
('hil', 'Hiligaynon'),
('him', 'Himachali languages; Western Pahari languages'),
('hin', 'Hindi'),
('hit', 'Hittite'),
('hmn', 'Hmong; Mong'),
('hmo', 'Hiri Motu'),
('hrv', 'Croatian'),
('hsb', 'Upper Sorbian'),
('hun', 'Hungarian'),
('hup', 'Hupa'),
('iba', 'Iban'),
('ibo', 'Igbo'),
('ice', 'Icelandic'),
('ido', 'Ido'),
('iii', 'Sichuan Yi; Nuosu'),
('ijo', 'Ijo languages'),
('iku', 'Inuktitut'),
('ile', 'Interlingue; Occidental'),
('ilo', 'Iloko'),
('ina', 'Interlingua (International Auxiliary Language Association)'),
('inc', 'Indic languages'),
('ind', 'Indonesian'),
('ine', 'Indo-European languages'),
('inh', 'Ingush'),
('ipk', 'Inupiaq'),
('ira', 'Iranian languages'),
('iro', 'Iroquoian languages'),
('ita', 'Italian'),
('jav', 'Javanese'),
('jbo', 'Lojban'),
('jpn', 'Japanese'),
('jpr', 'Judeo-Persian'),
('jrb', 'Judeo-Arabic'),
('kaa', 'Kara-Kalpak'),
('kab', 'Kabyle'),
('kac', 'Kachin; Jingpho'),
('kal', 'Kalaallisut; Greenlandic'),
('kam', 'Kamba'),
('kan', 'Kannada'),
('kar', 'Karen languages'),
('kas', 'Kashmiri'),
('kau', 'Kanuri'),
('kaw', 'Kawi'),
('kaz', 'Kazakh'),
('kbd', 'Kabardian'),
('kha', 'Khasi'),
('khi', 'Khoisan languages'),
('khm', 'Central Khmer'),
('kho', 'Khotanese; Sakan'),
('kik', 'Kikuyu; Gikuyu'),
('kin', 'Kinyarwanda'),
('kir', 'Kirghiz; Kyrgyz'),
('kmb', 'Kimbundu'),
('kok', 'Konkani'),
('kom', 'Komi'),
('kon', 'Kongo'),
('kor', 'Korean'),
('kos', 'Kosraean'),
('kpe', 'Kpelle'),
('krc', 'Karachay-Balkar'),
('krl', 'Karelian'),
('kro', 'Kru languages'),
('kru', 'Kurukh'),
('kua', 'Kuanyama; Kwanyama'),
('kum', 'Kumyk'),
('kur', 'Kurdish'),
('kut', 'Kutenai'),
('lad', 'Ladino'),
('lah', 'Lahnda'),
('lam', 'Lamba'),
('lao', 'Lao'),
('lat', 'Latin'),
('lav', 'Latvian'),
('lez', 'Lezghian'),
('lim', 'Limburgan; Limburger; Limburgish'),
('lin', 'Lingala'),
('lit', 'Lithuanian'),
('lol', 'Mongo'),
('loz', 'Lozi'),
('ltz', 'Luxembourgish; Letzeburgesch'),
('lua', 'Luba-Lulua'),
('lub', 'Luba-Katanga'),
('lug', 'Ganda'),
('lui', 'Luiseno'),
('lun', 'Lunda'),
('luo', 'Luo (Kenya and Tanzania)'),
('lus', 'Lushai'),
('mac', 'Macedonian'),
('mad', 'Madurese'),
('mag', 'Magahi'),
('mah', 'Marshallese'),
('mai', 'Maithili'),
('mak', 'Makasar'),
('mal', 'Malayalam'),
('man', 'Mandingo'),
('mao', 'Maori'),
('map', 'Austronesian languages'),
('mar', 'Marathi'),
('mas', 'Masai'),
('may', 'Malay'),
('mdf', 'Moksha'),
('mdr', 'Mandar'),
('men', 'Mende'),
('mga', 'Irish, Middle (900-1200)'),
('mic', 'Mi\'\'kmaq; Micmac'),
('min', 'Minangkabau'),
('mis', 'Uncoded languages'),
('mkh', 'Mon-Khmer languages'),
('mlg', 'Malagasy'),
('mlt', 'Maltese'),
('mnc', 'Manchu'),
('mni', 'Manipuri'),
('mno', 'Manobo languages'),
('moh', 'Mohawk'),
('mon', 'Mongolian'),
('mos', 'Mossi'),
('mul', 'Multiple languages'),
('mun', 'Munda languages'),
('mus', 'Creek'),
('mwl', 'Mirandese'),
('mwr', 'Marwari'),
('myn', 'Mayan languages'),
('myv', 'Erzya'),
('nah', 'Nahuatl languages'),
('nai', 'North American Indian languages'),
('nap', 'Neapolitan'),
('nau', 'Nauru'),
('nav', 'Navajo; Navaho'),
('nbl', 'Ndebele, South; South Ndebele'),
('nde', 'Ndebele, North; North Ndebele'),
('ndo', 'Ndonga'),
('nds', 'Low German; Low Saxon; German, Low; Saxon, Low'),
('nep', 'Nepali'),
('new', 'Nepal Bhasa; Newari'),
('nia', 'Nias'),
('nic', 'Niger-Kordofanian languages'),
('niu', 'Niuean'),
('nno', 'Norwegian Nynorsk; Nynorsk, Norwegian'),
('nob', 'Bokmål, Norwegian; Norwegian Bokmål'),
('nog', 'Nogai'),
('non', 'Norse, Old'),
('nor', 'Norwegian'),
('nqo', 'N\'\'Ko'),
('nso', 'Pedi; Sepedi; Northern Sotho'),
('nub', 'Nubian languages'),
('nwc', 'Classical Newari; Old Newari; Classical Nepal Bhasa'),
('nya', 'Chichewa; Chewa; Nyanja'),
('nym', 'Nyamwezi'),
('nyn', 'Nyankole'),
('nyo', 'Nyoro'),
('nzi', 'Nzima'),
('oci', 'Occitan (post 1500); Provençal'),
('oji', 'Ojibwa'),
('ori', 'Oriya'),
('orm', 'Oromo'),
('osa', 'Osage'),
('oss', 'Ossetian; Ossetic'),
('ota', 'Turkish, Ottoman (1500-1928)'),
('oto', 'Otomian languages'),
('paa', 'Papuan languages'),
('pag', 'Pangasinan'),
('pal', 'Pahlavi'),
('pam', 'Pampanga; Kapampangan'),
('pan', 'Panjabi; Punjabi'),
('pap', 'Papiamento'),
('pau', 'Palauan'),
('peo', 'Persian, Old (ca.600-400 B.C.)'),
('per', 'Persian'),
('phi', 'Philippine languages'),
('phn', 'Phoenician'),
('pli', 'Pali'),
('pol', 'Polish'),
('pon', 'Pohnpeian'),
('por', 'Portuguese'),
('pra', 'Prakrit languages'),
('pro', 'Provençal, Old (to 1500)'),
('pus', 'Pushto; Pashto'),
('que', 'Quechua'),
('raj', 'Rajasthani'),
('rap', 'Rapanui'),
('rar', 'Rarotongan; Cook Islands Maori'),
('roa', 'Romance languages'),
('roh', 'Romansh'),
('rom', 'Romany'),
('rum', 'Romanian; Moldavian; Moldovan'),
('run', 'Rundi'),
('rup', 'Aromanian; Arumanian; Macedo-Romanian'),
('rus', 'Russian'),
('sad', 'Sandawe'),
('sag', 'Sango'),
('sah', 'Yakut'),
('sai', 'South American Indian (Other)'),
('sal', 'Salishan languages'),
('sam', 'Samaritan Aramaic'),
('san', 'Sanskrit'),
('sas', 'Sasak'),
('sat', 'Santali'),
('scn', 'Sicilian'),
('sco', 'Scots'),
('sel', 'Selkup'),
('sem', 'Semitic languages'),
('sga', 'Irish, Old (to 900)'),
('sgn', 'Sign Languages'),
('shn', 'Shan'),
('sid', 'Sidamo'),
('sin', 'Sinhala; Sinhalese'),
('sio', 'Siouan languages'),
('sit', 'Sino-Tibetan languages'),
('sla', 'Slavic languages'),
('slo', 'Slovak'),
('slv', 'Slovenian'),
('sma', 'Southern Sami'),
('sme', 'Northern Sami'),
('smi', 'Sami languages'),
('smj', 'Lule Sami'),
('smn', 'Inari Sami'),
('smo', 'Samoan'),
('sms', 'Skolt Sami'),
('sna', 'Shona'),
('snd', 'Sindhi'),
('snk', 'Soninke'),
('sog', 'Sogdian'),
('som', 'Somali'),
('son', 'Songhai languages'),
('sot', 'Sotho, Southern'),
('spa', 'Spanish; Castilian'),
('srd', 'Sardinian'),
('srn', 'Sranan Tongo'),
('srp', 'Serbian'),
('srr', 'Serer'),
('ssa', 'Nilo-Saharan languages'),
('ssw', 'Swati'),
('suk', 'Sukuma'),
('sun', 'Sundanese'),
('sus', 'Susu'),
('sux', 'Sumerian'),
('swa', 'Swahili'),
('swe', 'Swedish'),
('syc', 'Classical Syriac'),
('syr', 'Syriac'),
('tah', 'Tahitian'),
('tai', 'Tai languages'),
('tam', 'Tamil'),
('tat', 'Tatar'),
('tel', 'Telugu'),
('tem', 'Timne'),
('ter', 'Tereno'),
('tet', 'Tetum'),
('tgk', 'Tajik'),
('tgl', 'Tagalog'),
('tha', 'Thai'),
('tib', 'Tibetan'),
('tig', 'Tigre'),
('tir', 'Tigrinya'),
('tiv', 'Tiv'),
('tkl', 'Tokelau'),
('tlh', 'Klingon; tlhIngan-Hol'),
('tli', 'Tlingit'),
('tmh', 'Tamashek'),
('tog', 'Tonga (Nyasa)'),
('ton', 'Tonga (Tonga Islands)'),
('tpi', 'Tok Pisin'),
('tsi', 'Tsimshian'),
('tsn', 'Tswana'),
('tso', 'Tsonga'),
('tuk', 'Turkmen'),
('tum', 'Tumbuka'),
('tup', 'Tupi languages'),
('tur', 'Turkish'),
('tut', 'Altaic languages'),
('tvl', 'Tuvalu'),
('twi', 'Twi'),
('tyv', 'Tuvinian'),
('udm', 'Udmurt'),
('uga', 'Ugaritic'),
('uig', 'Uighur; Uyghur'),
('ukr', 'Ukrainian'),
('umb', 'Umbundu'),
('und', 'Undetermined'),
('urd', 'Urdu'),
('uzb', 'Uzbek'),
('vai', 'Vai'),
('ven', 'Venda'),
('vie', 'Vietnamese'),
('vol', 'Volapük'),
('vot', 'Votic'),
('wak', 'Wakashan languages'),
('wal', 'Walamo'),
('war', 'Waray'),
('was', 'Washo'),
('wel', 'Welsh'),
('wen', 'Sorbian languages'),
('wln', 'Walloon'),
('wol', 'Wolof'),
('xal', 'Kalmyk; Oirat'),
('xho', 'Xhosa'),
('yao', 'Yao'),
('yap', 'Yapese'),
('yid', 'Yiddish'),
('yor', 'Yoruba'),
('ypk', 'Yupik languages'),
('zap', 'Zapotec'),
('zbl', 'Blissymbols; Blissymbolics; Bliss'),
('zen', 'Zenaga'),
('zha', 'Zhuang; Chuang'),
('znd', 'Zande languages'),
('zul', 'Zulu'),
('zun', 'Zuni'),
('zxx', 'No linguistic content; Not applicable'),
('zza', 'Zaza; Dimili; Dimli; Kirdki; Kirmanjki; Zazaki');
#

--changeset czuk:13

UPDATE `report_perspectives` SET `id` = 'importAnnotations' WHERE `report_perspectives`.`id` = 'importannotations';

--changeset czuk:14

ALTER TABLE `corpora` ADD `css` TEXT NULL AFTER `date_created`;

ALTER TABLE `reports` ADD `deleted` BOOLEAN NOT NULL DEFAULT FALSE AFTER `parent_report_id`, ADD INDEX `reports_deleted_idx` (`deleted`);

--changeset czuk:15

UPDATE `report_perspectives` SET `id` = 'annotator_wsd' WHERE `report_perspectives`.`id` = 'annotatorwsd';

--changeset sw:16

DROP VIEW IF EXISTS `reports_annotations`;
CREATE VIEW `reports_annotations` AS select `ra`.`id` AS `id`,`ra`.`report_id` AS `report_id`,`ra`.`type_id` AS `type_id`,`at`.`name` AS `type`,`at`.`group_id` AS `group`,`ra`.`from` AS `from`,`ra`.`to` AS `to`,`ra`.`text` AS `text`,`ra`.`user_id` AS `user_id`,`ra`.`creation_time` AS `creation_time`,`ra`.`stage` AS `stage`,`ra`.`source` AS `source` from (`inforex`.`reports_annotations_optimized` `ra` left join `inforex`.`annotation_types` `at` on((`at`.`annotation_type_id` = `ra`.`type_id`)));

--changeset sw:17

SET @reports_annotations_shared_attributes_value_fk = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports_annotations_shared_attributes'
      AND COLUMN_NAME = 'value'
      AND REFERENCED_TABLE_NAME IS NOT NULL
    LIMIT 1
);
SET @drop_reports_annotations_shared_attributes_value_fk = IF(
    @reports_annotations_shared_attributes_value_fk IS NULL,
    'SELECT 1',
    CONCAT('ALTER TABLE `reports_annotations_shared_attributes` DROP FOREIGN KEY `', @reports_annotations_shared_attributes_value_fk, '`')
);
PREPARE drop_reports_annotations_shared_attributes_value_fk_stmt FROM @drop_reports_annotations_shared_attributes_value_fk;
EXECUTE drop_reports_annotations_shared_attributes_value_fk_stmt;
DEALLOCATE PREPARE drop_reports_annotations_shared_attributes_value_fk_stmt;

SET @reports_annotations_shared_attributes_value_idx = (
    SELECT INDEX_NAME
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports_annotations_shared_attributes'
      AND INDEX_NAME = 'value'
    LIMIT 1
);
SET @drop_reports_annotations_shared_attributes_value_idx = IF(
    @reports_annotations_shared_attributes_value_idx IS NULL,
    'SELECT 1',
    'ALTER TABLE `reports_annotations_shared_attributes` DROP INDEX `value`'
);
PREPARE drop_reports_annotations_shared_attributes_value_idx_stmt FROM @drop_reports_annotations_shared_attributes_value_idx;
EXECUTE drop_reports_annotations_shared_attributes_value_idx_stmt;
DEALLOCATE PREPARE drop_reports_annotations_shared_attributes_value_idx_stmt;

ALTER TABLE `reports_annotations_shared_attributes` CHANGE `value` `value` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

--changeset tn:18

ALTER TABLE `inforex`.`tokens_tags_optimized`
    CHANGE COLUMN `pos` `pos` VARCHAR(32) NOT NULL ;

ALTER TABLE `inforex`.`reports`
    CHANGE COLUMN `author` `author` VARCHAR(256) CHARACTER SET 'utf8' NULL DEFAULT NULL ;

--changeset tn:19
--validCheckSum 7:a42b784ac1f95e717356c35e8beaf851

SET @activities_user_corpus_datetime_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activities'
      AND INDEX_NAME = 'activities_user_corpus_datetime_idx'
);
SET @activities_user_corpus_datetime_idx_sql = IF(
    @activities_user_corpus_datetime_idx_exists = 0,
    'ALTER TABLE `activities` ADD INDEX `activities_user_corpus_datetime_idx` (`user_id`, `corpus_id`, `datetime`)',
    'SELECT 1'
);
PREPARE activities_user_corpus_datetime_idx_stmt FROM @activities_user_corpus_datetime_idx_sql;
EXECUTE activities_user_corpus_datetime_idx_stmt;
DEALLOCATE PREPARE activities_user_corpus_datetime_idx_stmt;

SET @users_corpus_roles_corpus_role_user_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users_corpus_roles'
      AND INDEX_NAME = 'users_corpus_roles_corpus_role_user_idx'
);
SET @users_corpus_roles_corpus_role_user_idx_sql = IF(
    @users_corpus_roles_corpus_role_user_idx_exists = 0,
    'ALTER TABLE `users_corpus_roles` ADD INDEX `users_corpus_roles_corpus_role_user_idx` (`corpus_id`, `role`, `user_id`)',
    'SELECT 1'
);
PREPARE users_corpus_roles_corpus_role_user_idx_stmt FROM @users_corpus_roles_corpus_role_user_idx_sql;
EXECUTE users_corpus_roles_corpus_role_user_idx_stmt;
DEALLOCATE PREPARE users_corpus_roles_corpus_role_user_idx_stmt;

--changeset tn:20
--validCheckSum 7:c47fbb5a78bcd57b574f9aa47cd05705

SET @exports_corpus_submit_id_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'exports'
      AND INDEX_NAME = 'exports_corpus_submit_id_idx'
);
SET @exports_corpus_submit_id_idx_sql = IF(
    @exports_corpus_submit_id_idx_exists = 0,
    'ALTER TABLE `exports` ADD INDEX `exports_corpus_submit_id_idx` (`corpus_id`, `datetime_submit`, `export_id`)',
    'SELECT 1'
);
PREPARE exports_corpus_submit_id_idx_stmt FROM @exports_corpus_submit_id_idx_sql;
EXECUTE exports_corpus_submit_id_idx_stmt;
DEALLOCATE PREPARE exports_corpus_submit_id_idx_stmt;

SET @export_errors_export_id_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'export_errors'
      AND INDEX_NAME = 'export_errors_export_id_idx'
);
SET @export_errors_export_id_idx_sql = IF(
    @export_errors_export_id_idx_exists = 0,
    'ALTER TABLE `export_errors` ADD INDEX `export_errors_export_id_idx` (`export_id`)',
    'SELECT 1'
);
PREPARE export_errors_export_id_idx_stmt FROM @export_errors_export_id_idx_sql;
EXECUTE export_errors_export_id_idx_stmt;
DEALLOCATE PREPARE export_errors_export_id_idx_stmt;

--changeset tn:21
--validCheckSum 7:a5a24c7e308e0dab402b822d55b68ca1

SET @reports_corpora_deleted_id_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND INDEX_NAME = 'reports_corpora_deleted_id_idx'
);
SET @reports_corpora_deleted_id_idx_sql = IF(
    @reports_corpora_deleted_id_idx_exists = 0,
    'ALTER TABLE `reports` ADD INDEX `reports_corpora_deleted_id_idx` (`corpora`, `deleted`, `id`)',
    'SELECT 1'
);
PREPARE reports_corpora_deleted_id_idx_stmt FROM @reports_corpora_deleted_id_idx_sql;
EXECUTE reports_corpora_deleted_id_idx_stmt;
DEALLOCATE PREPARE reports_corpora_deleted_id_idx_stmt;

--changeset tn:22

SET @reports_annotations_autoextension_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports_annotations_optimized'
      AND INDEX_NAME = 'reports_annotations_autoextension_idx'
);
SET @reports_annotations_autoextension_idx_sql = IF(
    @reports_annotations_autoextension_idx_exists = 0,
    'ALTER TABLE `reports_annotations_optimized` ADD INDEX `reports_annotations_autoextension_idx` (`report_id`, `source`, `stage`, `type_id`, `from`, `to`)',
    'SELECT 1'
);
PREPARE reports_annotations_autoextension_idx_stmt FROM @reports_annotations_autoextension_idx_sql;
EXECUTE reports_annotations_autoextension_idx_stmt;
DEALLOCATE PREPARE reports_annotations_autoextension_idx_stmt;

--changeset tn:23

SET @activities_user_datetime_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activities'
      AND INDEX_NAME = 'activities_user_datetime_idx'
);
SET @activities_user_datetime_idx_sql = IF(
    @activities_user_datetime_idx_exists = 0,
    'ALTER TABLE `activities` ADD INDEX `activities_user_datetime_idx` (`user_id`, `datetime`)',
    'SELECT 1'
);
PREPARE activities_user_datetime_idx_stmt FROM @activities_user_datetime_idx_sql;
EXECUTE activities_user_datetime_idx_stmt;
DEALLOCATE PREPARE activities_user_datetime_idx_stmt;

--changeset tn:24

SET @activities_user_type_datetime_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activities'
      AND INDEX_NAME = 'activities_user_type_datetime_idx'
);
SET @activities_user_type_datetime_idx_sql = IF(
    @activities_user_type_datetime_idx_exists = 0,
    'ALTER TABLE `activities` ADD INDEX `activities_user_type_datetime_idx` (`user_id`, `activity_type_id`, `datetime`)',
    'SELECT 1'
);
PREPARE activities_user_type_datetime_idx_stmt FROM @activities_user_type_datetime_idx_sql;
EXECUTE activities_user_type_datetime_idx_stmt;
DEALLOCATE PREPARE activities_user_type_datetime_idx_stmt;

--changeset tn:25

SET @activities_datetime_user_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activities'
      AND INDEX_NAME = 'activities_datetime_user_idx'
);
SET @activities_datetime_user_idx_sql = IF(
    @activities_datetime_user_idx_exists = 0,
    'ALTER TABLE `activities` ADD INDEX `activities_datetime_user_idx` (`datetime`, `user_id`)',
    'SELECT 1'
);
PREPARE activities_datetime_user_idx_stmt FROM @activities_datetime_user_idx_sql;
EXECUTE activities_datetime_user_idx_stmt;
DEALLOCATE PREPARE activities_datetime_user_idx_stmt;

--changeset tn:26

SET @activities_anonymous_datetime_ip_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activities'
      AND INDEX_NAME = 'activities_anonymous_datetime_ip_idx'
);
SET @activities_anonymous_datetime_ip_idx_sql = IF(
    @activities_anonymous_datetime_ip_idx_exists = 0,
    'ALTER TABLE `activities` ADD INDEX `activities_anonymous_datetime_ip_idx` (`user_id`, `datetime`, `ip_id`)',
    'SELECT 1'
);
PREPARE activities_anonymous_datetime_ip_idx_stmt FROM @activities_anonymous_datetime_ip_idx_sql;
EXECUTE activities_anonymous_datetime_ip_idx_stmt;
DEALLOCATE PREPARE activities_anonymous_datetime_ip_idx_stmt;

--changeset tn:27

SET @activities_corpus_user_datetime_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'activities'
      AND INDEX_NAME = 'activities_corpus_user_datetime_idx'
);
SET @activities_corpus_user_datetime_idx_sql = IF(
    @activities_corpus_user_datetime_idx_exists = 0,
    'ALTER TABLE `activities` ADD INDEX `activities_corpus_user_datetime_idx` (`corpus_id`, `user_id`, `datetime`)',
    'SELECT 1'
);
PREPARE activities_corpus_user_datetime_idx_stmt FROM @activities_corpus_user_datetime_idx_sql;
EXECUTE activities_corpus_user_datetime_idx_stmt;
DEALLOCATE PREPARE activities_corpus_user_datetime_idx_stmt;

--changeset tn:28

UPDATE `report_perspectives`
SET `title` = 'Morph. Agreement'
WHERE `id` = 'morphodisambagreement';

UPDATE `report_perspectives`
SET `title` = 'History'
WHERE `id` = 'diffs';

--changeset tn:29

SET @reports_corpora_subcorpus_id_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reports'
      AND INDEX_NAME = 'reports_corpora_subcorpus_id_idx'
);
SET @reports_corpora_subcorpus_id_idx_sql = IF(
    @reports_corpora_subcorpus_id_idx_exists = 0,
    'ALTER TABLE `reports` ADD INDEX `reports_corpora_subcorpus_id_idx` (`corpora`, `subcorpus_id`, `id`)',
    'SELECT 1'
);
PREPARE reports_corpora_subcorpus_id_idx_stmt FROM @reports_corpora_subcorpus_id_idx_sql;
EXECUTE reports_corpora_subcorpus_id_idx_stmt;
DEALLOCATE PREPARE reports_corpora_subcorpus_id_idx_stmt;

SET @tokens_tags_optimized_word_frequency_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tokens_tags_optimized'
      AND INDEX_NAME = 'tokens_tags_optimized_word_frequency_idx'
);
SET @tokens_tags_optimized_word_frequency_idx_sql = IF(
    @tokens_tags_optimized_word_frequency_idx_exists = 0,
    'ALTER TABLE `tokens_tags_optimized` ADD INDEX `tokens_tags_optimized_word_frequency_idx` (`token_id`, `disamb`, `pos`, `base_id`)',
    'SELECT 1'
);
PREPARE tokens_tags_optimized_word_frequency_idx_stmt FROM @tokens_tags_optimized_word_frequency_idx_sql;
EXECUTE tokens_tags_optimized_word_frequency_idx_stmt;
DEALLOCATE PREPARE tokens_tags_optimized_word_frequency_idx_stmt;

--changeset tn:30

SET @tokens_report_id_token_id_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tokens'
      AND INDEX_NAME = 'tokens_report_id_token_id_idx'
);
SET @tokens_report_id_token_id_idx_sql = IF(
    @tokens_report_id_token_id_idx_exists = 0,
    'ALTER TABLE `tokens` ADD INDEX `tokens_report_id_token_id_idx` (`report_id`, `token_id`)',
    'SELECT 1'
);
PREPARE tokens_report_id_token_id_idx_stmt FROM @tokens_report_id_token_id_idx_sql;
EXECUTE tokens_report_id_token_id_idx_stmt;
DEALLOCATE PREPARE tokens_report_id_token_id_idx_stmt;

SET @tokens_tags_optimized_disamb_pos_token_base_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tokens_tags_optimized'
      AND INDEX_NAME = 'tokens_tags_optimized_disamb_pos_token_base_idx'
);
SET @tokens_tags_optimized_disamb_pos_token_base_idx_sql = IF(
    @tokens_tags_optimized_disamb_pos_token_base_idx_exists = 0,
    'ALTER TABLE `tokens_tags_optimized` ADD INDEX `tokens_tags_optimized_disamb_pos_token_base_idx` (`disamb`, `pos`, `token_id`, `base_id`)',
    'SELECT 1'
);
PREPARE tokens_tags_optimized_disamb_pos_token_base_idx_stmt FROM @tokens_tags_optimized_disamb_pos_token_base_idx_sql;
EXECUTE tokens_tags_optimized_disamb_pos_token_base_idx_stmt;
DEALLOCATE PREPARE tokens_tags_optimized_disamb_pos_token_base_idx_stmt;

--changeset tn:31

SET @tto_stage_user_idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tokens_tags_optimized'
      AND INDEX_NAME = 'tokens_tags_optimized_stage_user_disamb_pos_token_base_idx'
);
SET @tto_stage_user_idx_sql = IF(
    @tto_stage_user_idx_exists = 0,
    'ALTER TABLE `tokens_tags_optimized` ADD INDEX `tokens_tags_optimized_stage_user_disamb_pos_token_base_idx` (`stage`, `user_id`, `disamb`, `pos`, `token_id`, `base_id`)',
    'SELECT 1'
);
PREPARE tokens_tags_optimized_stage_user_disamb_pos_token_base_idx_stmt FROM @tto_stage_user_idx_sql;
EXECUTE tokens_tags_optimized_stage_user_disamb_pos_token_base_idx_stmt;
DEALLOCATE PREPARE tokens_tags_optimized_stage_user_disamb_pos_token_base_idx_stmt;

--changeset tn:32

SET @users_auth_provider_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'auth_provider'
);
SET @users_auth_provider_sql = IF(
    @users_auth_provider_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `auth_provider` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `clarin_login`',
    'SELECT 1'
);
PREPARE users_auth_provider_stmt FROM @users_auth_provider_sql;
EXECUTE users_auth_provider_stmt;
DEALLOCATE PREPARE users_auth_provider_stmt;

SET @users_auth_subject_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'auth_subject'
);
SET @users_auth_subject_sql = IF(
    @users_auth_subject_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `auth_subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `auth_provider`',
    'SELECT 1'
);
PREPARE users_auth_subject_stmt FROM @users_auth_subject_sql;
EXECUTE users_auth_subject_stmt;
DEALLOCATE PREPARE users_auth_subject_stmt;

SET @users_auth_username_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'auth_username'
);
SET @users_auth_username_sql = IF(
    @users_auth_username_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `auth_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `auth_subject`',
    'SELECT 1'
);
PREPARE users_auth_username_stmt FROM @users_auth_username_sql;
EXECUTE users_auth_username_stmt;
DEALLOCATE PREPARE users_auth_username_stmt;

SET @users_auth_email_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'auth_email'
);
SET @users_auth_email_sql = IF(
    @users_auth_email_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `auth_email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `auth_username`',
    'SELECT 1'
);
PREPARE users_auth_email_stmt FROM @users_auth_email_sql;
EXECUTE users_auth_email_stmt;
DEALLOCATE PREPARE users_auth_email_stmt;

SET @users_auth_email_verified_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'auth_email_verified'
);
SET @users_auth_email_verified_sql = IF(
    @users_auth_email_verified_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `auth_email_verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `auth_email`',
    'SELECT 1'
);
PREPARE users_auth_email_verified_stmt FROM @users_auth_email_verified_sql;
EXECUTE users_auth_email_verified_stmt;
DEALLOCATE PREPARE users_auth_email_verified_stmt;

SET @users_auth_linked_at_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'auth_linked_at'
);
SET @users_auth_linked_at_sql = IF(
    @users_auth_linked_at_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `auth_linked_at` datetime DEFAULT NULL AFTER `auth_email_verified`',
    'SELECT 1'
);
PREPARE users_auth_linked_at_stmt FROM @users_auth_linked_at_sql;
EXECUTE users_auth_linked_at_stmt;
DEALLOCATE PREPARE users_auth_linked_at_stmt;

SET @users_last_login_at_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'last_login_at'
);
SET @users_last_login_at_sql = IF(
    @users_last_login_at_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `last_login_at` datetime DEFAULT NULL AFTER `auth_linked_at`',
    'SELECT 1'
);
PREPARE users_last_login_at_stmt FROM @users_last_login_at_sql;
EXECUTE users_last_login_at_stmt;
DEALLOCATE PREPARE users_last_login_at_stmt;

SET @users_auth_identity_uidx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND INDEX_NAME = 'users_auth_identity_uidx'
);
SET @users_auth_identity_uidx_sql = IF(
    @users_auth_identity_uidx_exists = 0,
    'ALTER TABLE `users` ADD UNIQUE INDEX `users_auth_identity_uidx` (`auth_provider`, `auth_subject`)',
    'SELECT 1'
);
PREPARE users_auth_identity_uidx_stmt FROM @users_auth_identity_uidx_sql;
EXECUTE users_auth_identity_uidx_stmt;
DEALLOCATE PREPARE users_auth_identity_uidx_stmt;

--changeset tn:33
SET @exports_export_format_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'exports'
      AND COLUMN_NAME = 'export_format'
);
SET @exports_export_format_sql = IF(
    @exports_export_format_exists = 0,
    'ALTER TABLE `exports` ADD COLUMN `export_format` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''legacy'' AFTER `tagging`',
    'SELECT 1'
);
PREPARE exports_export_format_stmt FROM @exports_export_format_sql;
EXECUTE exports_export_format_stmt;
DEALLOCATE PREPARE exports_export_format_stmt;
