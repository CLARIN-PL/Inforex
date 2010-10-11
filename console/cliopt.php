<?

class ClioptArgument{
	
	var $name = null;
	var $description = null;
	var $enum = null;
	
	function __construct($name, $description, $enum=null){
		$this->name = $name;
		$this->description = $description;
		$this->enum = $enum;
	}
	
}

class ClioptParameter{
	
	var $name = null;
	var $short = null;
	var $param = null;
	var $description = null;
	
	function __construct($name, $short, $param, $description){
		$this->name = $name;
		$this->short = $short;
		$this->param = $param;
		$this->description = $description;
	}

	function toString(){
		if ($this->param)
			return sprintf("--%-30s - %s", $this->name." <".$this->param.">", $this->description);
		else
			return sprintf("--%-30s - %s", $this->name." ", $this->description);
	}	
}

class Cliopt{
	
	var $cli = null;
	var $parameters = array();
	var $arguments = array();
	var $executes = null;
	var $argumentValues = array();
	
	function __construct(){
		
	}
	
	function parseCli($argv){
		$this->argumentValues = array();
		$this->argv = $argv;
		
		$param_names = array();
		foreach ($this->parameters as $p)
			$param_names[$p->name] = 1;
			
		$skip = true;
		foreach ($argv as $a){
			if (substr($a, 0, 2)=="--"){
				$skip = true;
				$name = substr($a, 2);
				if (!$param_names[$name])
					throw new Exception("Uknown parameter '$name'");
			}else{
				if (!$skip){
					$this->argumentValues[] = $a; 
				}
				$skip = false;
			}
		}
		
		$i = 0;
		foreach ($this->arguments as $a){
			$v = $this->argumentValues[$i];
			if ($a->enum != null)
				if ( ! ( in_array($v, $a->enum)
				         || ( is_numeric($v) && in_array("DECIMAL", $a->enum) ) 
				       )
				   ) throw new Exception("Incorrect value for <{$a->name}>. Expected one of: " . implode(", ", $a->enum));
			$i++;
		}	
	}
	
	function exists($name){
		return array_search("--$name", $this->argv) !==false;
	}
	
	function getArgument($index=0){
		return $this->argumentValues[$index];
	}
	
	function get($name){
		$p = array_search("--$name", $this->argv);
		return $this->argv[$p+1];
	}
	
	function getParameters($name){
		$values = array();
		for ($i=1; $i<count($this->argv); $i++)
			if ($this->argv[$i] == "--$name")
				$values[] = $this->argv[$i+1];
		return $values;		
	}
	
	function getOptional($name, $default){
		if ($this->exists($name))
			return $this->get($name);
		else
			return $default;
	}
	
	function getRequired($name){
		if ($this->exists($name)){
			return $this->get($name);			
		}else{
			throw new Exception("Parameter --$name not set");
		}
	}
	
	function addParameter($parameter){
		$this->parameters[] = $parameter;
	}
	
	function addExecute($sample, $description){
		$this->executes[$sample] = $description;
	}
	
	function addArgument($argument){
		$this->arguments[] = $argument;
	}
	
	function printHelp(){
		$args = array();
		
		print " Execute: \n";
		print "  php ".$_SERVER["SCRIPT_NAME"];
		foreach ($this->arguments as $a){
			print " <{$a->name}>";
			$args[] = sprintf("  %-30s - %s\n", $a->name, $a->description);		
		}
		print " [parameters]\n\n";
		
		if (count($this->executes)){
			print " Sample execute:\n";
			foreach ($this->executes as $sample=>$description);
				echo " ".sprintf(" %-30s - %s\n", $sample, $description);
		}
		print "\n";
		
		if (count($args)>0){
			print " Argument(s):\n";
			print implode($args);
			print "\n";
		}
		
		print " Parameters: \n";
		foreach ($this->parameters as $p){
			echo " ".$p->toString()."\n";
		}
	}
}
?>