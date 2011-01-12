<?
header("Content-type: text/html; charset=utf-8");
//error_reporting(E_ALL);

    include_once "Text/Diff.php";
    include_once "Text/Diff/Renderer.php";
    include_once "Text/Diff/Renderer/inline.php";
    include_once "Text/Diff/Renderer/context.php";
    include_once "Text/Diff/Renderer/unified.php";


function PrintDiffInline($pagename) {
  global $DiffShow,$DiffStartFmt,$TimeFmt,$DiffDelFmt,$DiffAddFmt,
    $DiffEndDelAddFmt,$DiffEndFmt,$DiffRestoreFmt,$FmtV, $LinkFunctions, $FarmD;
  $page = ReadPage($pagename);
  if (!$page) return;
  krsort($page); reset($page);
  $lf = $LinkFunctions;
  $LinkFunctions['http:'] = 'LinkSuppress';
  $LinkFunctions['https:'] = 'LinkSuppress';
  foreach($page as $k=>$v) {
    if (!preg_match("/^diff:(\d+):(\d+):?([^:]*)/",$k,$match)) continue;
    $diffclass = $match[3];
    if ($diffclass=='minor' && $DiffShow['minor']!='y') continue;
    $diffgmt = $FmtV['$DiffGMT'] = $match[1]; $FmtV['$DiffTime'] = strftime($TimeFmt,$diffgmt);
    $diffauthor = @$page["author:$diffgmt"];
    if (!$diffauthor) @$diffauthor=$page["host:$diffgmt"];
    if (!$diffauthor) $diffauthor="unknown";
    $FmtV['$DiffChangeSum'] = htmlspecialchars(@$page["csum:$diffgmt"]);
    $FmtV['$DiffHost'] = @$page["host:$diffgmt"];
    $FmtV['$DiffAuthor'] = $diffauthor;
    $FmtV['$DiffId'] = $k;
    echo FmtPageName($DiffStartFmt,$pagename);
    $difflines = explode("\n",$v."\n");
    $in=array(); $out=array(); $dtype='';
    foreach($difflines as $d) {
      if ($d>'') {
        if ($d[0]=='-' || $d[0]=='\\') continue;
        if ($d[0]=='<') { $out[]=substr($d,2); continue; }
        if ($d[0]=='>') { $in[]=substr($d,2); continue; }
      }
      if (preg_match("/^(\\d+)(,(\\d+))?([adc])(\\d+)(,(\\d+))?/",
          $dtype,$match)) {
        if (@$match[7]>'') {
          $lines='lines';
          $count=$match[1].'-'.($match[1]+$match[7]-$match[5]);
        } elseif ($match[3]>'') {
          $lines='lines'; $count=$match[1].'-'.$match[3];
        } else { $lines='line'; $count=$match[1]; }
        $FmtV['$DiffLines'] = $count;
        switch($match[4]) {
          case 'a':
            $txt = str_replace('line',$lines,$DiffDelFmt['a']);
            echo FmtPageName($txt,$pagename),
              "<div class='diffmarkup'><del>",
              str_replace("\n","<br />",htmlspecialchars(join("\n",$in))),
              "</del></div>";
            break;
          case 'd':
            $txt = str_replace('line',$lines,$DiffAddFmt['d']);
            echo FmtPageName($txt,$pagename),
              "<div class='diffmarkup'><ins>",
              str_replace("\n","<br />",htmlspecialchars(join("\n",$out))),
              "</ins></div>";
            break;
          case 'c':
            $txt = str_replace('line',$lines,$DiffAddFmt['c']);
            echo FmtPageName($txt,$pagename);
            require_once("$FarmD/cookbook/pagerevinline/Text/Diff.php");
            require_once("$FarmD/cookbook/pagerevinline/Text/Diff/Renderer/inline.php");
            $diff = new Text_Diff($in, $out);
            $renderer = new Text_Diff_Renderer_inline();
            echo "<div class='diffmarkup'>",
              str_replace("\n","<br />",$renderer->render($diff)),
              "</div>";
            break;
        }
        echo FmtPageName($DiffEndDelAddFmt,$pagename);
      }
      $in=array(); $out=array(); $dtype=$d;
    }
    echo FmtPageName($DiffEndFmt,$pagename);
    echo FmtPageName($DiffRestoreFmt,$pagename);
  }
  $LinkFunctions = $lf;
}


//$f1 = htmlspecialchars(file_get_contents('docu/snippets_c.html'));
//$f2 = htmlspecialchars(file_get_contents('docu/snippets_php.html'));

$f1 = "Ala ma kota\naa\naa";
$f2 = "Ala ma kota\n\naa\naa";

$lines1 = explode("\n",$f1);
$lines2 = explode("\n",$f2);

$diff     = new Text_Diff('xdiff', array($lines1, $lines2));
$r = new Text_Diff_Renderer(
    array(
        'leading_context_lines' => 0,
        'trailing_context_lines' => 0
    )
);

$r_context = new Text_Diff_Renderer_context(
    array(
        'leading_context_lines' => 1,
        'trailing_context_lines' => 1,
        'chg_prefix' => '<span class="change">',
        'chg_suffix' => '</span>'
    )
);

$r_inline = new Text_Diff_Renderer_inline(
    array(
        'leading_context_lines' => 1,
        'trailing_context_lines' => 1,
        'ins_prefix' => '<span class="added">',
        'ins_suffix' => '</span>',
        'del_prefix' => '<span class="deleted">',
        'del_suffix' => '</span>'
    )
);

$r_unified = new Text_Diff_Renderer_unified(
    array(
        'leading_context_lines' => 0,
        'trailing_context_lines' => 0,
        'ins_prefix' => '<span class="added">',
        'ins_suffix' => '</span>',
        'del_prefix' => '<span class="deleted">',
        'del_suffix' => '</span>'
    )
);

?>

<html>
  <head>
    <title>diff</title>
    <style type="text/css">
      .deleted {background-color:#ffdddd;}
      .added {background-color:#ddffdd;}
      .context {background-color: #ffffff;}
      .change {background-color:#ffffdd;}
      .new-block {border-top: 10px solid #ffffff;}
      table {border-width: 0; border-collapse:collapse; font-family: Monospace;}
      td {vertical-align: top;}
    </style>
  </head>
  <body>
    <h1>Default-Diff</h1>
    <pre>
<?php echo $r->render($diff); ?>
    </pre>
    <br>
    <br>
    <h1>Context-Diff</h1>
    <pre>
<?php echo $r_context->render($diff); ?>
    </pre>
    <br>
    <br>
    <h1>Inline-Diff</h1>
    <pre>
<?php echo$r_inline->render($diff); ?>
    </pre>
    <br>
    <br>
    <h1>Unified-Diff</h1>
    <pre>
<?php echo $r_unified->render($diff);?>
    </pre>
    
<?php echo gzdeflate($r_unified->render($diff), 9); ?>
<?
	$diff = $r_unified->render($diff);
	$deflate =  gzdeflate($diff, 9);
	echo strlen($diff) . " to " . strlen($deflate);
	echo "\n".gzinflate($deflate);
	echo xdiff_string_diff($text1, $text2, 0, true);
?>

  </body>
</html>