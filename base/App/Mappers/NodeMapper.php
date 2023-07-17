<?php

final class NodeMapper
{
    private int $id = 0;

    private int $parent_id = 0;
    private string $name = "";
    private int $hidden = 0;

    private ActiveRecord $dataModel;

    public function __construct($id = 0)
    {
        $this->dataModel = new NodeModel($id);
    }

    public function save(): int
    {
        $c = $this->dataModel->columns;
        $c->id = $this->id;
        $c->name = $this->name;
        $c->parent_id = $this->parent_id;
        $c->hidden = $this->hidden == "true" ? 1 : 0;;
        return $this->dataModel->save();
    }

    public function setId($id): void
    {
        $this->id = (int)$id;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function setHidden($hidden): void
    {
        $this->hidden = $hidden == "true" ? 1 : 0;
    }

    public function setParentId($parent_id): void
    {
        if ($parent_id == "root") $parent_id = 0;
        $this->parent_id = (int)$parent_id;
    }


    public static function toModel($data): self
    {
        $o = new self();
        $o->setId($data->id);
        $o->setParentId($data->parent_id);
        $o->setName($data->name);
        $o->setHidden($data->hidden);
        return $o;
    }
}