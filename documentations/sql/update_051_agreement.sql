UPDATE `corpus_roles` SET description='Edycja anotacji prywatnych' WHERE role='annotate_agreement';

ALTER TABLE `reports_annotations_optimized` CHANGE `stage` `stage` ENUM( 'new', 'final', 'discarded', 'agreement' ) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT 'final'