/*Dodać tabelę*/

CREATE TABLE `corpus_subcorpora` (
	`subcorpus_id` INT NULL AUTO_INCREMENT,
	`corpus_id` INT NOT NULL,
	`name` VARCHAR( 256 ) NOT NULL ,
	`description` TEXT NOT NULL ,
	PRIMARY KEY ( `subcorpus_id` )
) ENGINE = InnoDB;


/*Rozszerzyć schemat tabeli `reports` o kolumnę `subcorpus_id`.*/
ALTER TABLE `reports` ADD `subcorpus_id` INT NULL;

/*tymczasowo nie ustawialem kluczy obcych!*/

ALTER TABLE `reports` ADD CONSTRAINT `fk_subcorpus_id` FOREIGN KEY (`subcorpus_id`) REFERENCES `corpus_subcorpora` (`subcorpus_id`) ON UPDATE CASCADE;

ALTER TABLE `corpus_subcorpora` ADD INDEX ( `corpus_id` ) ;

ALTER TABLE `corpus_subcorpora` ADD FOREIGN KEY ( `corpus_id` ) REFERENCES `gpw`.`corpora` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

