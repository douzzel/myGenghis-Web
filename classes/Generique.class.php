<?php

class Generique
{
    //*********************************************************************************************** */
    public static function insert(string $table, string $db, array $data): void
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $requete = $connexion->prepare($database::insert($table, $data));
        foreach ($data as $key => $value) {
            $requete->bindValue(':'.$key, $value);
        }
        $requete->execute();
        /*print_r($requete->errorInfo());
        die;*/
    }

    //********************************************************************************************** */
    public static function select(string $table, string $db, array $filter = [], string $order = "", string $limit = "")
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();

        $class = explode('_', $table);
        $className = UtilsFunctions::className($class, $table);
        $requete = $connexion->prepare($database::select($table, $filter ,$order, $limit));
        $requete->execute();
        $result = $requete->fetchAll(PDO::FETCH_CLASS, $className);

        return $result;
    }

    //********************************************************************************************** */
    public static function customSelect(string $table, string $db, string $data, string $order = "", string $limit = "")
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();

        $class = explode('_', $table);
        $className = UtilsFunctions::className($class, $table);
        $requete = $connexion->prepare($database::customSelect($table, $data ,$order, $limit));
        $requete->execute();
        $result = $requete->fetchAll(PDO::FETCH_CLASS, $className);

        return $result;
    }

    //********************************************************************************************** */
    public static function selectOne(string $table, string $db, array $filter = [])
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();

        $class = explode('_', $table);
        $className = UtilsFunctions::className($class, $table);
        $requete = $connexion->prepare($database::select($table, $filter));
        $requete->execute();
        $result = $requete->fetchObject($className);

        return $result;
    }

    //********************************************************************************************** */
    public static function selectInner(string $table1, string $table2, string $table3, string $keyTable, string $db, array $data = [], string $order = "")
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();
        $class = explode('_', $table2);
        $className = UtilsFunctions::className($class, $table2);
        $requete = $connexion->prepare($database::selectInner($table1, $table2, $table3, $keyTable, $data, $order));
        $requete->execute();
        $result = $requete->fetchAll(PDO::FETCH_CLASS, $className);

        return $result;
    }

    //******************************************************************************************** */
    public static function selectMaxId(string $table, string $db)
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();
        $requete = $connexion->prepare($database::selectMaxId($table));
        $requete->execute();
        $result = $requete->fetchAll();

        return $result[0][0];
    }

    //************************************************************************************************** */
    public static function update(string $table, string $db, array $filter, array $data): void
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $requete = $connexion->prepare($database::update($table, $filter, $data));
        $requete->execute();
        /*print_r($requete->errorInfo());
         die;*/
    }

    //************************************************************************************************** */
    public static function updateOrInsert(string $table, string $db, array $filter, array $data): void
    {
        $row = self::selectOne($table, $db, $filter);
        if ($row) {
            self::update($table, $db, $filter, $data);
        } else {
            self::insert($table, $db, $data);
        }
    }

    //*********************************************************************************************** */
    public static function delete(string $table, string $db, array $filter): void
    {
        $database = new MYSQLPDO($db);
        $connexion = $database->getConnexion();
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $requete = $connexion->prepare($database::delete($table, $filter));
        foreach ($filter as $key => $value) {
            $requete->bindValue(':'.$key, $value);
        }
        $requete->execute();
    }

    //****************************************************************************************** */
}
