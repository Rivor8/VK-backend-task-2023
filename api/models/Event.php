<?php

class Event
{
    public $name;
    public $auth;
    public $userIp;
    public $eventDate;

    public function __construct($name, $auth)
    {
        $this->name = $name;
        $this->auth = $auth;
        $this->eventDate = date("Y-m-d");
        $this->userIp = $_SERVER['REMOTE_ADDR'];
    }
}