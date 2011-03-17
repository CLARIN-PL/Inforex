<?
include("../../engine/include/utils/CDiffFormatter.php");
$df = new DiffFormatter();

$text1 = "lini różni się białymi znakami   \n" .
		"      różna treść    \n" .
		" aaa \n".
		" aaa \n".
		" aaa \n".
		" a to jest usunięta linia \n".
		" aaa \n".
		" aaa \n".
		" takie same co do znaków   ";

$text2 = "   lini różni się białymi znakami     \n" .
		"   różna treść linii    \n" .
		" aaa \n".
		" aaa \n".
		" aaa \n".
		" takie same co do znaków   \n" .
		" aaa \n".
		" aaa \n".
		" i dodana linia   ";

print $df->diff($text1, $text2, true);
?>