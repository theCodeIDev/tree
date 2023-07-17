<?php

class DeleteNodesCommand
{

    private Database $db;
    private TreeData $treeData;
    private string $user_uid;

    public function __construct()
    {
        $this->db = new Database();
        $this->treeData = new TreeData();
        $this->user_uid = Auth::getInstance()->getUserUID();
    }

    private function delete(array $childrenIds): bool
    {
        $success = true;

        try {
            //BEGIN transaction
            $this->db->beginTransaction();
            foreach ($childrenIds as $item) {
                $this->db
                    ->prepare("DELETE FROM nodes WHERE id = :id AND user_uid = :user_uid")
                    ->bind("user_uid", $this->user_uid)
                    ->bind("id", $item)
                    ->execute();
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


    private function deleteAll(): bool
    {

        $this->db
            ->prepare("DELETE FROM nodes WHERE user_uid = :user_uid")
            ->bind("user_uid", $this->user_uid)
            ->execute();

        return true;
    }


    public static function execute($parent_id): bool
    {

        $o = new self();

        if ($parent_id == "root") {
            return $o->deleteAll();
        }

        $parent_id = (int)$parent_id;
        $childrenIds = $o->treeData->getChildren($parent_id);
        $childrenIds[] = $parent_id;

        return $o->delete($childrenIds);

    }


}




