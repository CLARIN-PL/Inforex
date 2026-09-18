<?php
// Run in the PHP application container against an empty, explicitly created
// disposable database inforex_metadata_regression. Never writes the app database.
require dirname(__DIR__,2).'/engine/settings.php';
Config::Cfg()->put_localConfigFilename(dirname(__DIR__,2).'/config/config.local.php');
$d=Config::Cfg()->get_dsn();$d['database']='inforex_metadata_regression';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
$m=new mysqli($d['hostspec'],$d['username'],$d['password'],$d['database'],(int)($d['port']??3306));
$m->set_charset('utf8mb4');
if($m->query('SHOW TABLES')->num_rows)throw new Exception('The disposable database must be empty');
$m->query('CREATE TABLE corpora(id INT PRIMARY KEY,ext VARCHAR(64)) ENGINE=InnoDB');
$m->query('CREATE TABLE reports(id INT PRIMARY KEY,corpora INT,title VARCHAR(12)) ENGINE=InnoDB');
$m->query("CREATE TABLE metadata_extra(id INT PRIMARY KEY,custom_field VARCHAR(8) COMMENT 'Custom###Synthetic', klasyfikacja_podstawowa ENUM('Tekst literacki prozą','Tekst literacki wierszowany','Literatura stosowana') NULL COMMENT 'Classification###Synthetic', required_choice ENUM('A','B') NOT NULL DEFAULT 'A' COMMENT 'Required###Synthetic', empty_choice ENUM('','A') NULL COMMENT 'Empty###Synthetic') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$m->query("INSERT INTO corpora VALUES(157,'metadata_extra'),(158,'metadata_extra')");
$m->query("INSERT INTO reports VALUES(1,157,'Original1'),(2,157,'Original2'),(3,158,'Other')");
$m->query("INSERT INTO metadata_extra(id,custom_field) VALUES(1,'old1'),(2,'old2'),(3,'other')");
// Match the application: legacy MDB2 converts PEAR errors into DatabaseException.
mysqli_report(MYSQLI_REPORT_OFF);
$db=new Database($d,false);$passed=[];
function state(){global $m;return json_encode([$m->query('SELECT * FROM reports ORDER BY id')->fetch_all(MYSQLI_ASSOC),$m->query('SELECT * FROM metadata_extra ORDER BY id')->fetch_all(MYSQLI_ASSOC)]);}
function expect($ok,$name){global $passed;if(!$ok)throw new Exception($name);$passed[]=$name;}
DbCorpus::batchUpdateMetadata(157,['1_Title'=>['value'=>'Changed'],'2_custom_field'=>['value'=>'new']]);
expect($m->query('SELECT title FROM reports WHERE id=1')->fetch_row()[0]==='Changed'&&$m->query('SELECT custom_field FROM metadata_extra WHERE id=2')->fetch_row()[0]==='new','basic_and_extended_fields_commit');
$before=state();$failed=false;
try{DbCorpus::batchUpdateMetadata(157,['1_Title'=>['value'=>'Partial'],'2_custom_field'=>['value'=>str_repeat('x',100)]]);}catch(Exception $e){$failed=true;}
expect($failed&&state()===$before,'database_error_rolls_back_all_fields');
$failed=false;try{DbCorpus::batchUpdateMetadata(157,['1_Title'=>['value'=>'Partial'],'3_Title'=>['value'=>'Wrong']]);}catch(Exception $e){$failed=true;}
expect($failed&&state()===$before,'other_corpus_document_rejected_without_writes');
$failed=false;try{DbCorpus::batchUpdateMetadata(157,['1_unknown_field'=>['value'=>'Wrong']]);}catch(Exception $e){$failed=true;}
expect($failed&&state()===$before,'unknown_field_rejected_without_writes');
foreach(['Tekst literacki prozą','Tekst literacki wierszowany','Literatura stosowana'] as $value){
    DbCorpus::batchUpdateMetadata(157,['1_klasyfikacja_podstawowa'=>['value'=>$value]]);
    expect($m->query('SELECT klasyfikacja_podstawowa FROM metadata_extra WHERE id=1')->fetch_row()[0]===$value,'enum_option_round_trip_'.$value);
}
DbCorpus::batchUpdateMetadata(157,['1_klasyfikacja_podstawowa'=>['value'=>'']]);
expect($m->query('SELECT klasyfikacja_podstawowa FROM metadata_extra WHERE id=1')->fetch_row()[0]===null,'cleared_nullable_enum_is_sql_null');
DbCorpus::batchUpdateMetadata(157,['1_klasyfikacja_podstawowa'=>['value'=>null],'1_empty_choice'=>['value'=>'']]);
expect($m->query('SELECT empty_choice FROM metadata_extra WHERE id=1')->fetch_row()[0]==='','explicit_empty_enum_member_preserved');
foreach(['Unknown',1,false,'Tekst literacki prozą '] as $value){
    $before=state();$failed=false;
    try{DbCorpus::batchUpdateMetadata(157,['1_Title'=>['value'=>'Partial'],'1_klasyfikacja_podstawowa'=>['value'=>$value]]);}catch(UserDataException $e){$failed=strpos($e->getMessage(),'klasyfikacja_podstawowa')!==false&&strlen($e->getMessage())<200;}
    expect($failed&&state()===$before,'invalid_enum_rejected_concisely_without_partial_write_'.gettype($value).'_'.json_encode($value));
}
foreach(['',null,'Unknown'] as $value){
    $before=state();$failed=false;
    try{DbCorpus::batchUpdateMetadata(157,['1_required_choice'=>['value'=>$value]]);}catch(UserDataException $e){$failed=true;}
    expect($failed&&state()===$before,'required_enum_rejects_'.json_encode($value));
}
echo json_encode(['passed'=>count($passed),'checks'=>$passed])."\n";
