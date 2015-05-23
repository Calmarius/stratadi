<?php

class Template 
{
    private $args;
    private $file;

    public function __get($name) 
    {
        return @$this->args[$name];
    }

    public function __construct($file, $args = array()) 
    {
        $this->file = $file;
        $this->args = $args;
    }

    public function render() 
    {
        include $this->file;
    }
    
    public function getContents()
    {
    	ob_start();
	        include $this->file;
	        $v=ob_get_contents();
    	ob_end_clean();
    	return $v;
    }
}

?>
