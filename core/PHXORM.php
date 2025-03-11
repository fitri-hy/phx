<?php

namespace Core;

use ReflectionClass;
use Core\PHXDatabase;

abstract class PHXORM
{
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];
    protected $database;

    public function __construct()
    {
        // Using an existing PHXDatabase
        $this->database = PHXDatabase::getInstance();
    }

    // Get table name based on class name
    public function getTableName()
    {
        if (isset($this->table)) {
            return $this->table;
        }
        $className = strtolower((new ReflectionClass($this))->getShortName());
        return $className . 's'; // Add "s" for plural
    }

    // Save data to table
    public function save()
    {
        $fields = implode(', ', array_keys($this->attributes));
        $values = implode(', ', array_map(function ($value) {
            return ":$value";
        }, array_keys($this->attributes)));

        $sql = "INSERT INTO {$this->getTableName()} ($fields) VALUES ($values)";
        $stmt = $this->database->getConnection()->prepare($sql);

        foreach ($this->attributes as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    // Get all data from table
    public function all()
    {
        $sql = "SELECT * FROM {$this->getTableName()}";
        $result = $this->database->getConnection()->query($sql)->fetchAll();
        return $result;
    }

    // Retrieve data based on ID
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE {$this->primaryKey} = :id";
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    // Change attribute data
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    // Update data based on ID
    public function update($id, $attributes)
    {
        $setClause = implode(', ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($attributes)));

        $sql = "UPDATE {$this->getTableName()} SET $setClause WHERE {$this->primaryKey} = :id";
        $stmt = $this->database->getConnection()->prepare($sql);

        // Binding values
        foreach ($attributes as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(":id", $id);

        return $stmt->execute();
    }

    // Delete data by ID
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->getTableName()} WHERE {$this->primaryKey} = :id";
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // Query with WHERE condition
    public function where($conditions)
    {
        $whereClause = implode(' AND ', array_map(function ($key, $value) {
            return "$key = :$key";
        }, array_keys($conditions), $conditions));

        $sql = "SELECT * FROM {$this->getTableName()} WHERE $whereClause";
        $stmt = $this->database->getConnection()->prepare($sql);

        // Binding values
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // JOIN query
    public function join($table, $onCondition, $type = 'INNER')
    {
        $sql = "{$type} JOIN $table ON $onCondition";
        $sql = "SELECT * FROM {$this->getTableName()} $sql";
        $result = $this->database->getConnection()->query($sql)->fetchAll();
        return $result;
    }

    // Query with GROUP BY
    public function groupBy($columns)
    {
        $groupByClause = implode(', ', (array)$columns);
        $sql = "SELECT * FROM {$this->getTableName()} GROUP BY $groupByClause";
        $result = $this->database->getConnection()->query($sql)->fetchAll();
        return $result;
    }

    // Query with HAVING (usually after GROUP BY)
    public function having($groupByColumns, $havingConditions)
    {
        $groupByClause = implode(', ', (array)$groupByColumns);
        $havingClause = implode(' AND ', array_map(function ($key, $value) {
            return "$key = :$key";
        }, array_keys($havingConditions), $havingConditions));

        $sql = "SELECT * FROM {$this->getTableName()} GROUP BY $groupByClause HAVING $havingClause";
        $stmt = $this->database->getConnection()->prepare($sql);

        // Binding values
        foreach ($havingConditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Query with ORDER BY
    public function orderBy($columns, $direction = 'ASC')
    {
        $orderByClause = implode(', ', (array)$columns);
        $sql = "SELECT * FROM {$this->getTableName()} ORDER BY $orderByClause $direction";
        $result = $this->database->getConnection()->query($sql)->fetchAll();
        return $result;
    }

	// Query with LIMIT
    public function limit($limit, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->getTableName()} LIMIT :limit OFFSET :offset";
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Query with DISTINCT
    public function distinct($columns)
    {
        $columnsList = implode(', ', (array)$columns);
        $sql = "SELECT DISTINCT $columnsList FROM {$this->getTableName()}";
        $result = $this->database->getConnection()->query($sql)->fetchAll();
        return $result;
    }

    // Query with COUNT (counting the number of records)
    public function count($column = '*')
    {
        $sql = "SELECT COUNT($column) FROM {$this->getTableName()}";
        $result = $this->database->getConnection()->query($sql)->fetchColumn();
        return $result;
    }

    // Query to run raw query
    public function raw($sql, $params = [])
    {
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
