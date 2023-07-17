<?php


$root = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR.'base'. DIRECTORY_SEPARATOR.'App';
$sess_inc = $root . DIRECTORY_SEPARATOR. 'sess.php';

if (file_exists($sess_inc)) {
    require_once $sess_inc;
} else {
    die("sess.php not found");
}


try {

    $entityBody = file_get_contents('php://input');
    $data = json_decode($entityBody);
    $p = (object)array();


    if (isset($data->m)) {
        $m = $data->m;
    } else {
        die("{m: not defined ,p:{...}}");
    }

    if (isset($data->p)) {
        $p = (object)$data->p;
    }

    if (method_exists($m, $p->action)) {
        return $m::{$p->action}($p);
    } else {
        echo json_encode([
            "status" => "error",
            "err" => "Controller '$m' or Action '" . $p->action . "' not found"
        ]);
    }


} catch (Throwable $t) {
    echo json_encode(["status" => "error", "err" => $t->getMessage()]);
}