<?php
class Router
{
	protected $class;
	protected $args;
	protected $oReflection;
	protected $oReflectionMethod;
	
	public function __construct()
	{
		$this->setAttrs();
	}
	
	protected function setAttrs()
	{
		$context = Context::getInstance();
		
		$url = $_SERVER['PATH_INFO'];
		if ($url[strlen($url)-1] == '/')
			$url = substr($url, 0, -1);
		$url = substr($url, 1);
		$url = explode("/", $url);
		
		if(count($url) < 2){
			$context->response->httpCode = 400;
			$context->response->write();
		}
		$this->class = ucfirst(array_shift($url));
		$this->method = array_shift($url);
		if(count($url) > 0){
			$this->args = $url;
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if($_SERVER['CONTENT_TYPE'] == 'application/x-www-form-urlencoded' || 
				strstr($_SERVER['CONTENT_TYPE'] , 'multipart/form-data')){
				$this->args = array($_POST);
			}
			else if(ereg('application/json', $_SERVER['CONTENT_TYPE']))
			{
				$context->response->contentType = 'application/json';
				$ob = $GLOBALS['HTTP_RAW_POST_DATA'];
				$ob = json_decode($ob, true);
				if(!is_array($this->args))
					$this->args = array();
				$this->args[] = $ob;
			}
			else
				throw new ReflectionException('Unknown content type for POST method');
		}
	}
	
	private function callConstructor()
	{
		return $this->oReflection->getConstructor()->getNumberOfRequiredParameters() > 0;
	}
	
	private function passMethodArgs()
	{
		return $this->oReflectionMethod->getNumberOfParameters() > 0;
	}
	
	private function checkAuth()
	{
		$oInstance = $this->oReflection;
		if (count($oInstance->getInterfaces()) > 0)
		{
			$class = $this->class;
			$module = Module::getModuleByName($class);
			while(is_null($module) && $class != 'Page'){
				$ref = new ReflectionClass($class);
				$ref = $ref->getParentClass();
				$class = $ref->name;
				$module = Module::getModuleByName($class);
			}
			foreach ($oInstance->getInterfaces() as $oInt)
			{
				if ($oInt->getName() == 'ACLControl')
				{
					foreach($oInt->getMethods() as $method)
					{
						$oRefMethod = $oInstance->getMethod($method->getName());
						$oRefMethod->invokeArgs($oInstance->newInstance(), array($module));
					}
				}
			}
		}
	}
	
	private function postProcess()
	{
		$oInstance = $this->oReflection;
		if (count($oInstance->getInterfaces()) > 0)
		{
			foreach ($oInstance->getInterfaces() as $oInt)
			{
				if ($oInt->getName() == 'PostProcessFilter' || 
					$oInt->isSubclassOf(new ReflectionClass('PostProcessFilter')))
				{
					foreach($oInt->getMethods() as $method)
					{
						$oRefMethod = $oInstance->getMethod($method->getName());
						$oRefMethod->invoke($oInstance->newInstance());
					}
				}
			}
		}
	}
	
	private function preProcess()
	{
		$oInstance = $this->oReflection;
		if (count($oInstance->getInterfaces()) > 0)
		{
			foreach ($oInstance->getInterfaces() as $oInt)
			{
				if ($oInt->getName() == 'PreProcessFilter' || 
					$oInt->isSubclassOf(new ReflectionClass('PreProcessFilter')))
				{
					foreach($oInt->getMethods() as $method)
					{
						$oRefMethod = $oInstance->getMethod($method->getName());
						$oRefMethod->invoke($oInstance->newInstance());
					}
				}
			}
		}
	}
	
	public function invoke()
	{
		$context = Context::getInstance();
		try{
			$this->oReflection = new ReflectionClass($this->class);
			$this->PreProcess();
			$this->checkAuth();
			$this->oReflectionMethod = $this->oReflection->getMethod($this->method);
			$oResp = null;
			if ($this->callConstructor())
				$oResp = $this->oReflectionMethod->invoke($this->oReflection->newInstanceArgs($this->args));
				
			else if ($this->passMethodArgs())
				$oResp = $this->oReflectionMethod->invokeArgs($this->oReflection->newInstance(), 
															  $this->args);
				
			else
				$oResp = $this->oReflectionMethod->invoke($this->oReflection->newInstance());
			
			if(ereg('application/json', $_SERVER['CONTENT_TYPE']))
				$oResp = json_encode($oResp);
				
			$context->response->body = $oResp;
			$this->postProcess();
		}
		catch(UnauthorizedException $e){
			$context->response->httpCode = 401;
		}
		catch(ReflectionException $e){
			$context->response->httpCode = 400;
			$context->response->body = $e->getMessage();
		}
		catch(Exception $e){
			$context->response->httpCode = 500;
			$context->response->body = $e->getMessage();
		}
		$context->response->write();
	}
}
?>