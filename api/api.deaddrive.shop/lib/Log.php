<?php

class Log {
    private $logs = [];
    
    public function __construct() {
        $this->log("Date", date("Y-m-d H:i:s"));
    }

    public function log($key, $message) {
        if (!isset($this->logs[$key])) {
            $this->logs[$key] = [];
        }
        $this->logs[$key][] = $message;
    }

    public function getlogs() {
        return $this->logs;
    }
}
