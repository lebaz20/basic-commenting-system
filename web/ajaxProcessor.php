<?php

require_once __DIR__ . '/../Service/Validator.php';
use Service\Validator;
require_once __DIR__ . '/../Service/Response.php';
use Service\Response;

$validator = new Validator();
$response = new Response();

// Validate route 'required resource and action'
$routeValidation = $validator->validateRoute();
if(! empty($routeValidation["errors"])){
    $data["errors"]["main"] = implode("<br>", $routeValidation["errors"]);
    return $response->getErrorAjaxResponse($data);
}
// Check spam using honeypots
if(!$validator->isHuman()){
    $data["errors"]["main"] = "You are not a human";
    return $response->getErrorAjaxResponse($data);
}
// Validate submitted data
$resourceObject = $routeValidation["data"]["resourceObject"];
$action = $routeValidation["data"]["action"];
$dataValidation = $resourceObject->validateData();
if(! empty($dataValidation["errors"])){
    return $response->getErrorAjaxResponse($dataValidation);
}
// Persist submitted data in database
$actionResult = $resourceObject->$action($dataValidation);
if(! empty($actionResult["errors"])){
    return $response->getErrorAjaxResponse($actionResult);
}else{
    return $response->getAjaxResponse($actionResult);
}