<?php

namespace Sk\MediaApiBundle\MediaProviderApi;
use \Exception;

class SimpleRequest{

    public function makeRequest($host, $params, $user_agent = '')
    {
        $method = "GET";
        //$host = $end_point;
        //$uri = "/onca/xml";

        ksort($params);
        $canonicalized_query = array();

        foreach ($params as $param=>$value)
        {
            $param = str_replace("%7E", "~", rawurlencode($param));
            $value = str_replace("%7E", "~", rawurlencode($value));
            $canonicalized_query[] = $param."=".$value;
        }

        $canonicalized_query = implode("&", $canonicalized_query);

        /* create request */
        $request = "//".$host."?".$canonicalized_query;

        $response = $this->execCurl($request, $user_agent);
        if ($response === False)
        {
            return False;
        }
        else
        {
            //$parsed_response = @simplexml_load_string($xml_response);
            return $response;
        }
    }

    public function execCurl($request, $user_agent){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if(strlen($user_agent) > 0){
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        }

        $response = null;
        try {
            $response = curl_exec($ch);
        } catch (Exception $ex) {
            $response = $ex;
        }

        // curl_close($ch);

        return $response;
    }


}
?>
