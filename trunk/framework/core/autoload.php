<?php
spl_autoload_register(array(ClassLoader::getInstance(), 'loadClass'));
require_once(dirname(__FILE__)."/Interfaces.php");

class ClassLoader 
{
    private static $SAVE_FILE = 'ClassLoader.class.php';
    private static $instance = null;
    private static $moduleDirs = array('framework/core', 'framework/schema', 
				  					   'framework/servlets', 'framework/utils');
    private $classList = array();
    private $refreshed;
    private $path;

    public static function getInstance()
    {
        if (is_null(self::$instance))
            self::$instance = new ClassLoader();
        return self::$instance;
    }

    private function __construct() 
    {
    	$this->path = $_SERVER['DOCUMENT_ROOT']."/framework/core/";
        $this->initClassList();
    }

    public function loadClass($className) 
    {
        if (!array_key_exists($className, $this->classList) && !$this->refreshed)
            $this->refreshClassList();
        require_once($this->classList[$className]);
    }

    private function initClassList() 
    {
        if (file_exists($this->path . self::$SAVE_FILE)) {
            require_once($this->path . self::$SAVE_FILE);
            $this->refreshed = FALSE;
        } else {
            $this->refreshClassList();
        } 
    }

    private function refreshClassList() 
    {
        $this->__scanDirectory();
        $this->refreshed = TRUE;
        $this->saveClassList();
    }

    private function saveClassList() 
    {
    	$handle = fopen($this->path . self::$SAVE_FILE, 'w');
        fwrite($handle, "<?php\r\n");

        foreach($this->classList as $class => $path) 
        {
            $line = '$this->classList' . "['" . $class . "'] = '" . $path . "';\r\n";
            fwrite($handle, $line);
        }

        fwrite($handle, '?>');
        fclose($handle);
    }
    
    private function __scanDirectory()
    {
		$root = $_SERVER['DOCUMENT_ROOT'];
		foreach(self::$moduleDirs as $dir){
			$rs = $this->scanDirectory($root."/".$dir);
			$this->classList = array_merge($rs, $this->classList);
		}
    }

    private function scanDirectory ($directory) 
    {
        if (substr($directory, -1) == '/') 
                $directory = substr($directory, 0, -1);

        if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory))
            return array();

        $dirH = opendir($directory);
        $scanRes = array();

        while(($file = readdir($dirH)) !== FALSE) 
        {
            if ( strcmp($file , '.') == 0 || strcmp($file , '..') == 0 || $file == '.svn')
                continue;

            $path = $directory . '/' . $file;

            if (!is_readable($path))
                continue;

            // recursion
            if (is_dir($path)) {
                $scanRes = array_merge($scanRes, $this->scanDirectory($path));

            } elseif (is_file($path)) {
                $className = explode('.', $file);
                if ( strcmp($className[1], 'class') == 0 && strcmp($className[2], 'php') == 0 ) {
                    $scanRes[$className[0]] = $path; 
                }
            }
        }

        return $scanRes;
    }

}


?>