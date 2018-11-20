/*W bazie należy dodać tabelę, która będzie definicją możliwych statusów postępu prac.

flags
  flag_id INT AUTO_INCREMENT
  name VARCHAR(64)
  description TEXT
  
  CREATE TABLE `corpus_perspective_roles` (
  `user_id` INT NOT NULL,
  `corpus_id` INT NOT NULL,
  `report_perspective_id` VARCHAR(32) NOT NULL,
  CONSTRAINT `fk_user_id` FOREIGN KEY  (`user_id`)
    REFERENCES `users` (`user_id`)
    ON UPDATE CASCADE,
  CONSTRAINT `fk_corpus_id` FOREIGN KEY  (`corpus_id`)
    REFERENCES `corpora` (`id`)
    ON UPDATE CASCADE,
  CONSTRAINT `fk_report_perspective_id` FOREIGN KEY  (`report_perspective_id`)
    REFERENCES `report_perspectives` (`id`)
    ON UPDATE CASCADE
)
  
  
  */

CREATE TABLE `flags` (
	`flag_id` INT NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR( 64 ) NOT NULL ,
	`description` TEXT NOT NULL ,
	PRIMARY KEY ( `flag_id` )
) ENGINE = InnoDB;

CREATE TABLE `corpora_flags` (
	`corpora_flag_id` INT NOT NULL AUTO_INCREMENT ,
	`corpora_id` INT NOT NULL ,
	`name` VARCHAR( 128 ) NOT NULL ,
	PRIMARY KEY (`corpora_flag_id`) ,
	CONSTRAINT `fk_corpora_id` FOREIGN KEY (`corpora_id`)
	  REFERENCES `corpora` (`id`) 
) ENGINE = InnoDB;

CREATE TABLE `reports_flags` (
	`corpora_flag_id` INT NOT NULL,
	`report_id` BIGINT(20) NOT NULL, 
	`flag_id` INT NOT NULL,
	CONSTRAINT `fk_corpora_flag_id` FOREIGN KEY (`corpora_flag_id`)
	  REFERENCES `corpora_flags` (`corpora_flag_id`),
	CONSTRAINT `fk1_report_id` FOREIGN KEY (`report_id`)
	  REFERENCES `reports` (`id`),
	CONSTRAINT `fk_flag_id` FOREIGN KEY (`flag_id`)
	  REFERENCES `flags` (`flag_id`) 
) ENGINE = InnoDB;

INSERT INTO `gpw`.`flags` (
`flag_id` ,
`name` ,
`description`
)
VALUES (
1 , 'nowy', 'dokument nie został jeszcze sprawdzony'
), (
2 , 'w opracowaniu', 'dokument jest w trakcie opracowania'
), (
3 , 'gotowy', 'praca nad dokumentem została zakończone i wymaga sprawdzenia'
), (
4 , 'sprawdzony', 'dokument został pomyślnie sprawdzony'
), (
5 , 'do poprawy', 'dokument wymaga poprawy'
);
