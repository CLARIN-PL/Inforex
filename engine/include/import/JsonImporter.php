<?php

use \JsonMachine\Items;


require_once(__DIR__ . '/../vendor/autoload.php');

$imp = new JsonImporter();

$imp->importFromJson('sample.json');

class JsonImporter
{
    function __construct()
    {
        $this->db = new Database(Config::Config()->get_dsn(), false);
        $GLOBALS['db'] = $this->db; // necessary for other f
    }

    public function importFromJson($file, $corpusId, $userid){
        $docs = Items::fromFile($file , ['pointer' => '/docs']);
        foreach ($docs as $d) {
            $content = $d->raw;
            $title = $d->title;

            $annotations = $d->labels;
            $relations = $d->relations;

            $rid = $this->insertReport($corpusId, $userid, $title, $content);

            foreach ($annotations as $ann) {
                $from = $ann->from;
                $to = $ann->to;
                $txt = $ann->text;
                print($from) . PHP_EOL;
                print($to) . PHP_EOL;
                print($txt) . PHP_EOL;
            }
            print($rid);
        }
    }

    private function insertReport($corpusId, $userId, $title, $content)
    {
        $r = new TableReport();
        $r->corpora = intval($corpusId);
        $r->user_id = intval($userId);
        //1-xml, 2-plain, 3-premorph
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

    private function insertAnnotation($reportId, $from, $to, $text,$typeId, $userId)
    {
        $ann = new TableReportAnnotation();
        $ann->report_id = intval($reportId);
        $ann->from = $from;
        $ann->to = $to;
        $ann->type_id = $typeId;
        $ann->text = $text;
        $ann->user_id = $userId;
        $ann->creation_time = date("Y-m-d H:i:s");
        $ann->stage = "agreement";
        $ann->source = "auto";

        $ann->save();

        return $ann->id;
    }
}

