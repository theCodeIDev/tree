<?php

class Controller
{
    public static function index($p):void
    {
        $controller = get_called_class();

        if (isset($p) && isset($p->action) && method_exists($controller, $p->action)) {
            call_user_func($controller . '::' . $p->action, $p);
        } else {
            echo json_encode([
                "status" => "error",
                "err" => "unknown action"
            ]);
            die;
        }
    }

}