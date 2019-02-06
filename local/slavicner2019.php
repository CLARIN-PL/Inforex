<?php
$enginePath = realpath(dirname(__FILE__) . "/../engine/");
$configPath = realpath(dirname(__FILE__) . "/../config/");
include($enginePath . "/config.php");
include($configPath . "/config.local.php");
include($enginePath . "/include.php");
include($enginePath . "/cliopt.php");
include($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-32");
ob_end_clean();

/******************** set configuration   *********************************************/
$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("folder", "f", "path", "path to a folder where to save the documents"));
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "corpus ID"));
$opt->addParameter(new ClioptParameter("flag", "F", "flag", "tokenize using flag \"flag name\"=flag_value or \"flag name\"=flag_value1,flag_value2,..."));


/******************** scripts entry point   *********************************************/
function process($opt, $argv){
    $opt->parseCli($argv);
    $dsn = CliOptCommon::parseDbParameters($opt, array("localhost", "root", null, "gpw", "3306"));
    $flags = CliOptCommon::parseFlag($opt->getParameters("flag"));
    $folder = $opt->getRequired("folder");
    $corpusId = $opt->getRequired("corpus");

    /** Setup database  */
    $GLOBALS['db'] = new Database($dsn,false);

    /** Validate parameters  */
    CliOptCommon::validateCorpusId($corpusId);
    CliOptCommon::validateFolderExists($folder);
    CliOptCommon::validateFlags($flags, $corpusId);

    /** Process */
    $sqlBuilder = new SqlBuilder("reports", "r");
    $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r." . DB_COLUMN_REPORTS__REPORT_ID, DB_COLUMN_REPORTS__REPORT_ID));
    $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r." . DB_COLUMN_REPORTS__DATE, DB_COLUMN_REPORTS__DATE));
    $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r." . DB_COLUMN_REPORTS__SOURCE, DB_COLUMN_REPORTS__SOURCE));
    $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r." . DB_COLUMN_REPORTS__TITLE, DB_COLUMN_REPORTS__TITLE));
    $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r." . DB_COLUMN_REPORTS__CONTENT, DB_COLUMN_REPORTS__CONTENT));
    $sqlBuilder->addSelectColumn(new SqlBuilderSelect("r." . DB_COLUMN_REPORTS__FILENAME, DB_COLUMN_REPORTS__FILENAME));
    $sqlBuilder->addWhere(new SqlBuilderWhere("r.corpora = ?", array($corpusId)));

    $fi = 1;
    foreach ($flags as $name=>$values) {
        $sqlBuilder->addJoinTable(new SqlBuilderJoin("reports_flags", "rf$fi", "rf$fi.report_id=r.id"));
        $sqlBuilder->addJoinTable(new SqlBuilderJoin("corpora_flags", "cf$fi", "cf$fi.corpora_flag_id = rf$fi.corpora_flag_id"));
        $sqlBuilder->addWhere(new SqlBuilderWhere("cf$fi.short LIKE ?", array($name)));
        $sqlBuilder->addWhere(new SqlBuilderWhere("rf$fi.flag_id IN (" . implode(", ", $values)  . ")", array()));
        $fi++;
    }

    list($sql, $params) = $sqlBuilder->getSql();
    $reports = $GLOBALS['db']->fetch_rows($sql, $params);

    createFolderIfNotExists(implode(DIRECTORY_SEPARATOR, array($folder, "annotated")));
    createFolderIfNotExists(implode(DIRECTORY_SEPARATOR, array($folder, "raw")));

    foreach ($reports as $r){
        $filename = $r[DB_COLUMN_REPORTS__FILENAME];
        //$filename = $r[DB_COLUMN_REPORTS__REPORT_ID];
        echo "$filename\n";

        $anns = PerspectiveAnnotation_table::getAnnotations($r[DB_COLUMN_REPORTS__REPORT_ID]);

        $annotations = array(trim($r[DB_COLUMN_REPORTS__TITLE]));
        foreach ($anns as $an){
            $cols = array();
            $cols[] = $an['text'];
            $cols[] = $an['lemma'];
            $cols[] = $an['type'];
            $cols[] = $an['eid'];
            $annotations[] = implode("\t", $cols);
        }
        $annText = implode(PHP_EOL, $annotations);
        echo $annText . "\n";

        $lang = getLang($r[DB_COLUMN_REPORTS__TITLE]);

        createFolderIfNotExists(implode(DIRECTORY_SEPARATOR, array($folder, "annotated", $lang)));
        $pathOut = implode(DIRECTORY_SEPARATOR, array($folder, "annotated", $lang, $filename . ".out"));
        file_put_contents($pathOut, $annText . PHP_EOL);

        createFolderIfNotExists(implode(DIRECTORY_SEPARATOR, array($folder, "raw", $lang)));
        $pathOut = implode(DIRECTORY_SEPARATOR, array($folder, "raw", $lang, $filename . ".txt"));
        $content = array();
        $content[] = trim($r[DB_COLUMN_REPORTS__TITLE]);
        $content[] = $lang;
        $content[] = trim($r[DB_COLUMN_REPORTS__DATE]);
        $content[] = trim($r[DB_COLUMN_REPORTS__SOURCE]);
        $content[] = preg_replace("/\n\n+/s","\n\n", trim($r[DB_COLUMN_REPORTS__CONTENT]));
        file_put_contents($pathOut, implode("\n", $content) . PHP_EOL);
    }
}

function getLang($title){
    $cols = explode("-", trim($title));
    return $cols[0];
}

function createFolderIfNotExists($folder){
    if (!file_exists($folder)){
        mkdir($folder);
    }
}


/******************** scripts entry point   *********************************************/
try{
    process($opt, $argv);
}catch(Exception $ex){
    print "!! ". $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    die("\n");
}

