<?
//=================================================================
// Konfiguracja

$file_with_patterns = "/nlp/workdir/wzorce.4.6.wzorce3";
mb_internal_encoding("utf8");

class Node{
	static $gen = 1;
	
	var $id = null;
	var $text = null;
	var $nodes = array();
	var $incomming = array();
	
	function __construct($text){
		$this->text = $text;
		$this->id = Node::$gen;
		Node::$gen++;
	}

	function addNode(&$node){
		$this->nodes[] = $node;
		$node->incomming[] = &$this;
	}
	
	function relabel(){
		if ( in_array($this->text, array("NAM", "NUM") ) )
			$this->text = "is('{$this->text}')";
		elseif ( $this->text == "CITY_NAM" || $this->text == "CITY_NAM:loc")
			$this->text = "is('city_nam_gaz')";
		elseif ( in_array( $this->text, array("w", "nr", ".", "\"", "-", "-", ",", "&", "'", "z", "św", "o", "na", "i","do","dla")))
			$this->text = "equal(base, '".$this->text."')";
		else
			$this->text = "and( equal(base, '".$this->text."'), regex( orth, '[A-Ź][a-źA-Ź].*') )";
	}
		
	function equal(&$node){
		return $this->id == $node->id;
	}
	
	function hasNode(&$node){
		foreach ($this->nodes as $n)
			if ( $n->equal($node))
				return true;
		return false;
	}
	
	function removeNode(&$node){
		$this->nodes = $this->removeNodeFromArray($this->nodes, $node);
		$node->incomming = $this->removeNodeFromArray($node->incomming, $this);
	}
	
	static function removeNodeFromArray(&$array, &$node){
		$ar = array();
		foreach ($array as &$n)
			if (!$n->equal($node))
				$ar[] = $n;
		return $ar;
	}
}

// Siec wierzchołków
class NodeNet{

	// Index wierzchołków
	var $index = array();
	
	// Tablica wierzchołków początkowych
	var $start = array();
	
	// Start indent
	var $si = null;
	// End indent 
	var $ei = null;
	// Make indent
	var $in = null;

	function __construct(){
		$this->si = chr(10);
		$this->ei = chr(11);		
		$this->in = chr(12);
	}

	// Dodaje nową scieżkę
	function addPath($tokens){

		foreach ($this->start as $node){
			if ( NodeNet::isAlongPath($node, $tokens) )
				// Ścieżka pokrywa się z już istniejącą
				return; 
		}

		$token = array_shift($tokens);		
		$starting_node = null;			
		
		// Znajdź wierzchołek, który mółby być początkiem
		foreach ($this->index as $node){
			if ( $node->text == $token ){
				$starting_node = $node;
				break;
			}
		}
		
		if ( $starting_node == null ){		
			$starting_node = new Node($token);
			$this->start[] = &$starting_node;
			$this->index[] = &$starting_node;
		}
		
		$this->_addPath($starting_node, $tokens);
	}	
	
	function _addPath($start_node, $tokens){
		if (count($tokens)==0)
			return;
		
		// Znajdź pełne dopasowanie ścieżki
		foreach ($this->index as $node){
			if ( NodeNet::isAlongPath($node, $tokens) && !NodeNet::isOnPath($node, $start_node) ){
				$start_node->addNode($node);
				return; 
			}
		}
		
		$token = array_shift($tokens);
		$next_node = null;
		
		// Sprawdź przedłużenie ścieżki
		foreach ( $start_node->nodes as $n)
			if ( $n->text == $token && count($n->incomming)==1 ){
				$next_node = $n;
				break;
			}
			
		if ( $next_node == null ){		
			$new_node = new Node($token);
			$next_node = &$new_node;
			$this->index[] = $next_node;
			$start_node->addNode($next_node);
		}
		
		$this->_addPath($next_node, $tokens);
	}
	
	/**
	 * Sprawdza, czy sekwencja tokenów pokrywa się z daną ścieżką
	 */
	static function isAlongPath(&$node, $tokens){
		$token = array_shift($tokens);
		if ( $node->text == $token ){
			// Ciąg tokenów został skonsumowany i doszliśmy do pewnego wierzchołka
			if ( count($tokens) == 0 )
				return count($node->nodes)==0;
			elseif ( count($node->nodes) != 1 ) // nie ma gdzie przejść lub nie wiadomo gdzie
				return false;
			else
				return NodeNet::isAlongPath($node->nodes[0], $tokens);
		}
		else
			return false;
	}
	
	/**
	 * 
	 */
	static function isOnPath(&$path, $node){
		if ( $path->equal($node) )
			return true;
		else{
			foreach ($path->nodes as $n)
				if ($n->equal($node))
					return true;
			return false;
		}			
	}
	
	/**
	 * 
	 */
	function printTree(){
		foreach ($this->start as $node){
			echo "START:\n", $this->_printTree("  ", $node);
			echo "\n";
		};
	}
	
