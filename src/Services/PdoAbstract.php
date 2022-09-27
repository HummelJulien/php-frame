<?php

namespace Hummel\PhpFrame\Services;

use PDO;

abstract class PdoAbstract
{
    /**
     * YAML key config
     */
    const KEY_YAML = ['driver', 'host', 'dbname', 'username', 'password'];

    /**
     * Store pdoconfig for reset instance
     * @var array
     */
    private array $pdoConfig;

    /**
     * PDO storage instance
     * @var PDO
     */
    protected ?PDO $pdo;

    /**
     * String base for left join contruct
     * @var string
     */
    protected string $join_string_base = " %s %s ON %s = %s";

    /**
     * Left join constructed string
     * @var string|null
     */
    protected ?string $join_string = null;

    /**
     * Use for safe join
     * @var string|null
     */
    protected ?string $select_string = null;

    /**
     * For reste PDO dev only
     * @var bool
     */
    protected bool $resetable = false;

    /**
     * @throws \Exception
     */
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
            $this->pdoConfig = $pdoConfig['pdo'];
        }
        $this->upPDOObject();
    }

    /** Create instance of PDO
     * @return void
     */
    private function upPDOObject()
    {
        $this->pdo = new PDO($this->pdoConfig['driver'].':host='.$this->pdoConfig['host'].';dbname='.$this->pdoConfig['dbname'], $this->pdoConfig['username'], $this->pdoConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    private function resetPDOObject()
    {
        $this->pdo = null;
        $this->resetable = false;
        $this->upPDOObject();
    }

    /**
     * Return PDO instance
     * @return PDO
     */
    private function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * @Todo Feature that not implemented
     * @param string $tableName
     * @return array
     */
    protected final function discoverTable(string $tableName): array
    {
        $sql = 'SELECT * FROM ' . $tableName . ' LIMIT 1';
        $query = $this->getPdo()->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * ORM->getAll() to getAllRequest() converter
     * @param string $tableName
     * @return array
     */
    protected final function getAllRequest(string $tableName, array $selectable = null): array
    {
        $this->resetable ? $this->resetPDOObject() : null;
        $select = '';
        if (!empty($selectable)) {
            $length = count($selectable);
            $count = 0;
            foreach ($selectable as $key => $column) {
                $count++;
                if ($count === $length) {
                    $select .= ' '. $key . ' '.$column;
                } else {
                    $select .= ' '. $key . ' '.$column . ' ,';
                }

            }
        }
        else {
            $select = ' *';
        }

        $sql = 'SELECT' . $select . ' FROM ' . $tableName . ' ';

        if (!is_null($this->join_string)) {
            $sql .= $this->join_string;
            $this->join_string = null;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Orm request to PDO request construtor
     *
     * @param $tableName
     * @param $criteria
     * @return array
     */
    protected final function getTableArrayByCriteria($tableName, $criteria, $selectable): array
    {
        $this->resetable ? $this->resetPDOObject() : null;
        $select = '';
        if (!empty($selectable)) {
            $length = count($selectable);
            $count = 0;
            foreach ($selectable as $key => $column) {
                $count++;
                if ($count === $length) {
                    $select .= ' '. $key . ' '.$column;
                } else {
                    $select .= ' '. $key . ' '.$column . ' ,';
                }

            }
        }
        else {
            $select = ' *';
        }

        if (!is_null($this->join_string)) {
            $sql = 'SELECT' . $select . ' FROM ' . $tableName .' '. $this->join_string . ' WHERE ';
            $this->join_string = null;
        } else {
            $sql = 'SELECT' . $select . ' FROM ' . $tableName .' WHERE ';
        }
        $length = count($criteria);
        $count = 0;
        $execute = [];
        foreach ($criteria as $key => $value) {
            $count++;
            if (strpos($key, '.')) {
                $key_binding = str_replace('.', '', $key);
            } else {
                $key_binding = $key;
            }
            if ($count === $length) {
                $sql .= $key . ' = :' . $key_binding;
            } else {
                $sql .= $key . ' = :' . $key_binding . ' AND ';
            }
            $execute[':'.$key_binding] = $value;
        }

        $sql .= ' LIMIT 1;';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create query and execute for new entry of one orm object
     *
     * @param string $tableName
     * @param array $data
     * @return void
     */
    protected final function createEntry(string $tableName, array $data): void
    {
        $sql = 'INSERT INTO ' . $tableName . ' (';
        $length = count($data);
        $count = 0;
        $execute = [];
        $sqlValueKeys = ' VALUE ( ';
        foreach ($data as $key => $value) {
            $count++;
            if ($count === $length) {
                $sql .= $key;
                $sqlValueKeys .= ' :' . $key;
            } else {
                $sql .= $key . ' , ';
                $sqlValueKeys .= ':' . $key . ' , ';
            }
            $execute[':'.$key] = $value;
        }
        $sql .= ')' . $sqlValueKeys . ' );';


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute);
    }

    protected final function updateEntry(string $tableName, array $data): void
    {
        $sql = 'UPDATE ' . $tableName . ' SET ';
        $length = count($data);
        $count = 0;
        $execute = [];
        foreach ($data as $key => $value) {
            $count++;
            if ($key !== 'id') {
                if ($count === $length) {
                    $sql .= $key . ' = :' . $key;
                } else {
                    $sql .= $key . ' = :' . $key . ' , ';
                }
            }
            $execute[':'.$key] = $value;
        }
        $sql .= ' WHERE id = :id ;';


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute);
    }

    protected final function deleteEntry(string $tableName, array $data): void
    {
        $sql = 'DELETE FROM ' . $tableName . ' WHERE id = :id ;';
        $execute[':id'] = $data['id'];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute);
    }

    protected final function setJoinString(string $type_of_join, string $name_second_table, string $cond_table_primary, string $cond_table_secondary): void
    {
        //"LEFT JOIN %s ON %s = %s";
        $this->join_string = sprintf($this->join_string_base, $type_of_join, $name_second_table, $cond_table_primary, $cond_table_secondary);
    }
}
