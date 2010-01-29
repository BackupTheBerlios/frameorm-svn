<?php
include_once($_SERVER['DOCUMENT_ROOT']."/framework/core/Logger.class.php");

class AppException extends Exception
{
	
	public function __construct($message, $logError=false) 
	{
        parent::__construct($message);
        if($logError)
        	Logger::log($this->message);
    }
}

class UnauthorizedException extends AppException
{
	public function __construct($message) 
	{
        parent::__construct($message);
    }
}

class PermissionDenied extends AppException
{
	public function __construct($message) 
	{
        parent::__construct($message);
    }
}

class EntryNotFound extends AppException
{
	public function __construct($message) 
	{
        parent::__construct($message);
    }
}

class DuplicateEntry extends AppException
{
	public function __construct($message) 
	{
        parent::__construct($message);
    }
}

class ThrowError extends AppException
{
	public function __construct($message) 
	{
        parent::__construct($message);
		trigger_error($message, E_USER_ERROR);
    }
}

class NotFound extends ThrowError
{
	public function __construct($message) 
	{
        parent::__construct($message);
		trigger_error($message, E_USER_ERROR);
    }
}

class NotImplementedError extends ThrowError
{
	public function __construct($message) 
	{
        parent::__construct($message);
    }
}

class InvalidValue extends ThrowError
{
	public function __construct($message) 
	{
        parent::__construct($message);
    }
}

class AttributeNotFound extends ThrowError
{
	public function __construct($message) 
	{
        parent::__construct($message);
    }
}
?>