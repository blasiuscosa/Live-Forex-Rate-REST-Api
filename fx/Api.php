<?php

abstract class Api {
    
    protected $method = "";
    
    protected $args = array();
    
    protected $endpoint = "";
    
    protected $verbs = "";
    
    protected $request = array();
    
    public function __construct($request) {
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        $this->args = explode("/", rtrim($request, "/"));
        $this->endpoint = array_shift($this->args);
        if(array_key_exists(0, $this->args) && !is_numeric($this->args[0]))
        {
            $this->verbs = array_shift($this->args);
        }
        $this->method = $_SERVER["REQUEST_METHOD"];
        switch($this->method)
        {
            case "POST":
                $this->request = $this->cleanInput($_POST);
                break;
            case "GET":
                break;
            case "DELETE":
                break;
            case "PUT":
                parse_str(file_get_contents("php://input"), $this->request);
                $this->request = $this->cleanInput($this->request);
                break;
            default:
                $this->jsonResponse(array("error"=>"Invalid Method"), 405);
                break;
        }
        
    }
    
    private function cleanInput($data)
    {
        $clean_input = array();
        if(is_array($data))
        {
            foreach($data as $k => $v)
            {
                $clean_input[$k] = $this->cleanInput($v);
            }
        }
        else
        {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    
    public function executeApi()
    {
        if(method_exists($this, $this->endpoint))
        {
            return $this->{$this->endpoint}($this->args, $this->verbs);
        }
        else if($this->endpoint == "")
        {
            return $this->jsonResponse(array("error"=>"Empty endpoint"), 404);
        }
        else
        {
            return $this->jsonResponse(array("error"=>"No endpoint: " . $this->endpoint), 404);  
        }
    }
    
    private function requestStatus($code)
    {
        $status = array(
                        100 => 'Continue',  
	                101 => 'Switching Protocols',  
			200 => 'OK',
			201 => 'Created',  
		        202 => 'Accepted',  
			203 => 'Non-Authoritative Information',  
			204 => 'Deleted',  
			205 => 'Reset Content',  
			206 => 'Partial Content',  
			300 => 'Multiple Choices',  
			301 => 'Moved Permanently',  
			302 => 'Found',  
			303 => 'See Other',  
			304 => 'Not Modified',  
			305 => 'Use Proxy',  
			306 => '(Unused)',  
			307 => 'Temporary Redirect',  
			400 => 'Bad Request',  
			401 => 'Unauthorized',  
			402 => 'Payment Required',  
			403 => 'Forbidden',  
			404 => 'Not Found',  
			405 => 'Method Not Allowed',  
		        406 => 'Not Acceptable',  
			407 => 'Proxy Authentication Required',  
			408 => 'Request Timeout',  
			409 => 'Conflict',  
			410 => 'Gone',  
			411 => 'Length Required',  
			412 => 'Precondition Failed',  
			413 => 'Request Entity Too Large',  
			414 => 'Request-URI Too Long',  
			415 => 'Unsupported Media Type',  
			416 => 'Requested Range Not Satisfiable',  
			417 => 'Expectation Failed',  
			500 => 'Internal Server Error',  
			501 => 'Not Implemented',  
			502 => 'Bad Gateway',  
			503 => 'Service Unavailable',  
			504 => 'Gateway Timeout',  
			505 => 'HTTP Version Not Supported'
                       );
        return ($status[$code])?$status[$code]:$status[500];
    }
    
    public function jsonResponse($data, $status = 200)
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
