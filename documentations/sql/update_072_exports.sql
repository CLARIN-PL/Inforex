ALTER TABLE  `exports` ADD  `progress` INT NOT NULL DEFAULT  '0';
ALTER TABLE  `exports` ADD  `statistics` TEXT NOT NULL AFTER  `progress` ;