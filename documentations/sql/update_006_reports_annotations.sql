/*
Dodanie pola `user_id` dla `report_annotation_attribute`
Należy dodać pole `user_id` do tabeli `report_annotation_attribute` jako klucz obcy do tabeli `users`. Pole ustawiane jest TYLKO przy utworzeniu atrybutu i wartość pola równa jest identyfikatorowi użytkownika, który dodał pole. Edycja nie zmienia wartości tego pola.

Klucz dla tej tabeli powinien być zmieniony z (annotation_id, annotation_attribute_id) na (annotation_id, annotation_attribute_id, user_id)
*/
ALTER TABLE `reports_annotations_attributes` ADD `user_id` INT(11) NOT NULL ;
ALTER TABLE `reports_annotations_attributes` ADD CONSTRAINT `user_id` FOREIGN KEY ( `user_id` ) REFERENCES `users` ( `user_id` ) ;
ALTER TABLE `reports_annotations_attributes` DROP PRIMARY KEY;
ALTER TABLE `reports_annotations_attributes` ADD PRIMARY KEY ( `annotation_id` , `annotation_attribute_id` , `user_id` );

/*
Pole `stage` w tabeli `report_annotations` będzie polem wyliczeniowym przyjmującym jedną z wartości: candidate, user, final.
Domyślną wartością jest `user`.
*/
ALTER TABLE `reports_annotations` ADD `stage` ENUM( 'candidate', 'user', 'final' ) NOT NULL DEFAULT 'user';

