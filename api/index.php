<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once("db/Database.php");
require_once("Router.php");
require_once("models/Event.php");


$db = new Database();
$conn = $db->getConnection();
$db->createEventsTable();

$uri = trim(explode("?", $_SERVER['REQUEST_URI'])[0], "/");
$method = $_SERVER['REQUEST_METHOD'];

Router::route($uri, $method, $db);