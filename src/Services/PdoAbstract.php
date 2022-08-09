<?php

namespace Hummel\PhpFrame\Services;

use PDO;

abstract class PdoAbstract
{
    const KEY_YAML = ['driver', 'host', 'dbname', 'username', 'password'];

    protected PDO $pdo;

    public final function __construct()
    {
        $pdoConfig = yaml_parse_file('../config/pdo.yaml');

        if ($pdoConfig === false || !is_array($pdoConfig)) {
            throw new \Exception('Pdo config file is not valid');
        }

        if (array_key_exists('pdo', $pdoConfig)) {
            foreach (self::KEY_YAML as $key) {
                if (!array_key_exists($key, $pdoConfig['pdo'])) {
                    throw new \Exception('Pdo config file is not valid');
                }
            }
            $pdoConfig = $pdoConfig['pdo'];
        }

        $this->pdo = new PDO($pdoConfig['driver'].':host='.$pdoConfig['host'].';dbname='.$pdoConfig['dbname'], $pdoConfig['username'], $pdoConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    private function getPdo(): PDO
    {
        return $this->pdo;
    }

    protected final function discoverTable(string $tableName): array
    {
        $sql = 'SELECT * FROM ' . $tableName . ' LIMIT 1';
        $query = $this->getPdo()->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    protected final function getAllRequest(string $tableName): array
    {
        $sql = 'SELECT * FROM ' . $tableName;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected final function getTableArrayByCriteria($tableName, $criteria): array
    {
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE ';
        $length = count($criteria);
        $count = 0;
        $execute = [];
        foreach ($criteria as $key => $value) {
            $count++;
            if ($count === $length) {
                $sql .= $key . ' = :' . $key;
            } else {
                $sql .= $key . ' = :' . $key . ' AND ';
            }
            $execute[':'.$key] = $value;
        }
        $sql .= ' LIMIT 1';


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute);
        return $stmt->fetchAll();
    }
}