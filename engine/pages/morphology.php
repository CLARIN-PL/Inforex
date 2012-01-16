<?php
class Page_morphology extends CPage{
	
	function execute(){
		
		$ctag = $_GET['ctag'];
		$subcorpus = $_GET['subcorpus'];
		
		$corpus_id = 3;
				
		$this->set("classes", $this->getSgjpClasses());
		$this->set("ctag", $ctag);
		$this->set("subcorpus", $subcorpus);
		$this->set("words", DbCorpusStats::getWordsFrequnces($corpus_id, $subcorpus, $ctag, true));
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));		
	}
	
	function getSgjpClasses(){
		$c = array();
		$c['adv'] = 'adv';
		$c['imps'] = 'imps';
		$c['inf'] = 'inf';
		$c['pant'] = 'pant';
		$c['pcon'] = 'pcon';
		$c['qub'] = 'qub';
		$c['prep'] = 'prep';
		$c['siebie'] = 'siebie';
		$c['subst'] = 'subst';
		$c['depr'] = 'depr';
		$c['ger'] = 'ger';
		$c['ppron12'] = 'ppron12';
		$c['ppron3'] = 'ppron3';
		$c['num'] = 'num';
		$c['numcol'] = 'numcol';
		$c['adj'] = 'adj';
		$c['pact'] = 'pact';
		$c['ppas'] = 'ppas';
		$c['winien'] = 'winien';
		$c['praet'] = 'praet';
		$c['bedzie'] = 'bedzie';
		$c['fin'] = 'fin';
		$c['impt'] = 'impt';
		$c['aglt'] = 'aglt';
		$c['ign'] = 'ign';
		$c['brev'] = 'brev';
		$c['burk'] = 'burk';
		return $c;
	}

}
 
?>