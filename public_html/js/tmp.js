//tymczasowy "bezpieczny" logger dla firebugowej konsoli
function log(obj){
	try{
		console.log(obj);
	}
	catch(err){
	}
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
						cancel_relation(); 
					}
				});				
		}
		else {
			alert('nieznany blad!');
		}
	} 
	else {
		if (successHandler && $.isFunction(successHandler)){
			successHandler();
		}		
	}
} 
