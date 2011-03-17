<?

include("../../engine/include/utils/CMyDomDocument.php");

$xml = '<text type="suicide notes">                            
<opener>                            
<dateline>                            
</dateline>                            
<opening rend="center">Żegnam !</opening>                            
</opener>                            
<front>          
  <l rend="indent"><corr sic="Nie nawidzę">Nienawidzę</corr> siebie, a <corr sic="wogóle">w ogóle</corr> wszystkich !</l>              
</front>                          
<p>                            
Nie dam rady tak dalej żyć ! Już wiem<lb/>                            
jak to było , śniło mi się to!!! Wiem ,<lb/>                            
że to grzech (<corr sic="samobujstwo">samobójstwo</corr>) lecz boże<lb/>                            
musiałam proszę <corr sic="pomuż">pomóż</corr> mi , przy tobie<lb/>                            
będę bezpieczna. Wszyscy w kółko kłamią.<lb/>                             
a kiedy ja mówię prawdę to nikt mi nie<lb/>                             
chce wierzyć. Teraz już wiem, że sąd nie<lb/>                            
jest sprawiedliwy ! I po <corr sic="huj">chuj</corr> ja mówiłam<lb/>                          
prawdę ! <corr sic="Nie płacie">Nie płaczcie</corr> za mną ja sobie<lb/>                            
  tylko pomogę ! Kiedyś <corr sic="przecierz">przecież</corr> się zobaczymy.</p>                    
<p>  Kocham was !</p>                    
<p>                  
  Delfina i <corr sic="wogóle">w ogóle</corr> cała <corr sic="dwujka">dwójka</corr><lb/>                            
pamiętajcie, że nie <corr sic="zapomne">zapomnę</corr> o was !</p>                    
<p>                  
Policja to suki !</p>                        
<closer>                            
<p>Marta sorki ! No to tak jak<lb/>                             
mówiłam pa…</p>                  
</closer>                            
</text>';

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<candidate>
<candidatename id="candid">Robert</candidatename>
<candidatecity>New York</candidatecity>
<SSN>20072007</SSN>
<organization>XYZ Corp</organization>
</candidate>';

$xml = '<text>
	<body>
		 <opener>
		 	<dateline>2010</dateline>
		 </opener>
		 <p>To <add>jest</add> jakiś <add>paragraf</add> <corr resp="author">as</corr><lb/>.</p>
		 <p><b>To</b> jest <corr resp="author" type="one,two">paragraf</corr> <lb>asd</lb></p>
	</body>
</text>';

$c = new MyDOMDocument();
$c->loadXML($xml);
$c->schemaValidate("../../engine/resources/lps/lps.xsd");

print_r($c->errors);
?>