	function _printTree($prefix, $node){
		echo $node->text, " [", $node->id ,"|", count($node->incomming),"]";
		$i = 0;
		foreach ($node->nodes as $n){			
			if ( count($node->nodes) > 1 ){
				if ( $i++ == 0 )
					echo "\n";
				echo $prefix;
				$this->_printTree($prefix."  ", $n);
			}else{
				echo " ";
				$this->_printTree($prefix, $n);
			}
		}
		// Wypisz łamanie linii tylko dla ostatnich wierzchołków
		if ( count($node->nodes) == 0 )
			echo "\n";
	}
	
	function relabel(){
		foreach ($this->index as $n)
			$n->relabel();
	}
	
	function reduce(){		
		do{
			do{
				do {			
					$reduedSeq = $this->reduceSequence();
					$reduedPar = $this->reduceParallel();
					$reduedStart = $this->reduceStart();
					$reduedEnd = $this->reduceEnd();
				}while ( $reduedPar || $reduedSeq || $reduedStart || $reduedEnd);
			}while ($this->split());
		}while ($this->duplicate());
	}
	
	/**
	 * 
	 */
	function reduceSequence(){
		$reduced = false;
		foreach ($this->index as &$n){
			$reduced |= $this->_reduceSequence($n);
		}
		return $reduced;
	}
	
	function _reduceSequence(&$n){
		if ( count($n->nodes) == 1 && count($n->nodes[0]->incomming) == 1){
			$next_node = $n->nodes[0];
			$n->text .= ",{$this->in}".$next_node->text;
			
			$n->removeNode($next_node);			
			foreach ($next_node->nodes as &$ns){
				$next_node->removeNode($ns);
				$n->addNode($ns);
			}
			return true;
		}		
		return false;
	}
	
	/**
	 * Grupuje równoległe przejścia między parą wierzchołków
	 *    +- B -+
	 * A -+     +- D  =>  A -- (B|C) -- D
	 *    +- C -+
	 */
	function reduceParallel(){
		$reduced = false;
		foreach ($this->index as &$n){
			$redued |= $this->_reduceParallel($n);
		}		
		return $reduced;
	}
	
	function _reduceParallel(&$node){
		$second = array();
		
		foreach ($node->nodes as &$n){
			if ( count($n->nodes) == 1 && count($n->incomming) == 1 ){
				$second[$n->nodes[0]->id][] = $n;
			}
		}
	
		$reduced = false;	
		foreach ($second as $k=>$v)
		{
			if ( count($v) > 1 || $node->hasNode($v[0]->nodes[0]) ){
				
				$text = array();				
				foreach ($v as $n) $text[] = "variant({$this->si}".$n->text."{$this->ei})";
				
				if ( count($text) > 1)
					$text = "oneof({$this->si}" . implode(",{$this->in}", $text) . "{$this->ei})";
				else
					$text = $v[0]->text;
				
				// Sprawdź, czy przejście może być alternatywne
				if ( $node->hasNode($v[0]->nodes[0]) ){
					$node->removeNode($v[0]->nodes[0]);
					$text = "optional({$this->si}{$text}{$this->ei})";
				}
				
				$v[0]->text = $text;			
			
				for ($i=1; $i<count($v); $i++){
					$node->removeNode($v[$i]);
					$v[$i]->removeNode($v[0]->nodes[0]);
				}
					
				$reduced = true;
			}
		}
	
		return $reduced;
	}
	
	/**
	 * Zwija końce węzły w jeden.
	 *    +- B
	 * A -+     =>  A -- (B|C)
	 *    +- C
	 */
	function reduceEnd(){
		$reduced = false;
		foreach ($this->index as &$n){
			$redued |= $this->_reduceEnd($n);
		}		
		return $reduced;		
	}
	
	function _reduceEnd(&$node){
		$togroup = array();
		foreach ($node->nodes as $n)
			if ( count($n->nodes) == 0 && count($n->incomming) == 1)
				$togroup[] = $n;
		
		if ( count($togroup) > 1 ){
			$text = array();
			$i = 0;
			foreach ($togroup as $tg) {
				$text[] = "variant({$this->si}".$tg->text."{$this->ei})";
				if ( $i++ > 0 )
					$node->removeNode($tg);	
			}
			$togroup[0]->text = "oneof({$this->si}".implode(",{$this->in}", $text)."{$this->ei})";
			return true;
		}
		return false;		
	}

	/**
	 * Zwija końce węzły w jeden.
	 *    +- B
	 * A -+     =>  A -- (B|C)
	 *    +- C
	 */
	function reduceStart(){
		$reduced = false;
		foreach ($this->index as &$n){
			$redued |= $this->_reduceStart($n);
		}		
		return $reduced;		
	}
	
	function _reduceStart(&$node){
		$togroup = array();
		foreach ($node->incomming as &$n)
			if ( count($n->incomming) == 0 && count($n->nodes) == 1)
				$togroup[] = $n;
		
		if ( count($togroup) > 1 ){
			$text = array();
			$i = 0;
			foreach ($togroup as $tg) {
				$text[] = "variant({$this->si}".$tg->text."{$this->ei})";
				if ( $i++ > 0 ){
					$tg->removeNode($node);	
					$this->start = Node::removeNodeFromArray($this->start, $tg);
				}
			}
			$togroup[0]->text = "oneof({$this->si}".implode(",{$this->in}", $text)."{$this->ei})";			
			return true;
		}
		return false;		
	}
		
