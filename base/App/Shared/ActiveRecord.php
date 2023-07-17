<?php

class ActiveRecord
{

    private static string $table = "";
    public static string $id_column = "id";
    public ?array $db_error = null;
    public ?stdClass $columns = null;
    private ?stdClass $fields = null;

    public array $data = [];

    private Database $db;

    private function init()
    {


        $this->columns = new stdClass();
        $this->fields = new stdClass();

        $this->db->prepare(
            " 
                SELECT  lower(COLUMN_NAME)  as column_name,
                        lower(DATA_TYPE)    as data_type,
                        COLUMN_DEFAULT      as def
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE   TABLE_NAME      = :table "
        );

        $this->db->bind("table", static::$table);
        $properties = $this->db->execute_all();

        foreach ($properties as $property) {

            $this->fields->{$property['column_name']} = $property['data_type'];

            switch ($property['data_type']) {
                case 'int':
                    $this->columns->{$property['column_name']} = 0;
                    settype($this->columns->{$property['column_name']}, "int");
                    break;

                case 'decimal':
                    $this->columns->{$property['column_name']} = 0;
                    break;

                case 'datetime':
                    $this->columns->{$property['column_name']} = "1999-01-01 00:00:00";
                    break;

                default:
                    settype($this->columns->{$property['column_name']}, "string");
                    break;
            }
        }
    }

    public function __construct($id = 0)
    {
        if (static::$table == "") static::$table = __CLASS__;
        $this->db = new Database();
        $this->init();
        $this->findById($id);
    }

    public function findById($id = 0)
    {

        if (intval($id) > 0) {

            $sql = [];

            foreach ($this->columns as $key => $value) {
                $sql[] = '`' . $key . '`';
            }

            $this->db->prepare("SELECT " . implode(', ', $sql) . " FROM `" . static::$table . "` WHERE `" . static::$id_column . "` = :id");
            $this->db->bind("id", $id);
            $row = $this->db->execute_all();

            if ($row) {
                $itm = $row[0];
                foreach ($this->columns as $key => $value) {
                    $this->columns->{$key} = $itm[$key];
                }
                $this->data = $this->toArray();
            }
        }
    }

    /**
     * save model (insert or update)
     *
     * @param array $dm_excl - exclude fields in save (default [])
     * @return int - id of saved model (-1 - error)
     */
    public function save(array $dm_excl = []): int
    {

        if (isset($this->dm_excl)) {
            $dm_excl = array_merge($dm_excl, $this->dm_excl);
        }

        $cols = [];
        $column_list = [];
        $value_list = [];

        foreach ($this->columns as $name => $value) {
            if (!in_array($name, $dm_excl)) {
                if ($name != static::$id_column) {
                    $cols[] = "`$name` = :" . $name;
                }
                $value_list[] = ":" . $name;
            }
        }

        $cols = implode(', ', $cols);
        $column_list = implode(', ', $column_list);
        $value_list = implode(', ', $value_list);

        $sql = "INSERT INTO " . static::$table . " ($column_list) VALUES ($value_list) ON DUPLICATE KEY UPDATE $cols;";
        $this->db->prepare($sql);

        foreach ($this->columns as $name => $value) {
            if (!in_array($name, $dm_excl)) {
                $this->db->bind($name, $value);
            }
        }

        $q = $this->db->execute();

        if (!$q) {
            $this->db_error = $this->db;
            return -1;
        }
        $ret = $this->columns->{static::$id_column};

        if ($ret == 0) {
            return $this->db->lastInsertId();
        }

        return $ret;
    }

}
