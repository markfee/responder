<?php namespace Markfee\Responder\Responder;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
/*
 * ApiResponse wraps the Response returned by a Responder API back into a response object
 */

class ApiResponse extends Response {

    public function __construct(GuzzleResponse $response) 
    {
        parent::__construct();
        $this->withStatusCode($response->getStatusCode());
        $decoded = $this->readResponseBodyToObject($response);
        if (!empty($decoded->data)) {
            $this->withData($decoded->data);    
        }
        if (!empty($decoded->messages)) {
            $this->MessageSet($decoded->messages);
        }
        if (!empty($decoded->paginator)) {
            $this->withPaginator($decoded->paginator);
        }
        
    }

    private function readResponseBodyToObject($response)
    {
        $json = '';
        while (!$response->getBody()->eof()) {
            $json = $json . $response->getBody()->read(1024);
        }

        return json_decode($json);
    }
}