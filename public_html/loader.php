<?php

require_once(__DIR__. "/../engine" .DIRECTORY_SEPARATOR . "settings.php");
require_once(__DIR__. "/../engine" . DIRECTORY_SEPARATOR . 'include.php');

/*** load initial data set */
Config::Config()->put_localConfigFilename(__DIR__. "/../config/".DIRECTORY_SEPARATOR."config.local.php");
$db = new Database(Config::Config()->get_dsn());
if(! initialDataLoaded($db) ) {
    initialDataLoad($db);
}
$db->disconnect(); unset($db);

function initialDataLoaded($db) {

    // zwraca true jeśli pakiet danych inicjalnych był już załadowany
    // do bazy
    //  testem jest pustość bazy korpusów
    $sql = "SELECT id FROM `corpora`";
    $result = $db->fetch($sql);
    return (count($result))!==0;

} // initialDataLoaded()

function initialDataLoad($db) {

    //$sql = "REPLACE INTO `annotation_sets` (`annotation_set_id`,`name`,`description`) VALUES (1,'default name','default desc'), (2,'default name','default desc'), (3,'default name','default desc');";
    //$db->execute($sql);
    //print("Loading... \n");

    $sql = "INSERT INTO `corpora` (`id`, `name`, `description`, `public`, `user_id`, `ext`, `date_created`) VALUES (1,'test','test',0,1,NULL,'2022-09-30 10:24:39')";
    $db->execute($sql);

    $sql = "INSERT INTO `users_corpus_roles` (`user_id`, `corpus_id`, `role`) VALUES (1,1,'add_documents'),(1,1,'agreement_check'),(1,1,'agreement_morpho'),(1,1,'annotate'),(1,1,'annotate_agreement'),(1,1,'browse_annotations'),(1,1,'browse_relations'),(1,1,'delete_annotations'),(1,1,'delete_documents'),(1,1,'edit_documents'),(1,1,'export'),(1,1,'flag_history'),(1,1,'manager'),(1,1,'read'),(1,1,'read_limited'),(1,1,'run_tests'),(1,1,'tasks'),(1,1,'wccl_match')";
    $db->execute($sql);

    $sql = "INSERT INTO `corpus_and_report_perspectives` (`perspective_id`, `corpus_id`, `access`) VALUES ('agreement',1,'loggedin'),('anaphora',1,'loggedin'),('annotation_attributes',1,'loggedin'),('annotation_lemma',1,'loggedin'),('annotation_table',1,'loggedin'),('annotator',1,'loggedin'),('annotator_anaphora',1,'loggedin'),('annotator_wsd',1,'loggedin'),('autoextension',1,'loggedin'),('cleanup',1,'loggedin'),('diffs',1,'loggedin'),('edit',1,'loggedin'),('edittranslation',1,'loggedin'),('extendedmetadata',1,'loggedin'),('flag_history',1,'loggedin'),('images',1,'loggedin'),('importAnnotations',1,'loggedin'),('metadata',1,'loggedin'),('morphodisamb',1,'loggedin'),('morphodisambagreement',1,'loggedin'),('preview',1,'loggedin'),('relation_agreement',1,'loggedin'),('tokenization',1,'loggedin'),('topic',1,'loggedin'),('transcription',1,'loggedin'),('viewer',1,'loggedin')";
    $db->execute($sql);

    $sql = "INSERT INTO `reports` (`id`, `corpora`, `date`, `title`, `source`, `author`, `content`, `type`, `status`, `user_id`, `subcorpus_id`, `tokenization`, `format_id`, `lang`, `filename`, `parent_report_id`, `deleted`) VALUES (1,1,'1970-01-01','test','','',' To jest duże okno. Bardzo duże.\r\n',1,2,1,NULL,NULL,2,NULL,'',NULL,0)";
    $db->execute($sql);

    $sql = "ALTER ALGORITHM = UNDEFINED DEFINER=`inforex`@`%` SQL SECURITY DEFINER VIEW `reports_annotations` AS select `ra`.`id` AS `id`,`ra`.`report_id` AS `report_id`,`ra`.`type_id` AS `type_id`,`at`.`name` AS `type`,`at`.`group_id` AS `group`,`ra`.`from` AS `from`,`ra`.`to` AS `to`,`ra`.`text` AS `text`,`ra`.`user_id` AS `user_id`,`ra`.`creation_time` AS `creation_time`,`ra`.`stage` AS `stage`,`ra`.`source` AS `source` from (`reports_annotations_optimized` `ra` left join `annotation_types` `at` on((`at`.`annotation_type_id` = `ra`.`type_id`)))";
    $db->execute($sql);

    $sql = "INSERT INTO `annotation_sets_corpora` (`annotation_set_id`, `corpus_id`) VALUES (1,1)";
    $db->execute($sql);

/*
    $sql = "INSERT INTO `reports_annotations_optimized` (`id`, `report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`, `source`) VALUES (1,1,360,6,9,'duże',1,'2022-09-30 10:31:08','final','user')";
// lub trzy annotacje na raz */
/*    $sql = "INSERT INTO `reports_annotations_optimized` (`id`, `report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`, `source`) VALUES (1,1,360,6,9,'duże',1,'2022-10-03 08:07:37','final','user'),(2,1,119,10,13,'okno',1,'2022-10-03 08:34:21','final','user'),(3,1,360,21,24,'duże',1,'2022-10-03 08:34:21','final','user')";
*/
// dwie annotacje w trybie agreement do testowania relacji
$sql = "INSERT INTO `reports_annotations_optimized` (`id`, `report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`, `source`) VALUES (1,1,360,6,9,'duże',1,'2022-10-03 08:07:37','final','user'),(2,1,119,10,13,'okno',1,'2022-10-03 08:34:21','final','user'),(3,1,360,6,9,'duże',1,'2022-10-03 08:07:37','agreement','user'),(4,1,119,10,13,'okno',1,'2022-10-03 08:34:21','agreement','user')";

    $db->execute($sql);

// sety relacji
    $sql = "INSERT INTO `relation_sets` (`relation_set_id`, `name`, `description`, `public`, `user_id`) VALUES (1,'test','',1,1)";
    $db->execute($sql);
    $sql = "INSERT INTO `relation_types` (`id`, `relation_set_id`, `name`, `description`, `annotation_set_id`) VALUES (1,1,'test','',NULL),(2,1,'test2','',NULL)";
    $db->execute($sql);
    $sql = "INSERT INTO `relations_groups` (`relation_type_id`, `part`, `annotation_set_id`, `annotation_subset_id`, `annotation_type_id`) VALUES (1,'source',1,NULL,NULL),(1,'target',1,NULL,NULL),(2,'source',1,NULL,NULL),(2,'target',1,NULL,NULL)";
    $db->execute($sql);

// dodanie setu relacji do korpusu
    $sql = "INSERT INTO `corpora_relations` (`corpus_id`, `relation_set_id`) VALUES (1,1)";
    $db->execute($sql);

/*
// dodanie samej relacji:
    $sql = "INSERT INTO `relations` (`id`, `relation_type_id`, `source_id`, `target_id`, `date`, `user_id`, `stage`) VALUES (2,1,1,2,'2022-10-03',1,'final')";
    $db->execute($sql);
   // i jeszcze jednej:
    $sql = "INSERT INTO `relations` (`id`, `relation_type_id`, `source_id`, `target_id`, `date`, `user_id`, `stage`) VALUES (3,2,3,2,'2022-10-03',1,'final')";
    $db->execute($sql);
*/


} // initalDataLoad

?>
