ALTER TABLE  `relation_sets` ADD  `description` TEXT NOT NULL ;

CREATE TABLE IF NOT EXISTS `corpora_relations` (
  `corpus_id` int(11) NOT NULL,
  `relation_set_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;