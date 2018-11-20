UPDATE `corpus_roles` SET `description` = 'Edycja anotacji publicznych' WHERE `corpus_roles`.`role` = 'annotate';

INSERT INTO `corpus_roles` (`role`, `description`) VALUES ('', ''), ('annotate_agreement', 'Edycja anotacji prywatnych na potrzeby badania zgodności.');
