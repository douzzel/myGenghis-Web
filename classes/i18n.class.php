<?php

$config_file = file_exists(__DIR__."/../config.php") ? "config.php" : "config.sample.php";
require_once(__DIR__."/../{$config_file}");

class i18n {
    private static $isEnabled = false;

    // ----- base query function ----- //
    private static function query($sql) {
    new self();
    $base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        $base->set_charset("utf8mb4");
        $query = mysqli_query($base, $sql) or die (mysqli_error($base));
        if($query === false)
            self::error('Query Error: <br />'.$sql, mysqli_error($base));
        return $query;
    }

    // ----- error function ----- //
    private static function error($txt, $erreur) {
        trigger_error('MySQL Error: '.$txt.'<br />Message : '.$erreur.'<br />', E_USER_ERROR);
    }

    // ----- get l10n ----- //
    public static function getl10n($language, $source) {
        $l10n = "";
        $sql = "SELECT target FROM _i18n WHERE language = '" . $language . "' AND source = '" . $source . "'";
        $query = self::query($sql);
        if(mysqli_num_rows($query) < 1) {
            $sql = "SELECT target FROM _i18n WHERE language = 'fr-FR' AND source = '" . $source . "'";
            $query = self::query($sql);
            $l10n = $query;
        } else { $l10n = $query; }
        return $l10n;
    }

    // ----- set l10n ----- //
    public static function setl10n($language, $source, $target, $creator) {
        $sql = 'INSERT INTO _i18n (language, source, target, created_by, updated_by) VALUES (\''.$language.'\', \''.$source.'\', \''.$taget.'\', \''.$creator.'\', \''.$creator.'\')';
        $query = self::query($sql);
    }

    // ----- mod l10n ----- //
    public static function modl10n($id, $language, $source, $target, $modder) {
        $sql = 'UPDATE `_i18n` SET `language`=\''.$language.'\', `source`=\''.$source.'\', `target`=\''.$target.'\', `updated_by`=\''.$moder.'\' WHERE id = '.$id;
        $query = self::query($sql);
    }

}
?>