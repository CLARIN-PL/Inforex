/*Kolumna `short_description` typu VARCHAR będzie zawierała skrócony opis typu anotacji.*/
ALTER TABLE `annotation_types` ADD `short_description` VARCHAR( 64 ) NULL DEFAULT NULL ;