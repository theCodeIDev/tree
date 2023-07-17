<?php

class DeleteNodesCommand
{

    private Database $db;
    private TreeData $treeData;

    public function __construct()
    {
        $this->db = new Database();
        $this->treeData = new TreeData();
    }

    private function delete(array $childrenIds)
    {
        $success = true;

        try {
            //BEGIN transaction
            $this->db->beginTransaction();
            foreach ($childrenIds as $item) {
                $this->db->prepare("DELETE FROM nodes WHERE id = :id")->bind("id", $item)->execute();
            }
        } catch (Exception $e) {
            $success = false;
        } finally {
            // End transaction
            if ($this->db->inTransaction()) {
                if ($success) {
                    $this->db->commit();
                } else {
                    $this->db->rollback();
                }
            }
        }

        return $success;
    }


    public static function execute($parent_id)
    {

        if ($parent_id == "root") $parent_id = 0;
        $parent_id = (int)$parent_id;

        $o = new self();
        $childrenIds = $o->treeData->getChildren($parent_id);
        $childrenIds[] = $parent_id;

        return $o->delete($childrenIds);

    }


}




