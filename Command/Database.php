<?php

require_once __DIR__ . '/../config.php';

// get passed options 'action'
$options = getopt(/* $shortopts = */ "", $longopts = array(
    "action:", // Required value
        ));
$action = $options["action"];

// current supported action is create, which will create needed tables if not existing
$mysqlArguments = "-u" . DATABASE_USER . " -p'" . DATABASE_PASSWORD . "' -D" . DATABASE_NAME . " --port ".DATABASE_PORT ;
switch ($action) {
    case "create":
        $mysqlCreateCommand = "mysql $mysqlArguments < " . __DIR__ . "/../sql/%s.sql";
        shell_exec(sprintf($mysqlCreateCommand, "post"));
        shell_exec(sprintf($mysqlCreateCommand, "comment"));
        break;
}