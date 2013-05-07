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
		$short = $this->short == null ? "    --" : "-{$this->short}, --"; 
		$descriptions = explode("|", $this->description);
		$txt = null;
		if ($this->param)
			$txt = sprintf("%-30s - %s", $short . $this->name ." <".$this->param.">", $descriptions[0]);
		else
			$txt = sprintf("%-30s - %s", $short . $this->name ." ", $descriptions[0]);
		for ($i=1; $i<count($descriptions); $i++)
			$txt .= sprintf("\n%33s%s", " ", $descriptions[$i]);
		return $txt;
	}	
}

class Cliopt{
	
	var $cli = null;
	var $parameters = array();
	var $arguments = array();
	var $executes = null;
	var $argumentValues = array();
	var $description = null;
	var $authors = null;
	
	function __construct($description=null){
		$this->description = $description;
	}
	
	function setAuthors($authors){
		$this->authors = $authors; 
	}
	
	function parseCli($argv){
		$this->argumentValues = array();
		$this->argv = $argv;
		
		$param_names = array();
		foreach ($this->parameters as $n=>$p)
			$param_names[$p->name] = 1;
			
		$skip = true;
		foreach ($argv as $a){
			if (substr($a, 0, 2)=="--"){
				$skip = true;
				$name = substr($a, 2);
				if (!$param_names[$name])
					throw new Exception("Uknown parameter '$name'");
			}
			else if(substr($a, 0, 1)=="-"){
				$skip = false;
				$name = substr($a, 1);
				foreach ($this->parameters as $p)
					$skip = $p->short==$name || $skip;
				if ($skip==false)
					throw new Exception("Uknown parameter '$name'");
			}
			else{
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
		
		if ( count($this->argumentValues) > count($this->arguments) )
			throw new Exception("Too many arguments. Only ".count($this->arguments)." expected");
		else if ( count($this->argumentValues) < count($this->arguments) )
			throw new Exception("Too few arguments. ".count($this->arguments)." expected");
	}
	
	function exists($name){
		$short = $this->parameters[$name]->short;
		return array_search("--$name", $this->argv) !==false || ( $short != null && array_search("-$short", $this->argv) !== false );
	}
	
	function getArgument($index=0){
		if ( $index < count($this->argumentValues) )
			return $this->argumentValues[$index];
		else
			throw new Exception("Argument $index not present");
	}
	
	function get($name){		
		$p = array_search("--$name", $this->argv);
		if ( $p === false ){
			$short = $this->parameters[$name]->short;			
			$p = array_search("-$short", $this->argv);
		}
		return $this->argv[$p+1];
	}
	
	/**
	 * Get a list of parameters as an array.
	 */
	function getParameters($name){
		$short = $this->parameters[$name]->short;
		$values = array();
		for ($i=1; $i<count($this->argv); $i++)
			if ($this->argv[$i] == "--$name" || ( $short != null && $this->argv[$i]=="-$short") )
				$values[] = $this->argv[$i+1];
		if (count($values)==0)
			return array();
			//throw new Exception("Parameter '$name' not found");
		return $values;		
	}
	
	function getOptionalParameters($name){
		$short = $this->parameters[$name]->short;
		$values = array();
		for ($i=1; $i<count($this->argv); $i++)
			if ($this->argv[$i] == "--$name" || ( $short != null && $this->argv[$i]=="-$short") )
				$values[] = $this->argv[$i+1];
		if (count($values)==0)
			return null;
		else
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
		assert('($parameter instanceof ClioptParameter) /* $parameter not a ClioptParameter */'); 
		$this->parameters[$parameter->name] = $parameter;
	}
	
	function addExecute($sample, $description){
		$this->executes[$sample] = $description;
	}
	
	function addArgument($argument){
		assert('($argument instanceof ClioptArgument) /* $parameter not a ClioptArgument */'); 
		$this->arguments[] = $argument;
	}
	
	function printHelp(){
		$args = array();
		
		if ($this->description)
			print $this->description . "\n";
		if ($this->authors)
			print "© " . $this->authors . "\n";
		if ($this->description || $this->authors)
			print "\n";
		
		print " Execute: \n";
		print "  php ".$_SERVER["SCRIPT_NAME"];
		foreach ($this->arguments as $a){
			print " <{$a->name}>";
			$args[] = sprintf("  %-30s  - %s {%s}\n", $a->name, $a->description, implode(", ", is_array($a->enum)?$a->enum:array()));		
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
		foreach ($this->parameters as $n=>$p){
			echo " ".$p->toString()."\n";
		}
	}
}
?>