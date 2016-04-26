<?php

namespace Service;

require_once __DIR__ . '/Query.php';
require_once __DIR__ . '/Validator.php';
use Service\Query;
use Service\Validator;

/**
 * Comment Responsible for interaction with comment entity
 */
class Comment {
 
    /**
     * Insert comment entry in database
     * 
     * @access public
     * @param array $data submitted data
     * @return array data saved in database
     */
    public function create($data)
    {
        Query::startConnection();
        $data["data"]["created"] = date('Y-m-d H:i:s');
        try {
            $commentsQuery = Query::$connection->prepare("INSERT INTO comment(name,message,post_id,created) VALUES(:name,:message,:post_id,:created)");
            $commentsQuery->execute(array(
                ':name' => $data["data"]["name"],
                ':message' => $data["data"]["message"],
                ':post_id' => $data["data"]["postId"],
                ':created' => $data["data"]["created"],
            ));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $data["errors"] = array("main" => "Comment failed to be created!");
        }
        Query::killConnection();
        return $data;
    }
    
    /**
     * Validate submitted data before inserting in database
     * 
     * @access public
     * @return array data submitted and validation errors if exist
     */
    public function validateData(){
        $errorMessages = array();
        $postId = filter_input(INPUT_POST, 'post_id' );
        $fieldNameExt = "_" . $postId;
        $name = filter_input(INPUT_POST, 'name' . $fieldNameExt );
        $message = filter_input(INPUT_POST, 'message'. $fieldNameExt);
        
        $validator = new Validator();
        $isValid = true;
        $nameErrors = array();
        if(! empty($nameError = $validator->validateRequired($name))){
            $nameErrors[] = $nameError;
            $isValid = false;
        }
        if(! empty($nameError = $validator->validateLength($name, 3, 120))){
            $nameErrors[] = $nameError;
            $isValid = false;
        }
        
        if(! empty($messageError = $validator->validateRequired($message))){
            $isValid = false;
        }
        if($isValid === false){
            $errorMessages = array(
                "name" . $fieldNameExt => implode("<br>", $nameErrors),
                "message" . $fieldNameExt => $messageError
            );
        }
        return array(
            "errors" => $errorMessages,
            "data" => array(
                "postId" => $postId,
                "name" => $name,
                "message" => $message
            )
        );
    }
}

