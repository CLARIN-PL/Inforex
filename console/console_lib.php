<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-03-27
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

/**
 * Drukuje tabelę z wynikami.
 * 
 * Format tabeli:
 * $evaluation['TYPE1']->tp    // True Positives for TYPE1 annotation 
 * $evaluation['TYPE1']->fp    // False Positives for TYPE1 annotation 
 * $evaluation['TYPE1']->fn    // False Negatives for TYPE1 annotation 
 * $evaluation['TYPE2']->tp    // True Positives for TYPE2 annotation
 * ... 
 */
function print_summary_table($evaluation){
	$line_border = $line_header = $line_samples = $line_tp = $line_fp = $line_fn = $line_p = $line_r = array();
	foreach ($evaluation as $k=>$v){
		$len = strlen($k);
		$line_border[] = str_repeat("=", $len);
		$line_header[] = $k;
		$line_samples[] = sprintf("%{$len}d", $v->tp+$v->fn);
		$line_tp[] = sprintf("%{$len}d", $v->tp);
		$line_fp[] = sprintf("%{$len}d", $v->fp);
		$line_fn[] = sprintf("%{$len}d", $v->fn);
		$p = $v->tp/($v->tp+$v->fp)*100;
		$r = $v->tp/($v->tp+$v->fn)*100;
		$line_p[] = sprintf("%{$len}.2f", $p);
		$line_r[] = sprintf("%{$len}.2f", $r);
		$line_f[] = sprintf("%{$len}.2f", 2*$r*$p/($r+$p));		
	}		
	
	echo "\n";				
	echo sprintf("=================== %s\n", implode(" ", $line_border));
	echo sprintf("**Annotation type** %s\n", implode(" ", $line_header));
	echo sprintf("=================== %s\n", implode(" ", $line_border));
	echo sprintf("**Samples**         %s\n", implode(" ", $line_samples));
	echo sprintf("**True  Positive**  %s\n", implode(" ", $line_tp));
	echo sprintf("**False Positive**  %s\n", implode(" ", $line_fp));
	echo sprintf("**False Negative**  %s\n", implode(" ", $line_fn));
	echo sprintf("**Precision**       %s\n", implode(" ", $line_p));	
	echo sprintf("**Recall**          %s\n", implode(" ", $line_r));	
	echo sprintf("**F1-measure**      %s\n", implode(" ", $line_f));	
	echo sprintf("=================== %s\n", implode(" ", $line_border));	
} 

/**
 * Zamienia wielkość znaków w tekście -- pierwszy znak duży, pozostałe małe.
 */
function my_ucfirst($string, $e ='utf-8') {
    if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
        $string = mb_strtolower($string, $e);
        $upper = mb_strtoupper($string, $e);
            preg_match('#(.)#us', $upper, $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e);
    }
    else {
        $string = ucfirst($string);
    }
    return $string;
}  
?>
