/*Tabela `tokens` definiuje segmentację tekstu na tokeny. Jeżeli dla danego dokumentu została zdefiniowana segmentacja, to anotacje muszą być spójne z określoną segmentacją.

tokens
* token_id BIGINT
* report_id INT FOREIGN KEY na reports.id
* from INT
* to INT
*/
CREATE TABLE `tokens` (
  `token_id` BIGINT (20) NOT NULL AUTO_INCREMENT,
  `report_id` BIGINT(20)  NOT NULL,
  `from` INT  NOT NULL,
  `to` INT  NOT NULL,
  PRIMARY KEY (`token_id`),
  CONSTRAINT `fk_report_id` FOREIGN KEY  (`report_id`)
    REFERENCES `reports` (`id`)
    ON UPDATE CASCADE
)
ENGINE = InnoDB;
