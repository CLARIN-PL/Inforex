<?php
/*
 * Created on 2009-02-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/*** Hack for multiple database selection */
//$dbName = "inforex_180704";
$dbName = "inforex_tmp_backup_181003";
if ( isset($_GET['db']) ){
	$dbName = $_GET['db'];
	$_SESSION['db'] = $dbName;
} else if ( isset($_SESSION['db']) ){
	$dbName = $_SESSION['db'];
}
/*** Hack end */

// Server configuration
$config->path_engine = '/home/czuk/nlp/eclipse/workspace_inforex/inforex_web/engine';
$config->path_www    = '/home/czuk/nlp/eclipse/workspace_inforex/inforex_web/public_html';
$config->path_liner	 = '/nlp/eclipse/workspace_inforex/inforex_liner';
$config->path_liner2 = '/nlp/eclipse/workspace_liner2/liner2';
$config->path_python = '/home/czuk/dev/python/2.6/bin/python';
$config->path_python = 'python';
$config->file_with_rules = '/nlp/eclipse/workspace_inforex/semquel/transformations-common.ccl';
$config->path_semql  = '/nlp/eclipse/workspace_inforex/semquel';
$config->path_wcrft  = '/nlp/eclipse/workspace_common/wcrft';
$config->path_wcrft_model = '/nlp/resources/model_nkjp10_wcrft_s2';
$config->wcrft_config = 'nkjp_s2.ini';

$config->takipi_wsdl = 'http://nlp.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl';
$config->liner_wsdl = 'http://localhost/nerws/ws/nerws.wsdl';
$config->serel_liner_wsdl = 'http://188.124.184.105/nerws/ws/nerws.wsdl';
$config->path_secured_data = '/home/czuk/nlp/eclipse/workspace_inforex/inforex_web/secured_data';
$config->url = 'http://188.124.184.105/inforex';  

$config->log_sql = false;
$config->log_output = "chrome_php";
//$config->offline = true;

$config->dsn = array(
    			'phptype'  => 'mysql',
    			'username' => 'root',
    			'password' => 'krasnal',
    			'hostspec' => 'localhost',
    			'database' => $dbName
				);

$config->relation_marks_db = array(
    			'phptype'  => 'mysql',
    			'username' => 'root',
    			'password' => 'krasnal',
    			'hostspec' => 'localhost',
    			'database' => 'serel_wikipedia_1'
				);

$config->dsn_questions = array(
    			'phptype'  => 'mysql',
    			'username' => 'root',
    			'password' => 'krasnal',
    			'hostspec' => 'localhost',
    			'database' => 'serel_questions'
				);
				
$config->wmbt_cmd = '/nlp/workdir/wmbt/wmbt/wmbt.py -d /home/czuk/nlp/workdir/wmbt/model_nkjp10_guess /home/czuk/nlp/workdir/wmbt/config/nkjp-guess.ini';

/** Ścieżki do programów */

$config->path_semquel = '/nlp/eclipse/workspace_inforex/semquel/semquel-analyze-wcrft-local.sh';

$sql_log = false;

// $config->liner2_api[] = array(
// 			"name" => "Liner2-bis",
// 			"type" => "granice nazw własnych (projekt Agora)",
// 			"wsdl" => "http://localhost/nerws/ws/nerws.wsdl",
// 			"model" => "names",
// 			"description" => "");
// $config->liner2_api[] = array(
// 			"name" => "Minos",
// 			"type" => "czasowniki z podmiotem domyślnym",
// 			"wsdl" => "http://localhost/nerws/ws/nerws.wsdl",
// 			"model" => "minos",
// 			"annotations" => array("wyznacznik_null_verb"),
// 			"description" => "");
// $config->liner2_api[] = array(
// 			"name" => "Spatial",
// 			"type" => "spatial entities",
// 			"wsdl" => "http://localhost/nerws/ws/nerws.wsdl",
// 			"model" => "spatial",
// 			"annotations" => array("facility", "spatial_indicator", "landmark", "region", 
// 									"nam_loc", "nam_fac", "nam_org", "nam_pro", "nam_liv",
// 									"verb_located"),
// 			"description" => "");

// $config->liner2_api = array();			
// $config->liner2_api[] = array(
// 			"name" => "Liner2-litmap",
// 			"type" => "nam_adj, nam_eve, nam_fac_bridge, nam_fac_goe, nam_fac_goe_stop, nam_fac_park, nam_fac_road, nam_fac_square, nam_fac_system, nam_liv, nam_loc, nam_loc_astronomical, nam_loc_country_region, nam_loc_gpe_admin1, nam_loc_gpe_admin2, nam_loc_gpe_admin3, nam_loc_gpe_city, nam_loc_gpe_conurbation, nam_loc_gpe_country, nam_loc_gpe_district, nam_loc_gpe_subdivision, nam_loc_historical_region, nam_loc_hydronym, nam_loc_land, nam_num, nam_org, nam_othnam_pro",
// 			"wsdl" => "http://localhost/nerws/ws/nerws.wsdl",
// 			"model" => "litmap",
// 			"description" => "Kategorie jednostek identyfikacyjnych dla Mapy Literackiej");

$config->wccl_match_enable = true;
$path = "/nlp/corpora/pwr/kpwr-release";
$config->wccl_match_corpora = array(
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; all (1&ndash;551)", 
					"path"=>"$path/kpwr-1.2.2-time-disamb/index_time_train.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; A (1&ndash;100)", 
					"path"=>"$path/index_time_a.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; B (101&ndash;200)", 
					"path"=>"$path/index_time_b.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; C (201&ndash;300)", 
					"path"=>"$path/index_time_c.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; D (301&ndash;551)", 
					"path"=>"$path/index_time_d.txt"),
			array("name"=>"KPWr 1.2.2 TimeML tune",
					"path"=>"$path/index_time_tune.txt"),
			array("name"=>"KPWr 1.2.7 TimeML train&ndash; all",	"path"=>"/index_time_train.txt")
		); 	

?>