	/**
	 * Klonuje wierzchołki, które rozdzielają się na różne ścieżki.
	 * Wersja uproszczona, rozdziela wszystkie układy typu
	 *      +-- B          +- A -- B
	 * O -- A       =>  0 -+
	 *      +-- C          +- A -- C
	 */
	function split(){
		$reduced = false;
		foreach ($this->index as &$n){
			$redued |= $this->_split($n);
		}		
		return $reduced;				
	}

	function _split(&$node){
		if ( count($node->incomming) == 1 && count($node->nodes) == 1 ){
			$prev = $node->incomming[0];
			$next = $node->nodes[0];
			
			$link = array();
			foreach ($prev->nodes as $n)
				if (!$n->equal($node) && count($n->nodes) > 1 && ($n->hasNode($next)))
					$link[] = $n;
					
			if ( count($link) > 0){
				$text = array("variant({$this->si}".$node->text."{$this->ei})");
				foreach ($link as $l){
					$text[] = "variant({$this->si}".$l->text."{$this->ei})";
					$l->removeNode($next);
				}
				
				$node->text = "oneof({$this->si}" . implode(",{$this->in}", $text) . "{$this->ei})";
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 
	 */
	function duplicate(){
		$reduced = false;
		foreach ($this->index as &$n){
			$reduced |= $this->_duplicate($n);
		}		
		return $reduced;			
	}
	
	function _duplicate(&$node){
		if ( count($node->incomming) > 1){
			for ($i=1; $i<count($node->incomming); $i++){
				$in = $node->incomming[$i];
				$node->incomming[$i]->removeNode($node);
				$newNode = new Node($node->text);
				$in->addNode($newNode);
				foreach ($node->nodes as $link)
					$newNode->addNode($link);
				$this->index[] = &$newNode;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 */
	function duplicateEnds(){
		$reduced = false;
		foreach ($this->index as &$n){
			$reduced |= $this->_duplicateEnds($n);
		}		
		return $reduced;			
	}
	
	function _duplicateEnds(&$node){
		if ( count($node->incomming) > 1){
			for ($i=1; $i<count($node->incomming); $i++){
				$in = $node->incomming[$i];
				$node->incomming[$i]->removeNode($node);
				$newNode = new Node($node->text);
				$in->addNode($newNode);
				foreach ($node->nodes as $link)
					$newNode->addNode($link);
				$this->index[] = &$newNode;
			}
			return true;
		}
		return false;
	}	
	
	/**
	 * 
	 */
	function toString(){
		return implode("\n\n", $this->getRules());
	}
	
	function getRules(){
		$table = array();
		for ($j=0; $j<count($this->start); $j++){
			$rules = $this->_getRules($this->start[$j], "");
			
			foreach ($rules as $text){
				//$text = $this->start[$j]->text;
				$formated = "apply(\n match(\n  ";
				$indent = 2;
				for ($i=0; $i<strlen($text); $i++ ){
					$zn = $text[$i];
					if ($zn == $this->si)
						$formated .= "\n" . str_repeat(" ", ++$indent);
					elseif ($zn == $this->in)
						$formated .= "\n" . str_repeat(" ", $indent);
					elseif ($zn == $this->ei)
						$formated .= "\n" . str_repeat(" ", --$indent);
					else
						$formated .= $zn;
				}
				$formated = $formated;
				$formated .= "\n ),\n actions(\n  mark('ORGANIZATION_NAM')\n )\n)";
				
				$table[] = $formated;
			}
		}
		return $table;	
	}
	
	function _getRules(&$node, $prefix){
		if ( count($node->nodes) == 0 )
			return array($prefix . $this->in . $node->text);
		else{
			$tab = array();
			foreach ($node->nodes as $n){
				$tab = array_merge( $tab, $this->_getRules($n, $prefix . $this->in . $node->text) );
			}
			return $tab;
		}
	}
}

// Indeks wierzchołków
$nodes_index = array();

// Wczytaj plik
$patterns = file($file_with_patterns);

$nets = array();
$patterns_items = array();

foreach ($patterns as $p){	
	$tokens = split(" ", trim($p));	
	$token = array_shift($tokens);
	$patterns_items[$token][] = trim($p);
	if (!isset($nets[$token]))
		$nets[$token] = new NodeNet();
	$nets[$token]->addPath($tokens);
}
 
$total = 0;
foreach ($nets as $k=>$n){
	echo "\n====\n$k\n----\n";
	$n->relabel();
	$n->reduce();	
	
	foreach ($patterns_items[$k] as $p)
		echo $p,"\n";
	echo "\n";
	
	echo "Liczba początkowych wierzchołków: ",count($n->start),"\n";
	$rules = $n->getRules();
	for ($i=1; $i<=count($rules); $i++, $total++)
		echo "# Reguła ",$i," (",$total,")\n",$rules[$i-1],"\n\n";
}

echo "Reguł ",$total,"\n";
?>