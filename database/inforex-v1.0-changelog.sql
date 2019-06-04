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
