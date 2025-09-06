<?php

class DATA
{
    private static $contenuGet = null;
    private static $contenuPost = null;
    private static $contenuSession = null;
    private static $contenuCookie = null;

    /* singleton */
    private function __construct()
    {
    }

    // pour lire tout et vérifier les tentatives de hack sans utiliser le reste
    // de la classe (uniquement pour les anciennes parties du site !!!)
    public static function lireAll(): void
    {
        self::lireGet();
        self::lirePost();
        self::lireCookie();
    }

    private static function lireGet(): void
    {
        if (self::$contenuGet === null) {
            self::$contenuGet = $_GET;
            if (self::isHackAttemptInData(self::$contenuGet)) {
                UTILS::addHack('Possible attaque dans les données GET', true);
            }
        }
    }
    private static function lirePost(): void
    {
        if (self::$contenuPost === null) {
            self::$contenuPost = $_POST;
            if (!preg_match("#(/?)Forum(./)#i", $_SERVER['REQUEST_URI']) && self::isHackAttemptInData(self::$contenuPost, false)) {
                UTILS::addHack('Possible attaque dans les données POST');
            }
        }
    }

    private static function lireSession(): void
    {
        if (isset($_SESSION)) {
            if (self::$contenuSession === null) {
                self::$contenuSession = $_SESSION;
            }
        }
    }
    private static function lireCookie(): void
    {
        if (self::$contenuCookie === null) {
            self::$contenuCookie = $_COOKIE;
            if (self::isHackAttemptInData(self::$contenuCookie)) {
                UTILS::addHack('Possible attaque dans les cookies', true);
            }
        }
    }

    /**
     * isGet
     *
     * @param  string|array $cle
     * @return bool
     */
    public static function isGet($cle): bool
    {
        self::lireGet();
        if (is_array($cle)) {
            foreach ($cle as $c) {
                if (!(isset(self::$contenuGet[$c]) && !empty(self::$contenuGet[$c])))
                    return false;
            }
            return true;
        } else {
            return (isset(self::$contenuGet[$cle]) && !empty(self::$contenuGet[$cle]));
        }
    }

    /**
     * isPost
     *
     * @param  string|array $cle
     * @return bool
     */
    public static function isPost($cle): bool
    {
        self::lirePost();
        if (is_array($cle)) {
            foreach ($cle as $c) {
                if (!(isset(self::$contenuPost[$c]) && !empty(self::$contenuPost[$c])))
                    return false;
            }
            return true;
        } else {
            return (isset(self::$contenuPost[$cle]) && !empty(self::$contenuPost[$cle]));
        }
    }

    public static function isSession(string $cle): bool
    {
        self::lireSession();
        return (isset(self::$contenuSession[$cle]) && !empty(self::$contenuSession[$cle]));
    }

    public static function issetGet(string $cle): bool
    {
        self::lireGet();
        return (isset(self::$contenuGet[$cle]));
    }

    public static function issetPost(string $cle): bool
    {
        self::lirePost();
        return (isset(self::$contenuPost[$cle]));
    }

    public static function issetSession(string $cle): bool
    {
        self::lireSession();
        return (isset(self::$contenuSession[$cle]));
    }

    public static function isCookie(string $cle): bool
    {
        self::lireCookie();
        return (isset(self::$contenuCookie[$cle]) && !empty(self::$contenuCookie[$cle]));
    }

    /**
     * getGet
     * return GET param
     *
     * @param  string $cle
     * @param  bool $traiterHtml
     * @return string|string[]
     */
    public static function getGet(string $cle, bool $traiterHtml = true)
    {
        self::lireGet();
        if (!isset(self::$contenuGet[$cle])) {
            return '';
        }
        return self::filtrer(self::$contenuGet[$cle], $traiterHtml);
    }

    /**
     * return POST param
     *
     * @param  string $cle
     * @param  bool $traiterHtml
     * @return string|string[]
     */
    public static function getPost(string $cle, bool $traiterHtml = true)
    {
        self::lirePost();
        if (!isset(self::$contenuPost[$cle])) {
            return '';
        }
        return self::filtrer(self::$contenuPost[$cle], $traiterHtml);
    }

    public static function getSession(string $cle): string
    {
        self::lireSession();
        if (!isset(self::$contenuSession[$cle])) {
            return '';
        }
        return self::$contenuSession[$cle];
    }

    public static function getCookie(string $cle, bool $traiterHtml = true): string
    {
        self::lireCookie();
        if (!isset(self::$contenuCookie[$cle])) {
            return '';
        }
        return self::filtrer(self::$contenuCookie[$cle], $traiterHtml);
    }

    public static function setGet(string $cle, string $valeur): void
    {
        self::$contenuGet[$cle] = $valeur;
    }

    public static function setPost(string $cle, string $valeur): void
    {
        self::$contenuPost[$cle] = $valeur;
    }

    public static function setSession(string $cle, string $valeur): void
    {
        self::$contenuSession[$cle] = $valeur;
        $_SESSION[$cle] = $valeur;
    }

    public static function setCookie(string $cle, string $valeur, int $expire = -1): void
    {
        self::$contenuCookie[$cle] = $valeur;
        if ($expire == -1) {
            $expire = time() + 31536000;
        }
        setcookie($cle, $valeur, $expire, '/', '');
    }

    /**
     * filtrer
     *
     * @param  string|string[] $txt
     * @param  bool $traiterHtml
     * @return string|string[]
     */
    private static function filtrer($txt, bool $traiterHtml)
    {
        if (is_array($txt)) {
            foreach ($txt as $t) {
                $t = $traiterHtml ? htmlentities(str_replace("'", "’", $t)) : str_replace("'", "’", $t);
            }
            return $txt;
        } else {
            return $traiterHtml ? htmlentities(str_replace("'", "’", $txt)) : str_replace("'", "’", $txt);
        }
    }

    public static function getSerializedPost(): string
    {
        return serialize(self::$contenuPost);
    }

    public static function getSerializedGet(): string
    {
        return serialize(self::$contenuGet);
    }

    public static function getSerializedCookie(): string
    {
        return serialize(self::$contenuCookie);
    }

    public static function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private static function isHackAttemptInData($data, bool $checkHtml = true): bool
    {
        if ($checkHtml) {
            foreach ($data as $a => $b) {
                if ((preg_match("#<([^>]+)>#", $b)) || preg_match("#(INSERT |UPDATE |DELETE |SELECT |ALTER |DROP |GRANT |CREATE | OR | AND |INCLUDE|\\0)#i", $b)) {
                    return true;
                }
            }
        }
        return false;
    }
}
