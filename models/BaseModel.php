<?php

require_once(__ROOT__ . '/config/DB.php');
require_once(__ROOT__ . '/utils/ClassUtils.php');

class BaseModel
{
    private $_data;

    public static string $nome_tabella;
    protected array $_fields;

    private array $_values;
    private string $_columns;
    private string $_bind_columns;

    public ?array $errors;

    public function __construct(array $properties = array())
    {
        foreach ($this->_fields as $field)
            $this->_data[$field] = $properties[$field];
    }

    public function __set(string $property, $value)
    {
        if (method_exists($this, $method = 'set' . ucfirst($property))) {
            $this->$method($property, $value);
        }

        return $this->_data[$property] = $value;
    }

    public function __get(string $property)
    {
        if (method_exists($this, $method = 'get' . ucfirst($property))) {
            return $this->$method($property);
        }

        return array_key_exists($property, $this->_data)
            ? $this->_data[$property]
            : null;
    }

    public function __isset(string $property): bool
    {
        return array_key_exists($property, $this->_data);
    }

    private function _prepare()
    {
        $props = non_null_properties($this, $this->_fields);

        $this->_columns = implode(", ", $props);
        $this->_bind_columns = ':' . implode(", :", $props);
        $this->_values = class_to_array($this, $props);
    }

    public function save(): int
    {
        $this->_prepare();
        $updates = array();
        foreach ($this->_fields as $column) {
        $updates[] = "$column=VALUES($column)";
        }
        $updates_str = implode(',', $updates);
        $sql = "INSERT INTO " . static::$nome_tabella . " ($this->_columns) VALUES ($this->_bind_columns) ON DUPLICATE KEY UPDATE $updates_str";
        $sth = DB::get()->prepare($sql);
        $sth->execute($this->_values);

        if (isset($this->_values["id"]) && $this->_values["id"] != '') {
            return (int) $this->_values["id"];
        } else {
            return (int) DB::get()->lastInsertId();
        }
    }

    public static function all(): array
    {
        $sql = 'SELECT * FROM ' . static::$nome_tabella;
        $list = DB::get()->query($sql)->fetchAll();

        $entities = array();
        foreach ($list as $row) {
            $entities[] = new static($row);
        }

        return $entities;
    }

    public static function select(array $selection): array
    {
        $columns = implode(',', $selection);
        $sql = 'SELECT $columns FROM ' . static::$nome_tabella;
        $list = DB::get()->query($sql)->fetchAll();

        $entities = array();
        foreach ($list as $row) {
            $entities[] = new static($row);
        }

        return $entities;
    }

    public static function where(array $conditions): array
    {
        $sql = 'SELECT * FROM ' . static::$nome_tabella . ' WHERE';

        $where = '';
        foreach ($conditions as $key => $prop) {
            if ($where == '')
                $where .= " $key = :$key";
            else
                $where .= " AND $key = :$key";
        }
        $sql .= $where;

        $sth = DB::get()->prepare($sql);
        $sth->execute($conditions);
        $list = $sth->fetchAll();

        $entities = array();
        foreach ($list as $row) {
            $entities[] = new static($row);
        }

        return $entities;
    }
    
    public static function get(int $id)
    {
        $query = "SELECT * FROM " . static::$nome_tabella  . " WHERE id=:id";
        $sth = DB::get()->prepare($query);
        $sth->execute([
            'id' => $id,
        ]);
        $row = $sth->fetch();

        return new static($row);
    }

    public static function delete(int $id): void
    {
        //TODO: Controllo se esiste ID
        $query = "DELETE FROM " . static::$nome_tabella  . " WHERE id = :id;";
        $sth = DB::get()->prepare($query);
        $sth->execute([
            'id' => $id,
        ]);
    }

    public function validate(): bool
    {
        return true;
    }
}
