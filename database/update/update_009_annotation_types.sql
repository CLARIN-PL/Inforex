/*Do tabeli anotacji należy dodać pole `css`, w którym będą przechowywany styl anotacji (tło, czcionka, ramka, etc.). 
Może to być pole typu VARCHAR, w którym będzie przchowywany opis css.*/
ALTER TABLE `annotation_types` ADD `css` VARCHAR( 255 ) NULL;