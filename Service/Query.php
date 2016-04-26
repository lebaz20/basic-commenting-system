<?php

namespace Service;

require_once __DIR__ . '/../config.php';
use PDO;

/**
 * Query Responsible for having a connection with database
 */
class Query
{

    /**
     *
     * @var PDO database connection
     */
    public static $connection;
    
    /**
     * Start mysql database connection using provided credentials
     * @access public
     */
    public static function startConnection()
    {
        $host = "localhost";
        $name = DATABASE_NAME;
        $port = DATABASE_PORT;
        $user = DATABASE_USER;
        $password = DATABASE_PASSWORD;
        self::$connection = new PDO("mysql:host=$host;dbname=$name;port=$port", $user, $password, array(
            PDO::ATTR_TIMEOUT => 5, // set connection timeout to 5 secs
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // use exceptions for errors
        ));
    }

    /**
     * Kill existing database connection
     * @access public
     */
    public static function killConnection()
    {
        //close connection
        self::$connection = null;
    }

}
