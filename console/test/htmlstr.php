<?

mb_internal_encoding("UTF-8");

require_once("../../engine/config.php");
require_once("../../engine/config.local.php");
require_once("../../engine/include.php");
ob_end_clean();

$text = "Ala &amp; ma <b>kota</b><eos/>!";
$hs = new HtmlStr($text);

for ($i=0; $i<mb_strlen($text); $i++){
	printf("[%2d] = %s \n", $i, mb_substr($text, $i, 1));
}

echo "\nForward\n";

do {
	while ($tag = $hs->skipTag())
		echo "  T: $tag \n";
	$n = $hs->n;
	$zn = $hs->consumeCharacter();
	if ($zn){
		printf("m=%2d, [%2d] = $zn \n", $hs->m, $n, $zn);
	}
}while($zn);

echo "{$hs->m} [{$hs->n}])\n";

echo "\nBackward\n";

$hs->n = 31;

do {
	while ($tag = $hs->skipTagBackward())
		echo "T: $tag \n";
	$zn = $hs->consumeCharacterBackward();
	if ($zn){
		printf("m=%2d, [%2d] = $zn \n", $hs->m, $hs->n, $zn);
	}
}while($zn);


?>