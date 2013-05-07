<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Przykładowe opisy błędów:
 * DOMDocument::loadXML(): Opening and ending tag mismatch: p line 7 and body in Entity, line: 8
 * DOMDocument::schemaValidate(): The document has no document element.
 * DOMDocument::loadXML(): Opening and ending tag mismatch: p line 7 and body in Entity, line: 8
 */ 
class MyDOMDocument {
    private $_delegate;
    private $_validationErrors;
   
    public function __construct () {
        $this->_delegate = new DOMDocument();
        $this->_validationErrors = array();
    }
   
    public function __call ($pMethodName, $pArgs) {
        if ( in_array( $pMethodName, array("validate"))) {
        	//echo "call $pMethodName\n";	
            $eh = set_error_handler(array($this, "onValidateError"));
            if (count($pArgs)>0)
            	$rv = $this->_delegate->$pMethodName($pArgs[0]);
            else
            	$rv = $this->_delegate->$pMethodName();
            
            if ($eh) {
                set_error_handler($eh);
            }
            return $rv;
        }
        elseif ( $pMethodName == "schemaValidate" || $pMethodName == "loadXML" ){
			libxml_use_internal_errors(true);     
			if ( $rv = $this->_delegate->$pMethodName($pArgs[0]) ) {
			} else {
				$errors = libxml_get_errors();
			    foreach ($errors as $error) {
			        $error = array("line"=>$error->line, "col"=>$error->column, "description"=>$error->message); 			        
//			        	sprintf('XML error "%s" [%d] (Code %d) in %s on line %d column %d' . "\n",
//			            $error->message, $error->level, $error->code, $error->file,
//			            $error->line, $error->column);
			        $this->_validationErrors[] = $error; 
			    }
			    libxml_clear_errors();
			}
			libxml_use_internal_errors(false);        	
        }
        else {
            return call_user_func_array(array($this->_delegate, $pMethodName), $pArgs);
        }
    }
    
    public function __get ($pMemberName) {
        if ($pMemberName == "errors") {
            return $this->_validationErrors;
        }
        else {
            return $this->_delegate->$pMemberName;
        }
    }
    public function __set ($pMemberName, $pValue) {
        $this->_delegate->$pMemberName = $pValue;
    }
    public function onValidateError ($pNo, $pString, $pFile = null, $pLine = null, $pContext = null) {
        //$this->_validationErrors[] = preg_replace("/^.+: */", "", $pString);
        $this->_validationErrors[] = array("line"=>"?", "description"=>$pString) ;
    }
    public function getErrors(){
    	return $this->_validationErrors;
    }
}
    
?>