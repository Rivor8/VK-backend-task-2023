<?php

class Router
{
    public static function route($uri, $method, $db)
    {
        if ($uri == "event" and $method == "POST") {
            $requestData = Router::getData($method);
            $event = new Event($requestData);
            if (!$db->addEvent($event))
                Router::makeResponseCode(400);
            else
                Router::makeResponseCode(200, $event);
        } elseif ($uri === "statistics" and $method == "GET") {
            $requestData = Router::getData($method);
            $data = new Statistics($requestData);
            $stat = $db->getStatistics($data);
            if ($stat === false)
                Router::makeResponseCode(400);
            else
                echo json_encode($stat);
        } else
            Router::makeResponseCode(404);
    }

    private static function getData($method)
    {
        switch ($method) {
            case 'POST':
                $rawData = file_get_contents('php://input');
                $data = json_decode($rawData);
                if ($data != null)
                    return $data;
                else
                    Router::makeResponseCode(400);
            case 'GET':
                return $_GET;

            default:
                Router::makeResponseCode(400);
        }
    }

    public static function makeResponseCode($response, $response_message = null)
    {
        switch ($response) {
            case 404:
                http_response_code(404);
                echo json_encode(["status" => "404 Not found"]);
                exit();
            case 400:
                http_response_code(400);
                echo json_encode(["status" => "400 Bad Request"]);
                exit();
            case 200:
                http_response_code(200);
                echo json_encode(["status" => "200 OK", "response" => $response_message]);
                exit();
            default:
                http_response_code($response);
                break;
        }
    }
}