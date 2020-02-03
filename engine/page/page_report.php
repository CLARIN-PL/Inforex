<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_report extends CPageCorpus {

	/* Reference to an object representing the current report. */
	var $report = null;

	function __construct(){
		parent::__construct();
        $this->includeJs("js/jquery/jquery.tablesorter.min.js");
        $this->includeJs("js/jquery/jquery.tablesorter.pager.min.js");
        $this->includeJs("js/c_selection.js");
        $this->includeJs("js/c_annotation.js");
        $this->includeJs("js/page_report_annotation_highlight.js");
        $this->includeJs("js/jquery/jquery.tablesorter.pager.min.js");
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

    function execute(){
		global $corpus, $user, $config;

		$cid = intval($corpus['id']);
		$this->cid = $cid;
		// Przygotuj parametry filtrowania raportów
		// ******************************************************************************
		$id 	= intval($_GET['id']);
		$this->id = $id;
		$p 		= intval($_GET['p']);
		$edit 	= intval($_GET['edit']);
		$subpage = array_key_exists('subpage', $_GET) ? $_GET['subpage'] : $_COOKIE["{$cid}_".'subpage'];
		$this->subpage = $subpage;
		$view  = array_key_exists('view', $_GET) ? $_GET['view'] : $_COOKIE["{$cid}_".'view'];
		$where = trim($_COOKIE["{$cid}_".'sql_where']);
		$join  = stripslashes($_COOKIE["{$cid}_".'sql_join']);
		$group = stripcslashes($_COOKIE["{$cid}_".'sql_group']);
		$order = stripcslashes($_COOKIE["{$cid}_".'sql_order']);
		
		// Domyślne wartości dla wymaganych
		$order = strlen($order)==0 ? "r.id ASC" : $order; 
        // domyślne sortowanie w przypadku losowej kolejności
        if (substr($order, 0, 5) === 'RAND(') {
             $order = 'r.id ASC';
        }
		
		// Walidacja parametrów
		// ******************************************************************************
		// List dostępnych podstron dla danego korpusu
		$subpages = DBReportPerspective::get_corpus_perspectives($cid, $user);
		
		if ( $subpage == "unassigned"  || $subpage == "noaccess" ||$subpage == "" ){
			$subpage = "preview";
		}
		
		$find = false;
		foreach ($subpages as $s){
			$find = $find || $s->id == $subpage;
		}

		// ToDo: Verify if given user can have access to the requested perspective
		//       Check if it is a matter of role or the perspective is not assigned to the corpora
//		if ( !$find && $subpage != ""
//				 && ( hasCorpusRole(CORPUS_ROLE_MANAGER) || isCorpusOwner() ) ){
//				$this->set("unassigned_subpage", $subpage);
//				$subpage = 'unassigned';
//		}
//		else
		if ( !$find ){
			$perspectives = DBReportPerspective::get_corpus_perspectives($cid, $user);
			$subpage = count($perspectives) > 0 ? strtolower($perspectives[0]->id) : 'noaccess';
		}

		if (!$id){
			header("Location: index.php?page=browse");
		}
		
		// Zapisz parametry w sesjii
		// ******************************************************************************
		if ( $subpage != "unassigned" ){		
			setcookie("{$cid}_".'subpage', $subpage);
		}
		setcookie('view', $view);
						
		$row = $this->load_report_ext($id, $corpus);
		
		/* Sprawdzenie, czy id raportu jest poprawny */
		if ( !isset($row['id'])){
			$this->set("invalid_report_id", true);
			return;
		}
		$this->assertReportInCorpus($id, $cid);

        $access = hasAccessToReport($user, $row, $corpus);
		if ( $access !== true){
			$this->set("page_permission_denied", $access);
			return;
		}
		 		 
		// Dodanie nazwy podkorpusu jeżeli dokument jest do niego przypisany   		 
		if($row['subcorpus_id']){
			$subcorpus_name = $this->get_subcorpus_name($row['subcorpus_id']); 
			$row['subcorpus_name'] = $subcorpus_name;
		}
						
		$this->row = $row; // ToDo: Do wycofania. Zastąpione przez $this->report
		$this->report = $row;

        $year = date("Y", strtotime($row['date']));
		$month = date("n", strtotime($row['date']));
				
		// Lista adnoatcji
		$annotations = null;
		if ($subpage!="preview"){
			$annotations = db_fetch_rows("SELECT a.*, u.screename" .
					" FROM reports_annotations a" .
					" JOIN annotation_types t " .
						" ON (a.type=t.name)" .
					" LEFT JOIN users u USING (user_id)" .
					" WHERE a.report_id=$id");		
		}
		
		if (!in_array($subpage,array('annotator_anaphora','preview','annotator','autoextension','tokenization')) ){
			$this->set_annotations();
		}

        $this->set_flags();
		$this->set_up_navigation_links($id);

		$this->set('row', $row); // ToDo: do wycofania, zastąpione przez report
		$this->set('report', $row);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('p', $p);
		$this->set('status', $row['status']);
		$this->set('edit', $edit);
		$this->set('view', $view);
		$this->set('content_formated', reformat_content($row['content']));
		$this->set('annotations', $annotations);
		
		$this->set('subpages', $subpages);
		$this->set('report_id',$id);
	 	
		// Load and execute the perspective 
		$perspective_class_name = "Perspective".ucfirst($subpage);

		if (class_exists($perspective_class_name)){
			$perspective = new $perspective_class_name($this, $row);
			$perspective->execute();
		}else{
            $this->set("error", "Perspective $subpage does not exist");
			$subpage = "noaccess";
			$perspective_class_name = "Perspective".ucfirst("noaccess");
			$perspective = new $perspective_class_name($this, $row);
		}

		/**
		 * Dołączonie domyślnych plików JS i CSS dla perspektyw dokumentu.
		 * js/page_report_{$subpage}.js — skrypty dla perspektywy $subpage
		 * js/page_report_{$subpage}_resize.js — kod JS odpowiedzialny za automatyczne dopasowanie okna do strony.
		 * css/page_report_{$subpage}.css — style CSS występujące tylko w danej perspektywie.
		 */
		if (file_exists($config->path_www . "/js/page_report_{$subpage}.js")){
			$this->includeJs("js/page_report_{$subpage}.js");
		}
		if (file_exists($config->path_www . "/js/page_report_{$subpage}_resize.js")){
			$this->includeJs("js/page_report_{$subpage}_resize.js");
		}
		if (file_exists($config->path_www . "/css/page_report_{$subpage}.css")){
			$this->includeCss("css/page_report_{$subpage}.css");
		}
		
		$this->set('subpage', $subpage);
		$this->set('subpage_file', "inc_report_". strtolower($subpage) . ".tpl");
		$this->set('flags_active', isset($_COOKIE['flags_active']) ? $_COOKIE['flags_active'] : "1");
        $this->set('config_active', isset($_COOKIE['config_active']) ? $_COOKIE['config_active'] : "1");

        /* Setup css for annotation sets */
        $annotation_sets =  DbAnnotation::getAnnotationStructureByCorpora($cid);
        $annotation_sets_list = "";
        foreach($annotation_sets as $key=>$value){
            $annotation_sets_list .= $key . ",";
        }
        $annotation_sets_list = rtrim($annotation_sets_list, ",");
        $this->includeCss("css.php?annotation_set_ids=" . $annotation_sets_list . "&");
        $this->includeCss("css.php?corpora_ids=" . $cid . "&");
	}

	function assertReportInCorpus($reportId, $corpusId){
		if(!count(DbReport::getReportsByCorpusIdWithParameters($corpusId,' * ', '', ' AND r.id=' . $reportId . ' ',''))){
			$corpus_id = DbCorpus::getCorpusByReportId($reportId);

			$new_url = 'index.php?';
			$i = 0;
			foreach($_GET as $key => $values){
				if($i)
					$new_url .= '&';
				if($key == 'corpus')
					$new_url .= 'corpus=' . $corpus_id;
				else
					$new_url .= $key . '=' . $values;
				$i++;
			}

			$this->redirect($new_url);
		}
	}

	/**
	 * @param $corpusId
	 * @param $where
	 * @param $join
	 * @param $group
	 * @param $order
	 * @param $currentId
	 */
	function set_up_navigation_links($currentId){
        $reports = new ReportListFilters($this->getDb(), $this->getCorpusId(), $this->getUserId());
        $sql = $reports->getSql();
        $sql->setSelectColumn(array(new SqlBuilderSelect("DISTINCT r.id", "id")));
        list($sql, $param) = $sql->getSql();
        $reportsIdFinal = $this->getDb()->fetch_ones($sql, 'id', $param);

		$pos = array_search($currentId, $reportsIdFinal);

		$this->set('row_prev_c', $pos );
		$this->set('row_number', $pos + 1);
		$this->set('row_first', $reportsIdFinal[0]);
		$this->set('row_prev', $pos > 0 ? $reportsIdFinal[$pos-1] : null);
		$this->set('row_prev_10', $pos >= 10 ? $reportsIdFinal[$pos-10] : null);
		$this->set('row_prev_100', $pos >= 100 ? $reportsIdFinal[$pos-100] : null);
		$this->set('row_last', $pos+1 < count($reportsIdFinal) ? $reportsIdFinal[count($reportsIdFinal)-1] : null);
		$this->set('row_next', $pos+1 < count($reportsIdFinal) ? $reportsIdFinal[$pos+1] : null);
		$this->set('row_next_10', $pos+10 < count($reportsIdFinal) ? $reportsIdFinal[$pos+10] : null);
		$this->set('row_next_100', $pos+100 < count($reportsIdFinal) ? $reportsIdFinal[$pos+100] : null);
		$this->set('row_next_c', max(0, count($reportsIdFinal) - $pos - 1));
	}

	/**
	 * Get a list of active flag filters
	 * @param $corpusId
	 * @return Sample [{name: "valid", corpora_flag_id: "320", values: ["-1"]}]
	 */
	function getFilterFlags($corpusId){
		$corpusFlags = DbCorpus::getCorpusFlags($corpusId);
		$flagFilters = array();

		foreach($corpusFlags as $key => $flag){
			$flagNameStr = 'flag_' . str_replace(' ', '_', $flag['short']);
			$flagCookieKey = "{$corpusId}_".$flagNameStr;
			if ( array_key_exists($flagCookieKey, $_COOKIE) ) {
				$f = array();
				$f['corpora_flag_id'] = intval($flag['corpora_flag_id']);
				$f['name'] = $flag['short'];
				$f['values'] = explode(",", $_COOKIE["{$corpusId}_" . $flagNameStr]);
				$flagFilters[] = $f;
			}
		}
		return $flagFilters;
	}

	function set_flags(){
		/*****flags******/
		$sql = "SELECT corpora_flags.corpora_flag_id AS id, corpora_flags.name, corpora_flags.short, reports_flags.flag_id, flags.name AS fname " .
				"FROM corpora_flags " .
				"LEFT JOIN reports_flags " .
					"ON corpora_flags.corpora_id={$this->cid} " .
					"AND reports_flags.report_id={$this->id} " .
					"AND corpora_flags.corpora_flag_id=reports_flags.corpora_flag_id " .
				"LEFT JOIN flags " .
					"ON reports_flags.flag_id=flags.flag_id " .
				"WHERE corpora_flags.corpora_id={$this->cid}" .
				" ORDER BY sort";
		$corporaflags = db_fetch_rows($sql);
		$sql = "SELECT flag_id AS id, name FROM flags ";
		$flags = db_fetch_rows($sql);
		$this->set('corporaflags',$corporaflags);
		$this->set('flags',$flags);
	}
	
	function set_annotations(){
		$row = $this->row;
		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, text, t.group_id, ans.description setname, ansub.description subsetname, ansub.annotation_subset_id, t.name typename, t.short_description typedesc, an.stage, t.css, an.source"  .
				" FROM reports_annotations an" .
				" LEFT JOIN annotation_types t ON (an.type=t.name)" .
				" LEFT JOIN annotation_subsets ansub ON (t.annotation_subset_id=ansub.annotation_subset_id)" .
				" LEFT JOIN annotation_sets ans on (t.group_id=ans.annotation_set_id)" .
				" WHERE report_id = {$row['id']} ";
		$sql = $sql . " ORDER BY `from` ASC, `level` DESC"; 
		$anns = db_fetch_rows($sql);
		try{
			$htmlStr = new HtmlStr2($row['content'], true); //akaczmarek: można dodać sprawdzenie czy format nie jest ustawiony na 'plain'
			$this->set('content_inline', Reformat::xmlToHtml($htmlStr->getContent()));
			$this->set('anns',$anns);
		} catch(Exception $ex){
			$this->set("error", $ex->getMessage());
		}

	}
	
	/**
	 * Load report with extended data.
	 */
	function load_report_ext($report_id, $corpus){
		if ( $corpus['ext'] ){
			$sql = "SELECT r.*, e.*, r.id, rs.status AS status_name, rt.name AS type_name, rf.format" .
					" FROM reports r" .
					" JOIN reports_formats rf ON (r.format_id = rf.id)" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" LEFT JOIN {$corpus['ext']} e ON (r.id=e.id) " .
					" WHERE r.id={$report_id}";
		} else {
			$sql = "SELECT r.*, rs.status AS status_name, rt.name AS type_name, rf.format" .
					" FROM reports r" .
					" JOIN reports_formats rf ON (r.format_id = rf.id)" .
					" LEFT JOIN reports_statuses rs ON (r.status = rs.id)" .
					" LEFT JOIN reports_types rt ON (r.type = rt.id)" .
					" WHERE r.id={$report_id}";
		}
		return db_fetch($sql);		
	}
	
	function get_subcorpus_name($subcorpus_id){
		global $db;
		$sql = "SELECT cs.name AS name FROM corpus_subcorpora cs WHERE cs.subcorpus_id=? ";
		return $db->fetch_one($sql, array($subcorpus_id));
	}
	
	function where_or($column, $values){
		$ors = array();
		foreach ($values as $value)
			$ors[] = "$column = '$value'";
		if (count($ors)>0)	
			return "(" . implode(" OR ", $ors) . ")";
		else
			return "";
	}
	
}