<?php
include('framework/core/autoload.php');
function appErrorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
    case E_USER_WARNING:
    case E_USER_NOTICE:
    case E_NOTICE:
        break;
    case E_USER_ERROR:
    default:
    	$err = "ERROR: $errstr on line $errline in file $errfile\n";
        $context = Context::getInstance();
        $context->response->httpCode = 500;
        $context->response->body = $err;
        $context->response->write();
        break;
    }
    return false;
}
#set_error_handler("appErrorHandler");
$oRouter = new Router();
$oRouter->invoke();
?>