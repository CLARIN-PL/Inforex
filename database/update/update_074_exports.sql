ALTER TABLE  `exports` ADD  `progress` INT NOT NULL DEFAULT  '0';
ALTER TABLE `exports` ADD `statistics` TEXT NULL DEFAULT NULL AFTER `progress`;

CREATE TABLE `export_errors` (
  `id` int(11) NOT NULL,
  `export_id` int(11) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `export_errors`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `export_errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `export_errors` ADD `error_details` TEXT NOT NULL AFTER `message`;
ALTER TABLE `export_errors` ADD `count` INT NOT NULL AFTER `error_details`;
ALTER TABLE `export_errors` CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `export_errors` CHANGE `error_details` `error_details` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;