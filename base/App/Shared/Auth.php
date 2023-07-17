<?php

class Auth
{
    private static ?Auth $instance = null;

    private function __construct()
    {
        session_start();
    }

    public static function getInstance(): Auth
    {
        if (!self::$instance) {
            self::$instance = new Auth();
        }

        return self::$instance;
    }

    public function getUserUID():string
    {
        return session_id();
    }


}