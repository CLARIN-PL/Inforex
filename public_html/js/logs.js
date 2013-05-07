/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

//tymczasowy "bezpieczny" logger dla firebugowej konsoli
function log(obj){
	/*try{
		console.log(obj);
	}
	catch(err){
	}*/
}

function ajaxErrorHandler(data, successHandler, errorHandler){
	if (data['error']){
		if (data['error_code']=="ERROR_AUTHORIZATION"){
				loginForm(false, function(success){ 
					if (success){						
						if (errorHandler && $.isFunction(errorHandler)){
							errorHandler();
						}
					}else{
						$dialogObj = $(".deleteDialog");
						if ($dialogObj.length>0){
							$dialogObj.dialog("destroy").remove();
						} 
					}
				});				
		}
		else {
			alert('nieznany blad! '+data['error']+" "+data['error_code']);
			$dialogObj = $(".deleteDialog");
			if ($dialogObj.length>0){
				$dialogObj.dialog("destroy").remove();
			}
		}
	} 
	else {
		if (successHandler && $.isFunction(successHandler)){
			successHandler();
		}		
	}
} 
