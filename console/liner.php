<?

$path = "/nlp/eclipse/workspace_inforex/inforex_liner/production";

$cmd = "LANG=en_US.utf-8; java -cp {$path}/lingpipe-3.8.2.jar -jar {$path}/liner.jar batch";

$text = "Pani pani subst:sg:nom:f  Kamila kamil subst:sg:gen:m1  Nowa nowy adj:sg:nom:f:pos  ma mieć fin:sg:ter:imperf  kota kot subst:sg:nom:f";
$model = "{$path}/../crf_model.bin";
$cmd = sprintf("LANG=en_US.utf-8; java -Djava.library.path={$path}/lib -jar {$path}/liner.jar tag '%s' -chunker crfpp-load:%s", $text, $model);

$cmd_result = shell_exec($cmd);

echo $cmd_result;	


?>