<?php

class Event
{
    public $name;
    public $auth;
    public $userIp;
    public $eventDate;

    public function __construct($data)
    {
        if (!(is_string($data->name) and is_bool($data->auth))) {
            Router::makeResponseCode(400);
        }
        $this->name = $data->name;
        $this->auth = $data->auth;
        $this->eventDate = date("Y-m-d");
        $this->userIp = $_SERVER['REMOTE_ADDR'];
    }
}