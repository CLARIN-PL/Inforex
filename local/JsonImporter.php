<?php

use \JsonMachine\Items;

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . 'settings.php');
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');
require_once($enginePath. DIRECTORY_SEPARATOR . "include/database/CDbAnnotationType.php");

Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");

require_once($enginePath. DIRECTORY_SEPARATOR . 'include/vendor/autoload.php');


$imp = new JsonImporter();
$imp->importFromJson('sample.json',116,1,2, 185);

class JsonImporter
{
    function __construct()
    {
        $this->db = new Database(Config::Config()->get_dsn(), false);
        $GLOBALS['db'] = $this->db; // necessary for other f
    }

    public function importFromJson($file, $corpusId, $annotationSetId, $relationSetId, $userId){
        $docs = Items::fromFile($file , ['pointer' => '/docs']);
        $annotationTypeMap = DbAnnotationType::getAnnotationTypesForSetAsNameToIdMap($annotationSetId);
        $relationTypeMap = DbRelationSet::getRelationTypesForSetAsNameToIdMap($relationSetId);
        $stage = "agreement";

        foreach ($docs as $d) {

            $mapRelations = array();

            $content = $d->raw;
            $title = $d->title;
            $annotations = $d->labels;
            $relations = $d->relations;

            $rid = $this->insertReport($corpusId, $userId, $title, $content);

            foreach ($annotations as $ann) {
                $sid =  intval($ann->sid);
                $from = $ann->from;
                $to = $ann->to;
                $txt = $ann->text;
                $type = $annotationTypeMap[$ann->name];
                $aid = $this->insertAnnotation($rid, $from, $to, $txt, $type, $userId, $stage);
                $mapRelations[$sid] = $aid;
            }

            foreach ($relations as $rel){
                $this->insertRelation($rel, $mapRelations, $relationTypeMap, $userId , $stage);
            }
        }

    }

    private function insertReport($corpusId, $userId, $title, $content)
    {
        $r = new TableReport();
        $r->corpora = intval($corpusId);
        $r->user_id = intval($userId);
        $r->format_id = 1;
        $r->type = 1;
        $r->title = $title;
        $r->status = 2;
        $r->date = date("Y-m-d H:i:s");
        $r->source = "system";
        $r->author = "system";
        $r->content = $content;
        $r->filename = $title;

        $r->save();
        DbReport::insertEmptyReportExt($r->id);

        return $r->id;
    }

    private function insertAnnotation($reportId, $from, $to, $text, $typeId, $userId, $stage)
    {
        $ann = new TableReportAnnotation();
        $ann->report_id = intval($reportId);
        $ann->from = $from;
        $ann->to = $to;
        $ann->type_id = $typeId;
        $ann->text = $text;
        $ann->user_id = $userId;
        $ann->creation_time = date("Y-m-d H:i:s");
        $ann->stage = $stage;
        $ann->source = "auto";

        $ann->save();

        return $ann->id;
    }

    private function insertRelation($rel, $mapRelations, $relationTypeMap, $userId, $stage){
        $source = $mapRelations[intval($rel->source)];
        $target = $mapRelations[intval($rel->target)];
        $name = $rel->name;
        $date = date("Y-m-d H:i:s");
        $typeId = $relationTypeMap[$name];

        DbRelationSet::insertRelation($typeId, $source, $target, $date, $userId, $stage);
    }

}

