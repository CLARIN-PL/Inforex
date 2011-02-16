//tymczasowy "bezpieczny" logger dla firebugowej konsoli
function log(obj){
	try{
		console.log(obj);
	}
	catch(err){
	}
}