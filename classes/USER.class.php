<?php

class USER
{
    private static $isReferenceur = null;

    private static $isConnecteIG = null;

    private static $isConnecte = null;

    private static $ipAddress = null;

    //singleton
    private function __construct()
    {
    }

    //start
    public static function init()
    {
        session_name('myGenghis');
        session_start();

        $currentCookieParams = session_get_cookie_params();
        $sidvalue = session_id();
        setcookie(
            'PHPSESSID',//name
            $sidvalue,//value
            0,//expires at end of session
            $currentCookieParams['path'],//path
            $currentCookieParams['domain'],//domain
            true //secure
        );

        if (self::isBanni() or self::ipBanHack()) {
            $tplBan = new Template();
            $tplBan->setFile('Ban', '_Ban.html');
            $tplBan->bloc('BAN');

            switch (true) {
                case self::isBanni(): // Compte banni
                    list($true, $banDate, $duree, $reason, $author) = self::isBanni();
                    $tplBan->bloc('BAN.MESSAGE_IS_BANNI', [
                        'DATE' => $duree,
                        'MESSAGE' => $reason,
                    ]);

                    $tplBan->bloc('BAN.DESCRIPTION_IS_BANNI', [
                        'DATE' => $banDate,
                        'AUTEUR' => $author,
                    ]);

                break;

                case self::ipBanHack(): // adresse ip ban hack automatique
                    $tplBan->bloc('BAN.MESSAGE_IP_BAN_HACK');
                    list($true, $ip, $array) = self::ipBanHack();
                    foreach ($array as $keys => $values) {
                        $tplBan->bloc('BAN.DESCRIPTION_IP_BAN_HACK', [
                            'DESC' => $values[0],
                            'DATE' => date('d/m/Y à H:i', $values[1]),
                            'PAGE' => $values[2],
                        ]);
                    }

                break;
            }

            if (DATA::isSession('notification')) {
                unset($_SESSION['notification']);
            }

            exit(UTILS::compressHtml($tplBan->construire('Ban')));
        }
    }

    public static function ipBanHack()
    {
        $queryBanHack = MYSQL::query('SELECT * FROM ip_ban_hack WHERE ip=\''.UTILS::getIp().'\' AND tentatives > 2');
        if (MYSQL::numRows($queryBanHack) > 0) {
            $queryHack = MYSQL::query('SELECT description, date, page FROM tentatives_hack WHERE ip=\''.UTILS::getIp().'\' ORDER BY id desc LIMIT 5');
            $array = [];
            while ($result = mysqli_fetch_row($queryHack)) {
                $array[] = [$result[0], $result[1], $result[2]];
            }

            return [true, UTILS::getIp(), $array];
        }

        return false;
    }

    public static function isBanni()
    {
        if (USER::isConnecte()) {
            $query = MYSQL::Query('SELECT isBanni FROM accounts WHERE Pseudo =\''.USER::GetPseudo().'\' AND isBanni = 1');
            if (mysqli_num_rows($query) > 0) {
                $query = MYSQL::Query('SELECT * FROM bans WHERE Pseudo =\''.USER::GetPseudo().'\' AND ban_expire > NOW()');
                if (mysqli_num_rows($query) > 0) {
                    $result = mysqli_fetch_object($query);
                    if (1 == $result->ban_permanent) {
                        $duree = 'Vous êtes banni à vie !';
                    } else {
                        $duree = 'Vous êtes banni jusqu\'au '.$result->ban_expire->format('d/m/Y à H:i');
                    }

                    $banDate = $result->ban_date->format('d/m/Y à H:i');

                    return [true, $banDate, $duree, $result->reason, $result->author];
                } // sinon on deban le compte
                MYSQL::Query('UPDATE MEMB_INFO  SET bloc_code = 0 WHERE memb___id =\''.USER::GetPseudo().'\'');
                UTILS::notification('success', 'Votre compte est désormais débanni, la prochaine merci d\'en profiter pour lire les rêgles du jeu pour éviter un autre blocage du compte.', false, true);
                header('location: /Accueil');

                exit;
            }

            return false;
        }
    }

    public static function isConnecte(): bool
    {
        if (null == self::$isConnecte) {
            self::$isConnecte = (DATA::isSession('login') && DATA::isSession('ip') == UTILS::getIp());
        }

        return self::$isConnecte;
    }

