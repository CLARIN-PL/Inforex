<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath . DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath . DIRECTORY_SEPARATOR . 'include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/") . DIRECTORY_SEPARATOR . "config.local.php");
require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

/******************** set configuration   *********************************************/
const PARAM_DOCUMENT = "document";
const PARAM_CORPUS = "corpus";
const PARAM_OUTPUT_PATH = "output_path";

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("verbose", "v", null, "verbose mode"));
$opt->addParameter(new ClioptParameter(PARAM_DOCUMENT, "d", "id", "Document id"));
$opt->addParameter(new ClioptParameter(PARAM_OUTPUT_PATH, "p", "out", "output path"));
$opt->addParameter(new ClioptParameter(PARAM_CORPUS, "c", "corpus", "Corpus id"));

try {
    ini_set('memory_limit', '1024M');
    $opt->parseCli($argv);
    $documentId = $opt->getOptional(PARAM_DOCUMENT, "");
    $corpusId = $opt->getOptional(PARAM_CORPUS, "");
    $out_path = $opt->getRequired(PARAM_OUTPUT_PATH);

    $dbHost = "db";
    $dbUser = "inforex";
    $dbPass = "password";
    $dbName = "inforex";
    $dbPort = "3306";

    if ($opt->exists("db-uri")) {
        $uri = $opt->getRequired("db-uri");
        if (preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)) {
            $dbUser = $m[1];
            $dbPass = $m[2];
            $dbHost = $m[3];
            $dbPort = $m[4];
            $dbName = $m[5];
            Config::Config()->put_dsn(array(
                'phptype' => 'mysql',
                'username' => $dbUser,
                'password' => $dbPass,
                'hostspec' => $dbHost . ":" . $dbPort,
                'database' => $dbName
            ));
        } else {
            throw new Exception("DB URI is incorrect. Given '$uri', but expected 'user:pass@host:port/name'");
        }
    }
    Config::Config()->put_verbose($opt->exists("verbose"));
} catch (Exception $ex) {
    print "!! " . $ex->getMessage() . " !!\n\n";
    $opt->printHelp();
    die("\n");
}

try {
    $loader = new CclLoader(Config::Config()->get_dsn(), Config::Config()->get_verbose());
    $loader->processDocuments($corpusId, $documentId, $out_path);
} catch (Exception $ex) {
    print "Error: " . $ex->getMessage() . "\n";
    print_r($ex);
}
sleep(1);

/**
 * Handle single request from tasks_documents.
 */
class CclLoader
{

    function __construct($dsn, $verbose)
    {
        $this->db = new Database($dsn, false);
        $GLOBALS['db'] = $this->db;
        $this->verbose = $verbose;
    }

    /**
     * Print message if verbose mode is on.
     */
    function info($message)
    {
        if ($this->verbose) {
            echo $message . "\n";
        }
    }
    function processDocuments($corpora_id, $report_id, $out_path){
        if($corpora_id != "") {
            $documents = $this->db->fetch_rows("SELECT * FROM reports WHERE corpora=?", array($corpora_id));
            foreach ($documents as $doc) {
                $this->parseDocument($report_id, $doc, $out_path);
            }
        }
        if($report_id != "") {
            $doc = $this->db->fetch("SELECT * FROM reports WHERE id=?", array($report_id));
            $this->parseDocument($report_id, $doc, $out_path);
        }
    }
    function parseDocument($doc, $out_path)
    {
        echo "Processing " . $doc["id"] . "\n";
        $content = $doc["content"];

        if( $doc["format_is"] == 1) {
            $this->parseXmlContent($content, $doc, $out_path);
        } else {
            $this->parseTextContent($content, $doc, $out_path);
        }

    }

