<?php

namespace Service;

/**
 * Response Responsible for response returned to browser
 */
class Response {
    
    /**
     * Generate ajax response
     * Normally response is valid, unless otherwise stated
     * 
     * @access public
     * @param array $data response data
     */
    public function getAjaxResponse($data){
        if(!array_key_exists("isValid", $data)){
            $data["isValid"] = true;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    /**
     * Generate ajax response in case of error
     * Set isValid to false
     * 
     * @access public
     * @param array $data response data
     */
    public function getErrorAjaxResponse($data){
        $data["isValid"] = false;
        $this->getAjaxResponse($data);
    }
}

