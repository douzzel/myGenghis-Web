<?php

$config_file = file_exists(__DIR__ . '/../config.php') ? 'config.php' : 'config.sample.php';

require_once __DIR__ . "/../{$config_file}";

class MYSQLPDO
{
    private $user;
    private $pass;
    private $host;
    private $dbName;
    private $connexion;

    public function __construct($db)
    {
        // DB Config is editable in config.php file (see config.sample.php)
        $this->user = MYSQL_USER;
        $this->pass = MYSQL_PASSWORD;
        $this->host = MYSQL_HOST;
        $this->dbName = MYSQL_USER . '_myg_demo_' . $db;
        $this->connexion = null;
    }

    //**************************************************************************** */
    public function getConnexion()
    {
        if (!$this->connexion) {
            try {
                $this->connexion = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbName . ';charset=utf8mb4', $this->user, $this->pass);
            } catch (PDOException $exception) {
                echo 'erreur de connexion est ' . $exception->getMessage();
            }
        }

        return $this->connexion;
    }

    //****************************************************************************** */
    public static function insert($table, $data)
    {
        $sql = "INSERT INTO `{$table}` ";
        $class = explode('_', $table);
        $className = UtilsFunctions::className($class, $table);

        $reflect = new ReflectionClass($className);
        foreach ($reflect->getProperties() as $value) {
            if (array_key_exists($value->name, $data)) {
                $proprety[] = '`' . $value->name . '`';
                $propretyBind[] = ':' . $value->name;
            }
        }
        $sql .= '(' . join(',', $proprety) . ') VALUES(' . join(',', $propretyBind) . ')';

        return $sql;
    }

    //*************************************************************************** */
    public static function select($table, $filter = [], $order = "", $limit = "")
    {
        $sql = "SELECT * FROM `{$table}` ";
        if (count($filter) > 0) {
            $count = 0;
            foreach ($filter as $key => $value) {
                if ($value && preg_match("/^`.+`$/", $value)) {
                    $sql .= 0 === $count ?
                        "WHERE `{$key}` = {$value}"
                        : "AND `{$key}` = {$value}";
                } else {
                    $sql .= 0 === $count ?
                        "WHERE `{$key}` = '{$value}'"
                        : "AND `{$key}` = '{$value}'";
                }
                ++$count;
            }
        }
        $sql .= $order ? " ORDER BY {$order}" : "";
        $sql .= $limit ? " LIMIT {$limit}" : "";

        return $sql;
    }

    //*************************************************************************** */
    public static function customSelect($table, $data, $order = "", $limit = "")
    {
        $sql = "SELECT * FROM `{$table}` {$data}";
        $sql .= $order ? " ORDER BY {$order}" : "";
        $sql .= $limit ? " LIMIT {$limit}" : "";

        return $sql;
    }

    //*************************************************************************** */
    public static function selectInner($table1, $table2, $table3, $keyTable1, $filter = [], $order = [])
    {
        $sql = "SELECT * FROM `{$table1}`  JOIN `{$table2}` ON `{$table1}`.id = `{$table2}`.`{$keyTable1}` JOIN `{$table3}` ON `{$table1}`.id = `{$table3}`.`{$keyTable1}`";
        if (count($filter) > 0) {
            $count = 0;
            foreach ($filter as $key => $value) {
                $sql .= 0 === $count ?
                    "AND {$key} = '{$value}'"
                    : "AND {$key} = '{$value}'";
                ++$count;
            }
        }
        $sql .= $order ? " ORDER BY {$order}" : "";

        return $sql;
    }

    //*************************************************************************** */
    public static function selectMaxId($table)
    {
        return "SELECT MAX(id) FROM `{$table}` ";
    }

    //******************************************************************************* */
    public static function update($table, $filter = [], $data = [])
    {
        $sql = "UPDATE `{$table}` SET";
        $count = 0;
        $countData = count($data) - 1;

        foreach ($data as $key => $value) {
            $value = (null === $value ? 'NULL ' : "'{$value}'");
            $sql .= $count === $countData ?
                "`{$key}` = {$value}"
                : "`{$key}` = {$value},";
            ++$count;
        }

        $count = 0;
        foreach ($filter as $key => $value) {
            $sql .= 0 === $count ?
                "WHERE `{$key}` = '{$value}'"
                : "AND `{$key}` = '{$value}'";
            ++$count;
        }

        return $sql;
    }

    //***************************************************************************** */
    public static function delete($table, $filter = [])
    {
        $sql = "DELETE FROM `{$table}` ";
        if (count($filter) > 0) {
            $count = 0;
            foreach ($filter as $key => $value) {
                $sql .= 0 === $count ?
                    "WHERE `{$key}` = '{$value}'"
                    : "AND `{$key}` = '{$value}'";
                ++$count;
            }
        }

        return $sql;
    }

    //*************************************************************************** */
}
