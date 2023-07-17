<?php

final class TreeData
{

    private Database $db;

    private array $rawData = [];

    public function __construct()
    {
        $this->db = new Database();
        $this->getAllData();
    }

    public function get(): array
    {
        return $this->renderTree($this->rawData, 0);
    }

    public function getMaxId()
    {
        $ids = array_column($this->rawData, 'id');
        $intIds = array_map('intval', $ids);

        if (!$intIds) {
            return 0;
        } else {
            return max($intIds);
        }

    }

    private function getAllData(): void
    {
        $this->rawData = $this->db->select("SELECT * FROM nodes");
    }


    private function renderTree($data, $parentId): array
    {

        $result = [];

        foreach ($data as $item) {

            if ($item['parent_id'] == $parentId) {

                $convertedItem = [
                    'id' => (string)$item['id'],
                    'name' => $item['name'],
                    'hidden' => (bool)$item['hidden'],
                    'parent_id' => $item['parent_id'] == 0 ? 'root' : (string)$item['parent_id'],
                    'data' => $this->renderTree($data, $item['id'])
                ];

                $result[] = $convertedItem;
            }

        }

        return $result;
    }

    public function getChildren(int $parentId)
    {
        return $this->getChildrenIds($this->rawData, $parentId);
    }

    private function getChildrenIds(array $data, int $parentId): array
    {

        $childrenIds = [];
        foreach ($data as $item) {
            if ($item['parent_id'] == $parentId) {
                $childrenIds[] = $item['id'];
                $childrenIds = array_merge($childrenIds, $this->getChildrenIds($data, (int)$item['id']));
            }
        }

        return $childrenIds;
    }


}