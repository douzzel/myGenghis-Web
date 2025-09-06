<?php

class CACHE
{
    public static $dossierCache = 'cache/';
    
    public static function get($fichier)
    {
        return file_get_contents(CACHE::$dossierCache.$fichier);
    }
        
    public static function is($fichier, $duree = 3600)
    {
        $file = CACHE::$dossierCache.$fichier;
        return (file_exists($file) && (time() - filemtime($file)) < $duree);
    }

    public static function set($fichier, $contenu)
    {
        $f = fopen(CACHE::$dossierCache.$fichier, 'w+');
        fputs($f, $contenu);
        fclose($f);
    }
    
    public static function del($fichier)
    {
        @unlink(CACHE::$dossierCache.$fichier);
    }
    
    
    public static function getUser()
    {
        return USER::getPseudo();
    }
    
    public static function setCache($value, $cache=false, $isTrue=false)
    {
        if ($cache) {
            $url = str_replace('/', '', $_SERVER['REQUEST_URI']);
            $name = self::getUser(false, $isTrue);
            if (self::is($name.'-'.$url, 3600)) {
                $content = unserialize(self::get($name.'-'.$url));
                return $content;
            } else {
                self::set($name.'-'.$url, serialize($value));
                return $value;
            }
        } else {
            return $value;
        }
    }
    
    public static function getCache()
    {
        $url = str_replace('/', '', $_SERVER['REQUEST_URI']);
        $name = self::getUser();
        if (self::is($name.'-'.$url, 3600)) {
            return true;
        }
    }
}
