<?php namespace Dwsla\ApiGateway;

use Zend\Http\Client;
use Zend\Http\Request;

class ApiGateway extends Client  implements ApiRunnerInterface{
    
    protected $endpoint;
    
    /**
     * Convenience methods for returning a specific content type from the API.
     *
     * @param string $route
     * @return array
     */
    public function __call($name, $args) {

        if (method_exists($this,$name)) {
            return $this->$name;
        }

        if (strstr($name, 'get')) {

            if (isset($args[0]) && !in_array(gettype($args[0]),array('string','integer'))){
                throw new ApiGatewayException("Get method expects parameter 1 to be a string or int");
            }
            
            $isSingular = (isset($args[1]) && $args[1] == true);        
            $this->setMethod(Request::METHOD_GET);
            $endpoint = strtolower(substr($name, 3));
            $path = $endpoint;
        
            if (isset($args[0])) {
                $path = $path . "/" . $args[0];    
            }
            
            $this->extendPath($path);                                                   

        } elseif (strstr($name, 'post')) {

            if (!isset($args[0]) || !is_array($args[0]) || count($args[0]) == 0) {
                throw new ApiGatewayException("Post method expects parameter 1 to be a non-empty array");
            }
            
            $this->setMethod(Request::METHOD_POST);
            $this->setRawBody(json_encode($args[0]));
            $this->setEncType("multipart/form-data");
            $endpoint = strtolower(substr($name, 4));
            $this->extendPath($endpoint);
        
        } elseif (strstr($name, 'put')) {
            
            if (!isset($args[0]) || !in_array(gettype($args[0]),array('string','integer'))) {
                throw new ApiGatewayException("Put method expects parameter 1 to be a string or int");
            }
            
            if (!isset($args[1]) || !is_array($args[1]) || count($args[1]) == 0) {
                throw new ApiGatewayException("Put method expects parameter 2 to be a non-empty array");
            }

            $_id = $args[0];
            $payload = $args[1];
            $this->setMethod(Request::METHOD_PUT);
            $this->setRawBody(json_encode($payload));
            $this->setEncType("multipart/form-data");
            $endpoint = strtolower(substr($name, 3));
            $this->extendPath($endpoint . "/" . $_id);

        } elseif (strstr($name, 'options')) {
            
            $this->setMethod(Request::METHOD_OPTIONS);
            $endpoint = strtolower(substr($name, 7));
            $this->extendPath($endpoint);
                                              
        } elseif (strstr($name, 'delete')) {
            
            if (!isset($args[0]) || !in_array(gettype($args[0]),array('string','integer'))) {
                throw new ApiGatewayException("Delete method expects parameter 1 to be a string or int");
            }
            
            $this->setMethod(Request::METHOD_DELETE);
            $endpoint = strtolower(substr($name, 6));
            $this->extendPath($endpoint . "/" . $args[0]);            
        
        } 
        
        return $this->getRequest();

        $response = $this->run();
        
        if ($this->getMethod() == 'DELETE') {
            return $response;
        }
    
        if ($isSingular || $this->getMethod() != 'GET') {
            $response = $response->$endpoint;
            return (isset($response[0])) ? $response[0] : null;
        }

        return $response;
    }  
    
    public function extendPath($path)
    {
        return $this->getUri()->setPath($this->getUri()->getPath() . "/" . $path);
    }    
    
    public function run()
    {
        
        $response = $this->send(); 
        
        if($response->isSuccess()){
            return json_decode($response->getBody());
        }else{
            $error = json_decode($response->getBody());
            throw new ApiGatewayException($error);
        }
    } 
    
    
}


?>
