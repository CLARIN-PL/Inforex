<?

include("PEAR.php");
include("Text_Diff-1.1.1/Diff.php");

$text1 = "Ala ma kota\n\nMa";
$text2 = "<b>Ala</b> ma kota\nMa\nMaa";


$array1 = split("\n", $text1);
$array2 = split("\n", $text2);


$diff = new Text_Diff("xdiff", array($array1, $array2));
print_r($diff->getDiff());


?>