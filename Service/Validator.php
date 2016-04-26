<?php

namespace Service;

/**
 * Validator Responsible for all validations required
 */
class Validator
{

    /**
     * Validate resource and action queried
     * Prepare and instance of required resource if valid
     * 
     * @access public
     * @return array validation result, which includes error messages and prepared resource if valid
     */
    public function validateRoute()
    {
        $errorMessages = array();
        $action = filter_input(INPUT_GET, 'action');
        $resource = ucfirst(filter_input(INPUT_GET, 'resource'));
        $validationResult = array(
            "errors" => $errorMessages,
            "data" => array(
                "resourceObject" => null,
                "resource" => $resource,
                "action" => $action
                )
        );
        if (empty($validationResult["errors"]) && (empty($action) || empty($resource))) {
            $validationResult["errors"] = array("Missing submission route!");
        }
        $resourceFile = __DIR__ . DIRECTORY_SEPARATOR . $resource . '.php';
        $resourceNamespace = 'Service\\' . $resource;
        if (empty($validationResult["errors"]) && !file_exists($resourceFile)) {
            $validationResult["errors"] = array("Wrong submission directory!");
        }
        require_once $resourceFile;
        $validationResult["data"]["resourceObject"] = new $resourceNamespace();
        if (empty($validationResult["errors"]) && !method_exists($validationResult["data"]["resourceObject"], $action)) {
            $validationResult["errors"] = array("Wrong submission action!");
        }
        return $validationResult;
    }
    
    /**
     * Protect against spam using honeypots
     * Honeypots would attract bots, not humans
     * Simple addition operation and checkbox that not supposed to be checked are the types of honeypots used
     * 
     * @access public
     * @return boolean is human or not
     */
    public function isHuman(){
        $isHuman = false;
        $postId = filter_input(INPUT_POST, 'post_id');
        $fieldNameExt = (empty($postId)) ? '' : '_' . $postId;
        $a = filter_input(INPUT_POST, 'a' . $fieldNameExt );
        $b = filter_input(INPUT_POST, 'b' . $fieldNameExt );
        // should hold summation result
        $optionOne = filter_input(INPUT_POST, 'optionOne' . $fieldNameExt );
        // should be unchecked
        $optionTwo = filter_input(INPUT_POST, 'optionTwo' . $fieldNameExt );
        if(is_numeric($a) && $a > 0
                && is_numeric($b) && $b > 0
                && is_numeric($optionOne) && $optionOne > 0
                && ($optionOne == ($a + $b))  
                && $optionTwo === "0"){
            $isHuman = true;
        }
        return $isHuman;
    }
    
    /**
     * Validate input is not empty
     * 
     * @access public
     * @param string $value
     * @return string error message if invalid
     */
    public function validateRequired($value){
        $errorMessage = "";
        if(empty($value)){
            $errorMessage = "Value is required";
        }
        return $errorMessage;
    }
    
    /**
     * Validate input length
     * 
     * @access public
     * @param string $value
     * @param int $min ,default is -1 where min. length validation is disabled
     * @param int $max ,default is -1 where max. length validation is disabled
     * @return string error message if invalid
     */
    public function validateLength($value, $min = -1, $max = -1){
        $errorMessage = "";
        $valueLength = mb_strlen($value);
        if($min >= 0 && $valueLength < $min){
            $errorMessage = "Value is too short, min is " . $min;
        }
        elseif($max >= 0 && $valueLength > $max){
            $errorMessage = "Value is too long, max is " . $max;
        }
        return $errorMessage;
    }
    
    /**
     * Validate input is email
     * 
     * @access public
     * @param string $value
     * @return string error message if invalid
     */
    public function validateEmail($value){
        $errorMessage = "";
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Value is not a valid email";
        }
        return $errorMessage;
    }

}
