<?php

class TreeController extends Controller
{
    public static function saveNode($p)
    {
        $id = NodeMapper::toModel($p->data)->save();
        if ($id > 0) {
            echo json_encode(["status" => "ok", "id" => $id]);
        } else {
            echo json_encode(["status" => "error"]);
        }
    }

    public static function loadTree()
    {

        $t = new TreeData();

        echo json_encode([
            "status" => "ok",
            "id" => $t->getMaxId(),
            "data" => $t->get()
        ]);

    }

    public static function deleteNodes($p)
    {

        $q = DeleteNodesCommand::execute($p->parent_id);

        if ($q) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error"]);
        }

    }

}