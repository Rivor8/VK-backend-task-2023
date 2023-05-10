<?php

class Statistics
{
    public $aggregation;
    public $names;
    public $daterange;

    public function __construct($data)
    {
        if (!in_array($data["aggregation"], ["byname", "byuserip", "bystatus"])) {
            Router::makeResponseCode(400);
        }
        $this->aggregation = $data["aggregation"];

        if (!is_array($data["names"]) and array_key_exists("names", $data)) {
            Router::makeResponseCode(400);
        } elseif (is_array($data["names"])) {
            if (count($data["names"]) == 0) {
                Router::makeResponseCode(400);
            } else {
                $this->names = $data["names"];
            }
        } else {
            $this->names = null;
        }
        if (!is_array($data["daterange"]) and array_key_exists("daterange", $data)) {
            Router::makeResponseCode(400);
        } elseif (is_array($data["daterange"])) {
            if (count($data["daterange"]) == 2) {
                if ($this->validateDate($data["daterange"][0]) and $this->validateDate($data["daterange"][1])) {
                    $this->daterange = $data["daterange"];
                } else
                    Router::makeResponseCode(400);
            } else
                Router::makeResponseCode(400);
        } else {
            $this->daterange = null;
        }
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}