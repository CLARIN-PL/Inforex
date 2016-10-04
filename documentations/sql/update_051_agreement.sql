UPDATE `corpus_roles` SET description='Edycja anotacji prywatnych' WHERE role='annotate_agreement';

ALTER TABLE `reports_annotations_optimized` CHANGE `stage` `stage` ENUM( 'new', 'final', 'discarded', 'agreement' ) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT 'final'

ALTER TABLE `corpus_roles` ADD `description_long` TEXT NOT NULL ;

INSERT INTO `corpus_roles` (
`role` ,
`description` ,
`description_long`
)
VALUES (
'agreement_check', 'Sprawdzanie zgodności anotatorów', 'Dostęp do strony z wynikami zgodności anotatorów. '
);