    function getMetadata($id)
    {
        $map = [
            "1" => ["author_gender" => "Mężczyzna", "author" => "Broniszewski Grzegorz", "title" => "Kochanek bez serca, czyli Guwernantka zalotna", "text_type" => "komedia", "period" => "1772-1800", "first_edition_year" => "1787", "source_text_year" => "1787", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/kochanek-bez-serca-czyli-guwernantka-zalotna-komedya-w-trzech-aktach-oryginalna,OTkxMjU4/0/#info:metadata"],
            "2" => ["author_gender" => "Mężczyzna", "author" => "Oraczewski Feliks", "title" => "Polak cudzoziemiec w Warszawie", "text_type" => "komedia (obyczajowa)", "period" => "1772-1801", "first_edition_year" => "1778", "source_text_year" => "1778", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/polak-cudzoziemiec-w-warszawie-komedya-we-trzech-aktach,MzY2ODA3/6/#info:metadata"],
            "3" => ["author_gender" => "Mężczyzna", "author" => "Oraczewski Feliks", "title" => "Zabawy, czyli życie bez celu", "text_type" => "komedia", "period" => "1772-1802", "first_edition_year" => "1799", "source_text_year" => "1780", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/zabawy-czyli-zycie-bez-celu-komedya-we-trzech-aktach,MTE2Nzg5NDI/4/#info:metadata"],
            "4" => ["author_gender" => "Mężczyzna", "author" => "Bończa-Tomaszewski Michał", "title" => "Kobiety, jakich mało na świecie, czyli cnota nagrodzona", "text_type" => "drama", "period" => "1772-1803", "first_edition_year" => "1799", "source_text_year" => "1799", "release_location" => "Wilno", "source_text_url" => "https://polonapl/item/kobiety-jakich-malo-na-swiecie-czyli-cnota-nadgrodzona-drama-we-trzech-aktach,MTIwNDM0NDU/2/#info:metadata"],
            "5" => ["author_gender" => "Mężczyzna", "author" => "Bohomolec Franciszek", "title" => "Podejrzliwi", "text_type" => "komedia", "period" => "1772-1804", "first_edition_year" => "1781", "source_text_year" => "1781", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/podeyrzliwi-komedya-we-trzech-aktach,MjE0MTIzMTg/2/#index"],
            "6" => ["author_gender" => "Mężczyzna", "author" => "Czartoryski Adam Kazimierz", "title" => "Kawa", "text_type" => "komedia", "period" => "1772-1805", "first_edition_year" => "1774", "source_text_year" => "1779", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/kawa-komedya-w-jednym-akcie,NzAzMTM5MzY/9/#info:metadata"],
            "7" => ["author_gender" => "Mężczyzna", "author" => "Broniszewski Grzegorz", "title" => "Filut postrzeżony", "text_type" => "komedia", "period" => "1772-1806", "first_edition_year" => "1786", "source_text_year" => "1786", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/filut-postrzezony-komedya-oryginalna-we-trzech-aktach,OTkwODk4/0/#info:metadata"],
            "8" => ["author_gender" => "Mężczyzna", "author" => "Bohomolec Franciszek", "title" => "Czary", "text_type" => "komedia", "period" => "1772-1807", "first_edition_year" => "1773", "source_text_year" => "1775", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/czary-komedya-we-trzech-aktach,MTIxODczNjQx/2/#index"],
            "9" => ["author_gender" => "Mężczyzna", "author" => "Siemoński Adam", "title" => "Anarchia domowa, czyli Moc miłości ojcowskiej  ", "text_type" => "dramat", "period" => "1801-1825 ", "first_edition_year" => "1820", "source_text_year" => "1833", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item/anarchia-domowa-czyli-moc-milosci-ojcowskiej-drama-w-5-ciu-aktach,OTI4OTQ4NzE/4/#item"],
            "10" => ["author_gender" => "Mężczyzna", "author" => "Pasławski Antoni", "title" => "Poczciwski, czyli Wybiegi niepospolite", "text_type" => "komedia", "period" => "1802-1825 ", "first_edition_year" => "1808", "source_text_year" => "1808", "release_location" => "Wilno", "source_text_url" => "https://polonapl/item/poczciwski-czyli-wybiegi-niepospolite-komedya-w-trzech-aktach-oryginalnie-w-polskim,NjU3NTI2NTc/4/#info:metadata"],
            "11" => ["author_gender" => "Mężczyzna", "author" => "Bończa-Tomaszewski Michał", "title" => "Uszczęśliwienie narodu, czyli miłość i wdzięczność w poddanych", "text_type" => "drama", "period" => "1803-1825 ", "first_edition_year" => "1802", "source_text_year" => "1804", "release_location" => "Wilno", "source_text_url" => "https://polonapl/item/uszczesliwienie-narodu-czyli-milosc-y-wdziecznosc-w-poddanych-dramma-w-2-aktach,NjU5MjEwNDY/4/#info"],
            "12" => ["author_gender" => "Mężczyzna", "author" => "Dmuszewski Ludwik Adam", "title" => "Pięć sióst a jedna", "text_type" => "komedioopera", "period" => "1804-1825 ", "first_edition_year" => "1820", "source_text_year" => "1823", "release_location" => "Wrocław", "source_text_url" => "https://polonapl/item/dziela-dramatyczne-l-a-dmuszewskiego-t-10-intryga-przed-slubem-piec-siostr-a-jedna,NjY0Mzg0NDg/108/#item"],
            "13" => ["author_gender" => "Mężczyzna", "author" => "Dmuszewski Ludwik Adam", "title" => "Siedem razy jeden", "text_type" => "komedioopera", "period" => "1805-1825 ", "first_edition_year" => "1804", "source_text_year" => "1823", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/dziela-dramatyczne-l-a-dmuszewskiego-t-8-jan-grudczynski-starosta-rawski-sekretarz,MTgyNzE3NDE/4/#info:metadata"],
            "14" => ["author_gender" => "Mężczyzna", "author" => "Fredro Aleksander", "title" => "Damy i huzary", "text_type" => "komedia (krotochwila)", "period" => "1806-1825 ", "first_edition_year" => "1825", "source_text_year" => "1839", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/damy-i-huzary-komedija-we-3-aktach-proza,MjEzMzg5MDg/"],
            "15" => ["author_gender" => "Mężczyzna", "author" => "Siemoński Adam", "title" => "Panna Intrygalska, czyli Chytrość uniewinniona", "text_type" => "komedia", "period" => "1807-1825 ", "first_edition_year" => "1825", "source_text_year" => "1828", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item/panna-jntrygalska-czyli-chytrosc-uniewinniona-komedya-oryginalna-w-3ch-aktach,ODg2NTIxNzU/6/#index"],
            "16" => ["author_gender" => "Mężczyzna", "author" => "Rajszel Ludwik", "title" => "Dymisjonowany kawaler", "text_type" => "komedioopera", "period" => "1808-1825 ", "first_edition_year" => "1823", "source_text_year" => "1833", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/dymissyonowany-kawaler-komedjoopera-w-jedym-akcie-oryginalnie-napisana,OTI4OTYyNDU/16/#index"],
            "17" => ["author_gender" => "Mężczyzna", "author" => "Skarbek Fryderyk", "title" => "Czemuż nie była sierotą", "text_type" => "drama", "period" => "1826-1850", "first_edition_year" => "1833", "source_text_year" => "1847", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/teatr-t-1,OTI4OTQ2ODE/8/#info:metadata"],
            "18" => ["author_gender" => "Mężczyzna", "author" => "Skarbek Fryderyk", "title" => "Roztrzepany", "text_type" => "komedia", "period" => "1826-1851", "first_edition_year" => "1832", "source_text_year" => "1847", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/teatr-t-2,OTc2NTA5MDA/8/#info:metadata"],
            "19" => ["author_gender" => "Mężczyzna", "author" => "Korzeniowski Józef", "title" => "Aniela", "text_type" => "tragedia", "period" => "1826-1852", "first_edition_year" => "1826", "source_text_year" => "1853", "release_location" => "Petersburg", "source_text_url" => "https://polonapl/item/dramata-i-komedye-mniejsze-serya-3-t-2,MTE0MDYwNTU/232/#info:metadata"],
            "20" => ["author_gender" => "Mężczyzna", "author" => "Korzeniowski Józef", "title" => "Panna mężatka", "text_type" => "komedia", "period" => "1826-1853", "first_edition_year" => "1844", "source_text_year" => "1845", "release_location" => "Wilno", "source_text_url" => "https://polonapl/item/panna-mezatka-komedya-we-trzech-aktach,ODEzNzgxNTk/6/#info:metadata"],
            "21" => ["author_gender" => "Mężczyzna", "author" => "Kamiński Jan Nepomucen", "title" => "Skalmierzanki, czyli Koniki zwierzynieckie", "text_type" => "opera (komedia, komedioopera, wodewil)", "period" => "1826-1854", "first_edition_year" => "1828", "source_text_year" => "1905", "release_location" => "Poznań", "source_text_url" => "https://polonapl/item/skalmierzanki-czyli-koniki-zwierzynieckie-komedyo-opera-w-3-aktach,MTA5MTQ4MjE/15/#info:metadata"],
            "22" => ["author_gender" => "Mężczyzna", "author" => "Anczyc Władysław Ludwik", "title" => "Chłopi arystokraci ", "text_type" => "szkic dramatyczny (obrazek ludowy, komedia ludowa ze śpiewem, szkic dramatyczny ze śpiewem, komedioopera, wodewil) ", "period" => "1826-1855", "first_edition_year" => "1849", "source_text_year" => "1851", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item/chlopi-arystokraci-szkic-dramatyczny-w-jednej-odslonie-ze-spiewkami,NDQ3MTQy/3/#index"],
            "23" => ["author_gender" => "Mężczyzna", "author" => "Mann Maurycy", "title" => "Sztuka i miłość", "text_type" => "dramat", "period" => "1826-1856", "first_edition_year" => "1849", "source_text_year" => "1849", "release_location" => "Poznań", "source_text_url" => "https://polonapl/item/sztuka-i-milosc-dramat-w-2ch-dobach-rzeczywistego-zycia,OTI4OTg5NDM/47/#index"],
            "24" => ["author_gender" => "Mężczyzna", "author" => "Fredro Aleksander", "title" => "Pan Jowialski", "text_type" => "komedia", "period" => "1826-1857", "first_edition_year" => "1832", "source_text_year" => "1880", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item/sluby-panienskie-pan-jowialski-nocleg-w-apeninach,OTkwODEy/4/#info:metadata"],
            "25" => ["author_gender" => "Mężczyzna", "author" => "Narzymski Józef", "title" => "Pozytywni", "text_type" => "komedia", "period" => "1851-1875", "first_edition_year" => "1872", "source_text_year" => "1875", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item-view/fd1de36d-490a-44bf-b0ea-8dcb81feea2d?page=6"],
            "26" => ["author_gender" => "Mężczyzna", "author" => "Bliziński Józef", "title" => "Marcowy kawaler", "text_type" => "krotochwila", "period" => "1851-1876", "first_edition_year" => "1873", "source_text_year" => "1879", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/marcowy-kawaler-krotochwila-w-jednym-akcie,ODM1MzkyNDQ/4/#info:metadata"],
            "27" => ["author_gender" => "Mężczyzna", "author" => "Zalewski Kazimierz", "title" => "Przed ślubem ", "text_type" => "komedia", "period" => "1851-1877", "first_edition_year" => "1875", "source_text_year" => "1875", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/przed-slubem-komedya-w-5-ciu-aktach,Mzc1MzA1NzQ/8/#info:metadata"],
            "28" => ["author_gender" => "Mężczyzna", "author" => "Bałucki Michał", "title" => "Polowanie na męża", "text_type" => "komedia", "period" => "1851-1878", "first_edition_year" => "1865", "source_text_year" => "1869", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/polowanie-na-meza-komedya-z-zycia-mieszczanskiego-w-2-aktach,MTA5NzUxODc/6/#info:metadata"],
            "29" => ["author_gender" => "Mężczyzna", "author" => "Wielogłowski Walery", "title" => "Kucharki", "text_type" => "obrazek sceniczny", "period" => "1851-1879", "first_edition_year" => "1858", "source_text_year" => "1858", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item-view/85b3707c-335c-47a0-a687-faa6a12f85ff?page=4"],
            "30" => ["author_gender" => "Mężczyzna", "author" => "Dobrzański Stanisław", "title" => "Kajcio", "text_type" => "komedia", "period" => "1851-1880", "first_edition_year" => "1869", "source_text_year" => "1886", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item/komedye-kajcio-onufry-podejrzana-osoba-tajemnica-wujaszek-alfonsa-zloty,MTEwNjI5NjQ/5/#info:metadata"],
            "31" => ["author_gender" => "Mężczyzna", "author" => "Zalewski Kazimierz", "title" => "Wycieczka za granicę", "text_type" => "komedia", "period" => "1851-1881", "first_edition_year" => "1870", "source_text_year" => "1872", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/wycieczka-za-granice-komedja-w-1-akcie,Mzc1MzA1ODg/13/#info:metadata"],
            "32" => ["author_gender" => "Kobieta", "author" => "Laudyn-Chrzanowska Stefania", "title" => "Zmarnowane życie", "text_type" => "dramat", "period" => "1876-1900", "first_edition_year" => "1895", "source_text_year" => "1895", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/zmarnowane-zycie-dramat-w-5-ciu-aktach,NzUxODk4NjU/8/#info:metadata"],
            "33" => ["author_gender" => "Mężczyzna", "author" => "Bałucki Michał", "title" => "Grube ryby", "text_type" => "komedia", "period" => "1876-1901", "first_edition_year" => "1881", "source_text_year" => "1900", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/grube-ryby-komedja-w-trzech-aktach,MjkxMjEx/6/#info:metadata"],
            "34" => ["author_gender" => "Mężczyzna", "author" => "Świętochowski Aleksander", "title" => "Ojciec Makary", "text_type" => "dramat", "period" => "1876-1902", "first_edition_year" => "1876", "source_text_year" => "1876", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item/ojciec-makary-dramat-w-3ch-aktach-oraz-niewinni-i-antea,MTc0NTcxNzY/4/"],
            "35" => ["author_gender" => "Mężczyzna", "author" => "Lubowski Edward", "title" => "Pogodzeni z losem", "text_type" => "komedia", "period" => "1876-1903", "first_edition_year" => "1878", "source_text_year" => "1878", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/pogodzeni-z-losem-komedya-w-5-ciu-aktach,MTYxNjg4/3/#info:metadata"],
            "36" => ["author_gender" => "Mężczyzna", "author" => "Bliziński Józef", "title" => "Pan Damazy", "text_type" => "komedia", "period" => "1876-1904", "first_edition_year" => "1877", "source_text_year" => "1925", "release_location" => "Złoczów", "source_text_url" => "https://polonapl/item/pan-damazy-komedja-w-4-aktach,ODM1NTYzNzk/4/#info:metadata"],
            "37" => ["author_gender" => "Mężczyzna", "author" => "Gawalewicz Marian", "title" => "Guzik", "text_type" => "komedia", "period" => "1876-1905", "first_edition_year" => "1887", "source_text_year" => "1890", "release_location" => "Warszawa", "source_text_url" => "https://sbcorgpl/dlibra/publication/365417/edition/345159/content"],
            "38" => ["author_gender" => "Mężczyzna", "author" => "Gawalewicz Marian", "title" => "Dzisiejsi", "text_type" => "komedia", "period" => "1876-1906", "first_edition_year" => "1885", "source_text_year" => "1890", "release_location" => "Warszawa", "source_text_url" => "https://sbcorgpl/dlibra/publication/365417/edition/345159/content"],
            "39" => ["author_gender" => "Kobieta", "author" => "Zapolska Gabriela", "title" => "Żabusia", "text_type" => "sztuka w 3 aktach", "period" => "1876-1907", "first_edition_year" => "1897", "source_text_year" => "1903", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/teatr-gabryeli-zapolskiej-t-5-zabusia-dziewiczy-wieczor,Mjk0NTk0MDY/4/#info:metadata"],
            "40" => ["author_gender" => "Mężczyzna", "author" => "Kisielewski Jan August ", "title" => "Karykatury", "text_type" => "studium sceniczne", "period" => "1876-1908", "first_edition_year" => "1899", "source_text_year" => "1903", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/karykatury-komedya,ODM1NTgzNzg/1/#info:metadata"],
            "41" => ["author_gender" => "Mężczyzna", "author" => "Przybyszewski Stanisław", "title" => "Dla szczęścia", "text_type" => "dramat (sztuka)", "period" => "1876-1909", "first_edition_year" => "1899", "source_text_year" => "1900", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/dla-szczescia,MTMxMjcwMjM/6/"],
            "42" => ["author_gender" => "Mężczyzna", "author" => "Neuwert-Nowaczyński Adolf", "title" => "Dom kalek", "text_type" => "komedia", "period" => "1876-1910", "first_edition_year" => "1900", "source_text_year" => "1903", "release_location" => "Lwów - Warszawa ", "source_text_url" => "https://polonapl/item/siedem-dramatow-jednoaktowych-adolfa-neuwerta-nowaczynskiego,MjQ2OTAwNTY/153/#info:metadata"],
            "43" => ["author_gender" => "Mężczyzna", "author" => "Perzyński Włodzimierz", "title" => "Aszantka", "text_type" => "komedia (sztuka)", "period" => "1901-1939", "first_edition_year" => "1906", "source_text_year" => "1907", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/aszantka-komedya-w-3-aktach,Mjc3MjQx/6/#info:metadata"],
            "44" => ["author_gender" => "Kobieta", "author" => "Zapolska Gabriela", "title" => "Moralność pani Dulskiej", "text_type" => "tragifarsa kołtuńska (komedia)", "period" => "1901-1940", "first_edition_year" => "1906", "source_text_year" => "1907", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/moralnosc-pani-dulskiej-komedya-w-trzech-aktach,MTM5NDA5/7/#index"],
            "45" => ["author_gender" => "Mężczyzna", "author" => "Rittner Tadeusz", "title" => "W małym domku", "text_type" => "sztuka (dramat)", "period" => "1901-1941", "first_edition_year" => "1904", "source_text_year" => "1907", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/w-malym-domku-dramat-w-3-aktach,MjQ4NTYy/4/#info:metadata"],
            "46" => ["author_gender" => "Mężczyzna", "author" => "Perzyński Włodzimierz", "title" => "Lekkomyślna siostra", "text_type" => "komedia", "period" => "1901-1942", "first_edition_year" => "1904", "source_text_year" => "1907", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/lekkomyslna-siostra-komedya-w-czterech-aktach,NzU4NjU1NTQ/8/#index"],
            "47" => ["author_gender" => "Mężczyzna", "author" => "Neuwert-Nowaczyński Adolf", "title" => "Cyrce Mańkowska", "text_type" => "utwór dramatyczny", "period" => "1901-1943", "first_edition_year" => "1903", "source_text_year" => "1903", "release_location" => "Lwów – Warszawa", "source_text_url" => "https://polonapl/item/siedem-dramatow-jednoaktowych-adolfa-neuwerta-nowaczynskiego,MjQ2OTAwNTY/153/#info:metadata"],
            "48" => ["author_gender" => "Mężczyzna", "author" => "Rostworowski Karol Hubert", "title" => "Niespodzianka", "text_type" => "Prawdziwe zdarzenie (dramat)", "period" => "1901-1944", "first_edition_year" => "1929", "source_text_year" => "1929", "release_location" => "Kraków", "source_text_url" => "https://polonapl/item/niespodzianka-prawdziwe-zdarzenie-w-4-ech-aktach,ODk3ODE5OTQ/22/#item"],
            "49" => ["author_gender" => "Kobieta", "author" => "Markowska Maria", "title" => "W zimową noc", "text_type" => "obrazek sceniczny", "period" => "1901-1945", "first_edition_year" => "1913", "source_text_year" => "1918", "release_location" => "Warszawa", "source_text_url" => "https://polonapl/item/w-zimowa-noc-obrazek-sceniczny-w-4-odslonach-maryi-markowskiej,OTgwODc4ODc/4/#index"],
            "50" => ["author_gender" => "Mężczyzna", "author" => "Janczowicz-Terlecki Mieczysław", "title" => "Jesteśmy gotowi", "text_type" => "obrazek sceniczny", "period" => "1901-1946", "first_edition_year" => "1939", "source_text_year" => "1939", "release_location" => "Lwów", "source_text_url" => "https://polonapl/item/jestesmy-gotowi-obrazek-sceniczny,OTY0NjkwNjM/5/#index"],
        ];
        return $map[$id];
    }

    // Function to convert Roman numerals to Arabic numbers
    function romanToArabic($roman)
    {
        $roman = strtoupper($roman); // Ensure the input is in uppercase
        $roman_map = [
            'I' => 1,
            'V' => 5,
            'X' => 10,
            'L' => 50,
            'C' => 100,
            'D' => 500,
            'M' => 1000
        ];

        $arabic = 0;
        $previous_value = 0;

        // Loop through each character of the Roman numeral string
        for ($i = strlen($roman) - 1; $i >= 0; $i--) {
            $current_value = $roman_map[$roman[$i]];

            // If current value is less than the previous one, subtract it, else add it
            if ($current_value < $previous_value) {
                $arabic -= $current_value;
            } else {
                $arabic += $current_value;
            }

            $previous_value = $current_value;
        }

        return $arabic;
    }

    // Function to extract the "AKT" Roman numeral and convert it
    function extractAndConvertAkt($string)
    {
        // Use a regular expression to match the pattern "AKT_<RomanNumeral>"
        if (preg_match('/AKT_([IVXLCDM]+)/', $string, $matches)) {
            // $matches[1] contains the Roman numeral part
            $roman_numeral = $matches[1];
            // Convert the Roman numeral to Arabic
            return $this->romanToArabic($roman_numeral);
        } else {
            // Return null or a default value if no match is found
            return null;
        }
    }

    // Function to extract the "AKT" Roman numeral and convert it
    function extractAndConvertScena($string)
    {
        // Use a regular expression to match the pattern "AKT_<RomanNumeral>"
        if (preg_match('/SCENA_([IVXLCDM]+)/', $string, $matches)) {
            // $matches[1] contains the Roman numeral part
            $roman_numeral = $matches[1];
            // Convert the Roman numeral to Arabic
            return $this->romanToArabic($roman_numeral);
        } else {
            // Return null or a default value if no match is found
            return null;
        }
    }

    /**
     * @param $content
     * @param $doc
     * @param $report_id
     * @return void
     * @throws Exception
     */
    public function parseXmlContent($content, $doc,  $out_path)
    {
        $htmlStr = new HtmlStr2($content, true);
        $sql = "SELECT * FROM reports_annotations WHERE report_id = ?";
        $ans = $this->db->fetch_rows($sql, array($doc['id']));
        foreach ($ans as $a) {
            try {
                $htmlStr->insertTag(intval($a['from']), sprintf("<anb id=\"%d\" type=\"%s\"/>", $a['id'], $a['type']), $a['to'] + 1, sprintf("<ane id=\"%d\"/>", $a['id']), TRUE);
            } catch (Exception $ex) {
                $this->page->set("ex", $ex);
            }
        }
        $htmlStr = ReportContent::insertTokensWithTag($htmlStr, DbToken::getTokenByReportIdWitCTagSorted($doc['id']));

        $akt_number = $this->extractAndConvertAkt($doc["filename"]);
        $scena_number = $this->extractAndConvertScena($doc["filename"]);

        $meta = $this->getMetadata($doc["source"]);
        $content = $htmlStr->getContent();
        $metadata = "<document>" . "\n" .
            "<metadata>" . "\n" .
            "<author>" . $meta["author"] . "</author>" . "\n" .
            "<author_gender>" . $meta["author_gender"] . "</author_gender>" . "\n" .
            "<title>" . $meta["title"] . "</title>" . "\n" .
            "<text_type>" . $meta["text_type"] . "</text_type>" . "\n" .
            "<period>" . $meta["period"] . "</period>" . "\n" .
            "<first_edition_year>" . $meta["first_edition_year"] . "</first_edition_year> " . "\n" .
            "<source_text_year>" . $meta["source_text_year"] . "</source_text_year>" . "\n" .
            "<release_location>" . $meta["release_location"] . "</release_location>" . "\n" .
            "<source_text_url>" . $meta["source_text_url"] . "</source_text_url>" . "\n" .
            "<act_number>" . ($akt_number !== null ? $akt_number : '') . "</act_number>" . "\n" .
            "<scean_number>" . ($scena_number !== null ? $scena_number : '') . "</scean_number>" . "\n" .
            "<characters>" . "\n" .
            "<character></character>" . "\n" .
            "</characters>" . "\n" .
            "</metadata>" . "\n" .
            "<body>";

        $tag1open = "<message><author></author><content>";
        $tag1close = "</content></message>";

        $content = str_replace("utf8", "utf-8", $content);
        $content = str_replace("<body>", $metadata, $content);
        $content = str_replace("</body>", "</body>" . "\n" . "</document>", $content);
        $content = str_replace("<subtitle>", $tag1open, $content);
        $content = str_replace("</subtitle>", $tag1close, $content);
        $content = str_replace("<out>", $tag1open, $content);
        $content = str_replace("</out>", $tag1close, $content);
        $path = $out_path . "/" . $doc['id'] . ".txt";
        $this->saveFileToDisk($path, $content);
    }

    public function parseTextContent($content, $doc, $out_path)
    {
        $htmlStr = new HtmlStr2($content, true);
        $htmlStr = ReportContent::insertTokensWithTag($htmlStr, DbToken::getTokenByReportIdWitCTagSorted($doc['id']));
        $akt_number = $this->extractAndConvertAkt($doc["filename"]);
        $scena_number = $this->extractAndConvertScena($doc["filename"]);

        if(strpos($doc["filename"], "Spis") !== false){
            $akt_number = "0";
            $scena_number = "0";
        }

        $meta = $this->getMetadata($doc["source"]);
        $content = $htmlStr->getContent();

        $data =
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
            "<document>\n" .
            "<metadata>\n" .
            "<author>" . $meta["author"] . "</author>\n" .
            "<author_gender>" . $meta["author_gender"] . "</author_gender>\n" .
            "<title>" . $meta["title"] . "</title>\n" .
            "<text_type>" . $meta["text_type"] . "</text_type>\n" .
            "<period>" . $meta["period"] . "</period>\n" .
            "<first_edition_year>" . $meta["first_edition_year"] . "</first_edition_year>\n" .
            "<source_text_year>" . $meta["source_text_year"] . "</source_text_year>\n" .
            "<release_location>" . $meta["release_location"] . "</release_location>\n" .
            "<source_text_url>" . $meta["source_text_url"] . "</source_text_url>\n" .
            "<act_number>" . ($akt_number !== null ? $akt_number : '') . "</act_number>\n" .
            "<scean_number>" . ($scena_number !== null ? $scena_number : '') . "</scean_number>\n" .
            "<characters>\n" .
            "<character></character>\n" .
            "</characters>\n" .
            "</metadata>\n" .
            "<body>\n" .
            "<message><author></author><content>". $content ."</content></message>\n" .
            "</body>\n" .
            "</document>\n";

        $path = $out_path . "/" . $doc['id'] . ".txt";
        $this->saveFileToDisk($path, $data);
    }
    function saveFileToDisk($filePath, $data, $mode = 'w') {
        // Get the directory path from the file path
        $directoryPath = dirname($filePath);

        // Check if the directory exists, if not, create it
        if (!is_dir($directoryPath)) {
            // Attempt to create the directory with 0755 permissions (read/write/execute for owner, read/execute for others)
            if (!mkdir($directoryPath, 0755, true)) {
                return "Failed to create directory.";
            }
        }

        // Open the file with the specified mode ('w' for write, 'a' for append)
        $file = fopen($filePath, $mode);

        // Check if the file was opened successfully
        if ($file === false) {
            return "Failed to open file.";
        }

        // Write data to the file
        $result = fwrite($file, $data);

        // Close the file
        fclose($file);

        // Check if the write operation was successful
        if ($result === false) {
            return "Failed to write to file.";
        } else {
            return "File saved successfully at $filePath.";
        }
    }
}