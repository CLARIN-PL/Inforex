/*
Tabela typów relacji powinna zawierać:

    * identyfikator relacji (może być liczba, auto increment),
    * nazwa relacji, do 64 znaków,
    * opis relacji, text,
    * identyfikator kategorii (z tabeli annotation_sets) --- informaja, między jakimi typami jednostek będzie można tworzyć relacje.
*/
CREATE TABLE `relation_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64)  NOT NULL,
  `description` TEXT  NOT NULL,
  `annotation_set_id` INT  NOT NULL,
  PRIMARY KEY (`id`)
);
 

/*
Tabela instancji relacji powinna zawierać:

    * identyfikator instancji (liczba, auto increment),
    * identyfikator typu relacji (z powyższej tabeli),
    * jednostka źródłowa (identyfikator jednostki, z tabeli report_annotations),
    * jednostka docelowa (j/w),
    * data dodania,
    * identyfikator użytkownika, który dodał relację.
*/
CREATE TABLE `relations` (
  `id` BIGINT(20)  NOT NULL AUTO_INCREMENT,
  `relation_type_id` INT  NOT NULL,
  `source_id` BIGINT(20)  NOT NULL,
  `target_id` BIGINT(20)  NOT NULL,
  `date` DATE  NOT NULL,
  `user_id` INT  NOT NULL,
  PRIMARY KEY (`id`)
);