    public static function Login($pseudo, $password): bool
    {
        $filter = ['Pseudo' => $pseudo, 'Password' => hash('sha256', $password)];
        $account = Generique::selectOne('accounts', 'graphene_bsm', $filter);
        if ($account) {
            $filter = ['Pseudo' => $pseudo, 'RestorePassword' => 1];
            $RestorePassword = Generique::selectOne('accounts', 'graphene_bsm', $filter);
            if ($RestorePassword) {
                $data = ['Pseudo' => $pseudo, 'RestorePassword' => 0];
                Generique::update('accounts', 'graphene_bsm', $filter, $data);
            }
            UTILS::addHistory($pseudo, 9, 'Connexion sur '.UTILS::getFunction('SiteName'), "/Account/Messages/ID-{$account->getIdClient()}");
            DATA::setSession('login', $pseudo);
            DATA::setSession('last_login', $pseudo);
            DATA::setSession('ip', UTILS::getIp());

            return true;
        }
        UTILS::addHistory($pseudo, 8, 'Connexion échouée au site '.UTILS::getFunction('SiteName'));
        UTILS::notification('danger', 'Identifiant ou mot de passe incorrect merci de réessayer', true, false);

        return false;
    }

    public static function getPseudo()
    {
        if (!self::isConnecte()) {
            trigger_error('Erreur de session (Tentative de récupération du pseudo d\'un invité).<br />', E_USER_ERROR);
        }

        return DATA::getSession('login');
    }

    public static function getId($pseudo = '')
    {
        if ($pseudo && '' != $pseudo) {
            return MYSQL::selectOneValue("SELECT id_client FROM accounts WHERE Pseudo = '{$pseudo}'");
        }
        if (!self::isConnecte()) {
            trigger_error('Erreur de session (Tentative de récupération du pseudo d\'un invité).<br />', E_USER_ERROR);
        }

        return MYSQL::selectOneValue('SELECT id_client FROM accounts WHERE Pseudo = \''.DATA::getSession('login').'\'');
    }

    // on calcule le nombre de jour date d'inscription
    public static function DateCompte($date_compte)
    {
        $query_date = MYSQL::QUERY('SELECT date FROM accounts WHERE Username=\''.USER::getPseudo().'\'');
        $row = MYSQL::fetchArray($query_date);
        $premiere_date = $row['date'];
        $deuxieme_date = date('Y-m-d H:i:s');

        $difference = abs(strtotime($deuxieme_date) - strtotime($premiere_date));

        $annee = floor($difference / (365 * 60 * 60 * 24));
        $mois = floor(($difference - $annee * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $jours = floor(($difference - $annee * 365 * 60 * 60 * 24 - $mois * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        return sprintf('%d annee, %d mois, %d jours', $annee, $mois, $jours);
        // � utiliser dans index '.USER::DateCompte().'
    }

    public static function isReferenceur()
    {
        if (!self::isConnecte()) {
            return false;
        }
        if (null === self::$isReferenceur) {
            $ref = MYSQL::selectOneValue('SELECT referenceur FROM memb_info WHERE memb___id=\''.self::getPseudo().'\'');
            self::$isReferenceur = (1 == $ref);
        }

        return self::$isReferenceur;
    }

    public static function isConnecteIG()
    {
        if (null === self::$isConnecteIG) {
            $result = mysqli_fetch_array(MYSQL::query('SELECT ConnectStat FROM MEMB_STAT WHERE memb___id=\''.self::getPseudo().'\''));
            self::$isConnecteIG = $result['ConnectStat'];
        }

        return self::$isConnecteIG;
    }

    public static function getIp(): string
    {
        if (null == self::$ipAddress) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && '' != $_SERVER['HTTP_X_FORWARDED_FOR']) {
                if (strchr($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                    $tab = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                    $proxy = trim($tab[count($tab) - 1]);
                    $realip = trim($tab[0]);
                } else {
                    $realip = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
                    $proxy = $_SERVER['REMOTE_ADDR'];
                }
                if (false === ip2long($realip)) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                }
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
                $proxy = '';
            }
            if ($realip == $proxy) {
                $proxy = '';
            }

            self::$ipAddress = '(IP : '.$realip.(('' != $proxy) ? ', PROXY : '.$proxy : '').')';
        }

        return self::$ipAddress;
    }
}
