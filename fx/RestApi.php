<?php
require_once 'Api.php';

class RestApi extends Api {
    
    
    public function __construct($request) {
        parent::__construct($request);
        
    }
    
    public function currency($arg, $verb = "")
    {
        if($this->method = "GET")
        {
            if(empty($arg) && $verb == "")
            {
                return $this->jsonResponse(array("error"=>"Resource not found."));
            }
            else if(!empty($arg) && $verb != "")
            {
                if($verb == "rates")
                {
                    return $this->getLiveRate($arg[0]);
                }
                else
                {
                    return $this->jsonResponse(array("error"=>"Resource not found."));
                }
            }
            else
            {
               return $this->jsonResponse(array("error"=>"Resource not found.")); 
            }
        }
    }
    
    private function getLiveRate($pair)
    {
        $currency_rate_arr = array();
        $currency_arr_with_key = array();
        if(strpos($pair, ",") !== false)
        {
            $currency_pair = explode(",", $pair);
        }
        else
        {
        $currency_pair = array($pair);
        }
        $currency_string = "";
        foreach($currency_pair as $pair)
        {
            $currency_string .= trim($pair) . "=X+";
        }
   
        $final_string = rtrim($currency_string, "+");
        
            $file = fopen("https://download.finance.yahoo.com/d/quotes.csv?s={$final_string}&f=snl1d1t1ab", "r");
            if($file)
            {
                while(!feof($file))
                {
                   $currency_rate_arr[] = fgetcsv($file);
                }
            }
            fclose($file);
           
        for($i = 0; $i < count(array_filter($currency_rate_arr)); $i++)
        {
             $currency_arr_with_key[] = array("id"=>$currency_rate_arr[$i][0], 
                                              "name"=>$currency_rate_arr[$i][1], 
                                              "rate"=>$currency_rate_arr[$i][2],
                                              "date"=>$currency_rate_arr[$i][3],
                                              "time"=>$currency_rate_arr[$i][4],
                                              "buy"=>$currency_rate_arr[$i][5],
                                              "sell"=>$currency_rate_arr[$i][6]
                                             );
             if($currency_rate_arr[$i][1] == "N/A")
             {
                 return $this->jsonResponse(array("error"=>"Resource not found."));
             }
            
        }
        return $this->jsonResponse(array("rate"=>$currency_arr_with_key));
    }
    
    
   
}