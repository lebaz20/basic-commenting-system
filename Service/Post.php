<?php

namespace Service;

require_once __DIR__ . '/Query.php';
require_once __DIR__ . '/Validator.php';

use Service\Query;
use Service\Validator;
use PDOException;
use PDO;

/**
 * Post Responsible for interaction with post entity
 */
class Post
{

    /**
     * Get posts and comments in multi-dimensional format
     * 
     * @access public
     * @return array posts with related comments if exist
     */
    public static function getPosts()
    {
        $customSeparator = ",randomSeparatorForMessagesToAvoidSameCharactersBeingUsed-a-s-d-%-#-$-*-@-";
        Query::startConnection();
        $postsQuery = Query::$connection->query("SELECT post.*, GROUP_CONCAT(comment.id) AS commentsId, GROUP_CONCAT(comment.created) AS commentsCreated, GROUP_CONCAT(comment.name) AS commentsName, GROUP_CONCAT(comment.message SEPARATOR '$customSeparator') AS commentsMessage from post LEFT JOIN comment ON post.id=comment.post_id Group by post.id");
        $posts = $postsQuery->fetchAll(/* $fetchStyle = */PDO::FETCH_ASSOC);
        Query::killConnection();
        foreach ($posts as &$post) {
            $post["comments"] = array();
            if (!empty($post["commentsId"])) {
                $commentIds = explode(/* $delimiter */ ",", $post["commentsId"]);
                $commentNames = explode(/* $delimiter */ ",", $post["commentsName"]);
                $commentsCreated = explode(/* $delimiter */ ",", $post["commentsCreated"]);
                $commentsMessages = explode(/* $delimiter */ $customSeparator, $post["commentsMessage"]);
                $comments = array();
                foreach ($commentIds as $commentIndex => $commentId) {
                    $comments[] = array(
                        "id" => $commentId,
                        "name" => $commentNames[$commentIndex],
                        "message" => $commentsMessages[$commentIndex],
                        "created" => $commentsCreated[$commentIndex],
                    );
                }
                $post["comments"] = $comments;
            }
        }

        return $posts;
    }

    /**
     * Insert post entry in database
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
            $postsQuery = Query::$connection->prepare("INSERT INTO post(name,email,message,created) VALUES(:name,:email,:message,:created)");
            $postsQuery->execute(array(
                ':name' => $data["data"]["name"],
                ':email' => $data["data"]["email"],
                ':message' => $data["data"]["message"],
                ':created' => $data["data"]["created"],
            ));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $data["errors"] = array("main" => "Post failed to be created!");
        }
        $data["data"]["id"] = Query::$connection->lastInsertId();
        Query::killConnection();
        return $data;
    }

    /**
     * Validate submitted data before inserting in database
     * 
     * @access public
     * @return array data submitted and validation errors if exist
     */
    public function validateData()
    {
        $errorMessages = array();
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email');
        $message = filter_input(INPUT_POST, 'message');

        $validator = new Validator();
        $isValid = true;
        $nameErrors = array();
        if (!empty($nameError = $validator->validateRequired($name))) {
            $nameErrors[] = $nameError;
            $isValid = false;
        }
        if (!empty($nameError = $validator->validateLength($name, 3, 120))) {
            $nameErrors[] = $nameError;
            $isValid = false;
        }

        $emailErrors = array();
        if (!empty($emailError = $validator->validateRequired($email))) {
            $emailErrors[] = $emailError;
            $isValid = false;
        }
        if (!empty($emailError = $validator->validateLength($email, 5, 120))) {
            $emailErrors[] = $emailError;
            $isValid = false;
        }
        if (!empty($emailError = $validator->validateEmail($email))) {
            $emailErrors[] = $emailError;
            $isValid = false;
        }

        if (!empty($messageError = $validator->validateRequired($message))) {
            $isValid = false;
        }
        if ($isValid === false) {
            $errorMessages = array(
                "name" => implode("<br>", $nameErrors),
                "email" => implode("<br>", $emailErrors),
                "message" => $messageError
            );
        }
        return array(
            "errors" => $errorMessages,
            "data" => array(
                "name" => $name,
                "email" => $email,
                "message" => $message
            )
        );
    }

}
