<?php

namespace Lib;

abstract class Persistence implements \JsonSerializable
{
    private static $configuration = [];
    public static function configure($host, $base, $user, $pass)
    {
        self::$configuration = [$host, $base, $user, $pass];
    }

    abstract protected function hydrate($raw);
    abstract static protected function getTableName();

    /**
     * @var \PDO
     */
    private static $db;

    /**
     * @param $id
     * @return static
     */
    public static function loadById($id)
    {
        $statement = self::query("SELECT * FROM " . static::getTableName() . " WHERE id = ?", [$id]);
        if (!$statement->rowCount()) {
            return null;
        }
        $modelClass = get_called_class();
        $model = new $modelClass();
        $model->hydrate($statement->fetch(\PDO::FETCH_ASSOC));

        return $model;
    }

    /**
     * @param array $parameters
     * @return static[]
     */
    public static function loadByParameters($parameters = [])
    {
        $statement = self::query("SELECT * FROM " . static::getTableName()
            . " WHERE " . implode(' AND ', array_map(function($paramName) {
                return $paramName . ' = ?';
            }, array_keys($parameters))), array_values($parameters));

        $models = [];
        $modelClass = get_called_class();
        while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $models[] = $model = new $modelClass();
            $model->hydrate($row);
        }
        return $models;
    }

    protected static function insert($values)
    {
        self::initDb();
        $query = "INSERT INTO " . static::getTableName()
            . '(' . implode(', ', array_keys($values)) . ') VALUES ( ?, ' . str_repeat('? ', count($values) - 1) . ')';
        $statement = self::$db->prepare($query);
        $statement->execute(array_values($values));
        return $statement->rowCount();
    }

    protected static function delete($parameters)
    {
        self::initDb();
        $query = "DELETE FROM " . static::getTableName() . " WHERE " . implode(' AND ', array_map(function($paramName) {
            return $paramName . ' = ?';
        }, array_keys($parameters)));
        $statement = self::$db->prepare($query);
        $statement->execute(array_values($parameters));
        return $statement->rowCount();
    }

    private static function initDb()
    {
        self::$db || self::$db = new \PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8', self::$configuration[0], self::$configuration[1]),
            self::$configuration[2],
            self::$configuration[3]
        );
    }

    private static function query($request, $params = [])
    {
        self::initDb();
        $statement = self::$db->prepare($request);
        $statement->execute($params);
        return $statement;
    }
}
