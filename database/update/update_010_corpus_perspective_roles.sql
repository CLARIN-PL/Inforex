/*
Jako trzeci typ dostępu do perspektywy będzie `role`. Oznacza to, że perspektywa będzie dostępna wybranym użytkownikom. Do przechowania informacji o dostępie do perspektywy w trybie `role` będzie trzeba utworzyć tablicę:
corpus_perspective_roles

    * user_id
    * corpus_id
    * report_perspective_id

Wpis w tej tablicy będzie oznaczał, że użytkownik user_id ma dostęp do perspektywy report_perspective_id w korpusie corpus_id.

Jeżeli użytkownik nie będzie miał dostępu do perspektywy, to nie powinna być ona widoczna w menu perspektyw.
*/
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
ENGINE = InnoDB;