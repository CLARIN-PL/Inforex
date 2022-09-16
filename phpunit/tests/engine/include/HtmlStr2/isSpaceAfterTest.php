<?php 

final class isSpaceAfterTest extends PHPUnit_Framework_TestCase {

    public function testForEmptyString()
    {
        // $tags tutaj wygląda tak:
        //  [0] => array( HtmlChar(''),HtmlChar('') )
        // dla pustego napisu zwraca zawsze False
        //  wynika to z tego, że dla count($tags)=1 tylko $pos=0
        // uruchomi wyszukiwanie w pętli foreach. 
        // Sprawdzone powinny zostać wszystkie składniki elementu $tags[1],
        // którego po prostu nie ma ( mamy tylko $tags[0] )
        $str = '';
        $recognizeTags = True; //  default 
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False; 
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

    } // testForEmptyString()

    public function testForOneSpace()
    {
        // $tags tutaj wygląda tak:
        //  [0] => array( HtmlChar(''),HtmlChar(' '),HtmlChar('') )
        // dla napisu z pojedynczą spacją zwraca zawsze False
        //  wynika to z tego, że dla count($tags)=1 tylko $pos=0
        // uruchomi wyszukiwanie w pętli foreach.
        // Sprawdzone powinny zostać wszystkie składniki elementu $tags[1],
        // którego po prostu nie ma ( mamy tylko $tags[0] )
        $str = ' ';
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False; 
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

    } // testForOneSpace()

    public function testForMultiSpace()
    {
        // $tags tutaj wygląda tak:
        //  [0] => array(   HtmlChar(''),
        //                  HtmlChar(' '),...,HtmlChar(' '),  // $COUNT razy 
        //                  HtmlChar('') )
        // dla dowolnie długiego napisu z samych spacji zwraca zawsze False
        //  wynika to z tego, że dla count($tags)=1 tylko $pos=0
        // uruchomi wyszukiwanie w pętli foreach.
        // Sprawdzone powinny zostać wszystkie składniki elementu $tags[1],
        // którego po prostu nie ma ( mamy tylko $tags[0] )
        $COUNT = 12;
        $str = str_repeat(' ',$COUNT);
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False;
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

    } // testForMultiSpace()

    public function testForOneChar()
    {
        // $tags tutaj wygląda tak 
        // ( znaki widzialne nie wchodzą w $tags, ale rozdzielają $tags na
        //   części - tablice składowe ):
        //  [0] => array( HtmlChar('')),
        //  [1] => array( HtmlChar(''))
        // dla napisu z pojedynczym widzialnym znakiem zwraca True
        //  wynika to z tego, że dla count($tags)=2 i $pos=0 lub 1
        // uruchomi wyszukiwanie w pętli foreach.
        //  W sytuacji $pos=0 sprawdzone zostaną wszystkie składniki 
        // elementu $tags[1] i jeśli trafi tam na HtmlChar to zwraca True
        // dla większych $pos nie ma już elementu $tags do przeszukania
        //  dlatego będzie zawsze False
        $str = 'ą';
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - tu już zawsze False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));
        $pos = 2;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False; 
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));
        $pos = 2;
        $this->assertFalse($o->isSpaceAfter($pos));

        // w dosłownym rozumieniu isSpaceAfter to nie działa ok, bo nie
        // ma już spacji ( znaku niewidzialnego ) po znaku 'ą' na pozycji
        // $pos = 0, a dostajemy True 

    } // testForOneChar()

    public function testForMultiChars()
    {
        // $tags tutaj wygląda tak:
        // ( znaki widzialne nie wchodzą w $tags, ale rozdzielają $tags na
        //   części - tablice składowe ):
        //  [0] =>          array( HtmlChar('')),
        //  [1] =>          array(),
        //  ...                 // $COUNT-1 razy
        //  [$COUNT-1] =>   array(),
        //  [$COUNT]   =>   array( HtmlChar(''))
        // dla napisu z $COUNT znakami widzialnymi w zakresie $pos od 0
        // do $COUNT-1 będzie uruchomione wyszukiwanie foreach wśród 
        // elementów $tags[$pos+1]. Tylko w jednym wypadku zakończy się
        // ono sukcesem dla $pos=$COUNT-1, bo w tym elemencie $tags jest
        // tablica zawierajaca HtmlChar(''). 
        //  W tym jednym przypadku zwróci True.   
        $COUNT = 2;
        $str = str_repeat('ą',$COUNT);
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0; // identycznie będzie dla wszystkich $pos < $COUNT-1
        $this->assertFalse($o->isSpaceAfter($pos));
        // element z końcowym HtmlChar('')
        $pos = $COUNT-1;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = $COUNT;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False; 
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0; // identycznie będzie dla wszystkich $pos < $COUNT-1
        $this->assertFalse($o->isSpaceAfter($pos));
        // element z końcowym HtmlChar('')
        $pos = $COUNT-1;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = $COUNT;
        $this->assertFalse($o->isSpaceAfter($pos));

        // w dosłownym rozumieniu isSpaceAfter to nie działa ok, bo nie
        // ma już spacji ( znaku niewidzialnego ) po znaku 'ą' na pozycji
        // $COUNT-1, a dostajemy True

    } // testForMultiChars

    public function testForOneTag()
    {
        // $tags tutaj wygląda tak ( $chars = array() ):
        //  [0] => array(   HtmlChar(''), 
        //                  XmlTagPointer(HtmlTag('<tag>')),
        //                  HtmlChar(''))
        // dla napisu z pojedynczym tagiem zwraca zawsze False
        //  wynika to z tego, że dla count($tags)=1 tylko $pos=0
        // uruchomi wyszukiwanie w pętli foreach.
        // Sprawdzone powinny zostać wszystkie składniki elementu $tags[1],
        // którego po prostu nie ma ( mamy tylko $tags[0] )
        $str = '<tag>';
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem 
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));
 
        // bez rozpoznawania tagów 
        //  tu $tags wygląda jak przy 5-cio znakowym napisie '<tag>'
        //  opisanym powyżej: 6-cio elementowa tablica pustych tablic
        //  poza elementem pierwszym i ostatnim, gdzie będzie to tablica
        //  z pojedynczym elementem HtmlChar('')
        $recognizeTags = False; 
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0; // identycznie będzie dla wszystkich $pos < 5-1
        $this->assertFalse($o->isSpaceAfter($pos));
        // element z końcowym HtmlChar('')
        $pos = 5-1;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 5;
        $this->assertFalse($o->isSpaceAfter($pos));

        // w przypadku bez rozpoznawania znaków oznacza to problem,
        // identycznie jak przy Multichars, z dosłownym rozumieniem 
        // isSpaceAfter. Nie działa ok, bo nie ma już spacji ( znaku 
        // niewidzialnego ) po końcowym '>' na pozycji 4, a dostajemy True

    } // testForOneTag()

    public function testForMultiTag()
    {
        // $tags tutaj wygląda tak ( $chars = array() ):
        //  [0] => array(   HtmlChar(''),
        //                  XmlTagPointer(HtmlTag('<tag>')),
        //                  ...     // $COUNT razy
        //                  XmlTagPointer(HtmlTag('<tag>')),
        //                  HtmlChar(''))
        // dla napisu z wieloma tagami zwraca zawsze False
        //  wynika to z tego, że dla count($tags)=1 tylko $pos=0
        // uruchomi wyszukiwanie w pętli foreach.
        // Sprawdzone powinny zostać wszystkie składniki elementu $tags[1],
        // którego po prostu nie ma ( mamy tylko $tags[0] )
        $COUNT = 2;
        $str = str_repeat('<tag>',$COUNT);
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertFalse($o->isSpaceAfter($pos));
        // poza zakresem
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów
        //  tu $tags wygląda jak przy 5*$COUNT znakowym napisie 
        //  opisanym powyżej: 5*$COUNT+1  elementowa tablica pustych tablic,
        //  poza elementem pierwszym i ostatnim, gdzie będzie to tablica
        //  z pojedynczym elementem HtmlChar('')
        $recognizeTags = False;
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0; // identycznie będzie dla wszystkich $pos < 5*$COUNT-1 
        $this->assertFalse($o->isSpaceAfter($pos));
        // element z końcowym HtmlChar('')
        $pos = 5*$COUNT-1;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 5*$COUNT;
        $this->assertFalse($o->isSpaceAfter($pos));

        // w przypadku bez rozpoznawania znaków oznacza to problem,
        // identycznie jak przy Multichars, z dosłownym rozumieniem
        // isSpaceAfter. Nie działa ok, bo nie ma już spacji ( znaku
        // niewidzialnego ) po końcowym '>' na pozycji 
        //      <długość_taga>*$COUNT-1  
        // a dostajemy True 

    } // testForMultiTag

    public function testForOneCharWithSpaceAfter()
    {
        // $tags tutaj wygląda tak
        // ( znaki widzialne nie wchodzą w $tags, ale rozdzielają $tags na
        //   części - tablice składowe ):
        //  [0] => array( HtmlChar('')),
        //  [1] => array( HtmlChar(' '),HtmlChar(''))
        // dla napisu z pojedynczym widzialnym znakiem zwraca True
        //  wynika to z tego, że dla count($tags)=2 i $pos=0 lub 1
        // uruchomi wyszukiwanie w pętli foreach.
        //  W sytuacji $pos=0 sprawdzone zostaną wszystkie składniki
        // elementu $tags[1] i jeśli trafi tam na HtmlChar to zwraca True
        // dla większych $pos nie ma już elementu $tags do przeszukania
        //  dlatego będzie zawsze False
        $str = 'ą ';
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - tu już zawsze False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));
 
        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False;
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

        // w dosłownym rozumieniu isSpaceAfter tu dla $pos=0 działa
        // ok, bo po 'ą' jest spacja. Ale identyczny wynik daje 
        // napis 'ą' bez spacji lub 'ąą' - czyli jednak niespójność

    } // testForOneCharWithSpaceAfter()

    public function testForSpaceBeforeChar()
    {
        // $tags tutaj wygląda tak
        // ( znaki widzialne nie wchodzą w $tags, ale rozdzielają $tags na
        //   części - tablice składowe ):
        //  [0] => array( HtmlChar(''),HtmlChar(' ')),
        //  [1] => array( HtmlChar(''))
        // dla napisu z pojedynczym widzialnym znakiem zwraca True
        //  wynika to z tego, że dla count($tags)=2 i $pos=0 lub 1
        // uruchomi wyszukiwanie w pętli foreach.
        //  W sytuacji $pos=0 sprawdzone zostaną wszystkie składniki
        // elementu $tags[1] i jeśli trafi tam na HtmlChar to zwraca True
        // dla większych $pos nie ma już elementu $tags do przeszukania
        //  dlatego będzie zawsze False
        $str = ' ą';
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - tu już zawsze False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False;
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 1;
        $this->assertFalse($o->isSpaceAfter($pos));

        // w dosłownym rozumieniu isSpaceAfter to nie działa ok, bo nie
        // ma już spacji ( znaku niewidzialnego ) po znaku ' ' na pozycji
        // $pos = 0, a dostajemy True

    } // testForSpaceBeforeChar

    public function testForSpaceBetweenChars()
    {
        // $tags tutaj wygląda tak
        // ( znaki widzialne nie wchodzą w $tags, ale rozdzielają $tags na
        //   części - tablice składowe ):
        //  [0] => array( HtmlChar('')),
        //  [1] => array( HtmlChar(' ')),
        //  [2] => array( HtmlChar(''))
        // dla napisu z pojedynczym widzialnym znakiem zwraca True
        //  wynika to z tego, że dla count($tags)=3 i $pos=0 do 2
        // uruchomi wyszukiwanie w pętli foreach.
        //  W sytuacji $pos=0 lub 1 sprawdzone zostaną wszystkie składniki
        // elementu $tags[1] lub $tags[2] i jeśli trafi tam na HtmlChar 
        // to zwraca True
        // dla większych $pos nie ma już elementu $tags do przeszukania
        //  dlatego będzie zawsze False
        $str = 'a ą';
        $recognizeTags = True; //  default
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        $pos = 1;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - tu już zawsze False
        $pos = 2;
        $this->assertFalse($o->isSpaceAfter($pos));

        // bez rozpoznawania tagów - $tags identyczny
        $recognizeTags = False;
        $o = new HtmlStr2($str,$recognizeTags);
        $pos = 0;
        $this->assertTrue($o->isSpaceAfter($pos));
        $pos = 1;
        $this->assertTrue($o->isSpaceAfter($pos));
        // poza zakresem - też False
        $pos = 2;
        $this->assertFalse($o->isSpaceAfter($pos));

        // w dosłownym rozumieniu isSpaceAfter to nie działa ok, bo zarówno
        // po znaku 'a' na pozycji 0 jak i po znaku ' ' na pozycji 1 zwróci
        // True, choć w tym drugim wypadku EWIDENTNIE znakiem następnym nie
        // jest spacja, tylko 'ą'

    } // testForSpaceBetweenChars()

} // isSpaceAfterTest class

?>
