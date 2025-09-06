<?php

class UTILS
{
    private static $ipAddress = null;

    private static $phpmailer_deja_inclus = false;

    /*
     * Pour récupérer la vraie adresse IP, męme derričre un proxy
     * @return
     */
    public static function getIp()
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

    public static function getModule($NameModule, $tplBloc, $NameBloc)
    {
        $reqModule = MYSQL::query('SELECT Titre, NamePage, Url, UrlImage, Description, Texte, Time, Html, NameModule, ModuleActive FROM modulespages WHERE NameModule = \''.$NameModule.'\'');
        if (mysqli_num_rows($reqModule) > 0) {
            $resultModule = mysqli_fetch_object($reqModule);
            if ('ON' === $resultModule->ModuleActive) {
                $tplBloc->bloc($NameBloc, [
                    'TITRE' => $resultModule->Titre,
                    'DESCRIPTION' => $resultModule->Description,
                    'TEXTE' => $resultModule->Texte,
                    'PAGE_NAME' => $resultModule->NamePage,
                    'HTML' => $resultModule->Html,
                    'URL' => $resultModule->Url,
                    'URL_IMG' => $resultModule->UrlImage,
                    'TIME' => $resultModule->Time,
                ]);

                return true;
            }

            return false;
        }
    }

    public static function isModuleActive($nameModule)
    {
        $moduleActive = MYSQL::selectOneValue("SELECT ModuleActive FROM modulespages WHERE NameModule = '{$nameModule}'");

        return 'ON' == $moduleActive;
    }

    public static function getTicketStatus($ouvert, $status)
    {
        if (1 == $ouvert) {
            return '<font color="#2776dc">Ouvert</font>';
        }
        if (2 == $ouvert) {
            return '<font color="green">Résolu</font>';
        }

        return '<font color="red">Fermé</font>';
    }

    public static function GetAvatar($avatar)
    {
        return self::getOnlyAvatar($avatar) ?? '/themes/assets/images/avatars/no-avatar.png';
    }

    public static function GetIdAvatar($id_client)
    {
        $avatar = MYSQL::selectOneValue("SELECT Pseudo FROM accounts WHERE id_client = '{$id_client}'");
        if ($avatar) {
            return self::getOnlyAvatar($avatar) ?? '/themes/assets/images/avatars/no-avatar.png';
        }
        return '/themes/assets/images/avatars/no-avatar.png';
    }

    public static function getOnlyAvatar($avatar) {
        $jpg = '/themes/assets/images/avatars/'.$avatar.'.jpg';
        $gif = '/themes/assets/images/avatars/'.$avatar.'.gif';
        $jpeg = '/themes/assets/images/avatars/'.$avatar.'.jpeg';
        $png = '/themes/assets/images/avatars/'.$avatar.'.png';
        if (file_exists('.'.$jpg))
            return $jpg.'?'.time();
        elseif (file_exists('.'.$gif))
            return $gif.'?'.time();
        elseif (file_exists('.'.$jpeg))
            return $jpeg.'?'.time();
        elseif (file_exists('.'.$png))
            return $png.'?'.time();
        return;
    }

    public static function GetBgProfil($bgProfil)
    {
        $jpg = '/themes/assets/images/bg_profil/'.$bgProfil.'.jpg';
        $gif = '/themes/assets/images/bg_profil/'.$bgProfil.'.gif';
        $jpeg = '/themes/assets/images/bg_profil/'.$bgProfil.'.jpeg';
        $png = '/themes/assets/images/bg_profil/'.$bgProfil.'.png';
        if (file_exists('.'.$jpg)) {
            $check_bgProfil = $jpg;
        } elseif (file_exists('.'.$gif)) {
            $check_bgProfil = $gif;
        } elseif (file_exists('.'.$jpeg)) {
            $check_bgProfil = $jpeg;
        } elseif (file_exists('.'.$png)) {
            $check_bgProfil = $png;
        } else {
            $check_bgProfil = '/themes/assets/images/bg_profil/no-bg-profil.jpeg';
        }

        return $check_bgProfil.'?'.time();
    }

    public static function getAdmin($pseudo)
    {
        $req = MYSQL::query('SELECT * FROM admin WHERE Name=\''.$pseudo.'\' AND Expiry > NOW()');
        if (mysqli_num_rows($req) > 0) {
            return true;
        }
    }

    public static function mssql_real_escape_string($string)
    {
        $chars = ['NULL', '\x00', '\n', '\r', '\\', "'", '"', '\x1a'];
        $escapes = ['\NULL', '\\x00', '\\n', '\\r', '\\\\', "''", '\"', '\\x1a'];

        return str_replace($chars, $escapes, $string);
    }

    public static function regexUrl($string)
    {
        //The Regular Expression filter
        $reg_exUrl = "/(?i)\\b((?:https?:\\/\\/|www\\d{0,3}[.]|[a-z0-9.\\-]+[.][a-z]{2,4}\\/)(?:[^\\s()<>]+|\\(([^\\s()<>]+|(\\([^\\s()<>]+\\)))*\\))+(?:\\(([^\\s()<>]+|(\\([^\\s()<>]+\\)))*\\)|[^\\s`!()\\[\\]{};:'\".,<>?«»“”‘’]))/";

        // Check if there is a url in the text
        if (preg_match_all($reg_exUrl, $string, $url)) {
            // Loop through all matches
            foreach ($url[0] as $newLinks) {
                if (false === strstr($newLinks, ':')) {
                    $link = 'http://'.$newLinks;
                } else {
                    $link = $newLinks;
                }

                // Create Search and Replace strings
                $search = $newLinks;
                $replace = '<a href="'.$link.'" title="'.$newLinks.'" target="_blank">'.$link.'</a>';
                $string = str_replace($search, $replace, $string);
            }
        }

        //Return result
        return $string;
    }

    public static function notification($type, $message, $refresh = true, $closeBtn = true)
    {
        if ($closeBtn) {
            $close = '<button type="button" class="close notif-close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
					</button>';
        } else {
            $close = false;
        }

        $alert = [
            'primary' => '<div class="alert alert-primary w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
            'secondary' => '<div class="alert alert-secondary w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
            'success' => '<div class="alert alert-success w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
            'danger' => '<div class="alert alert-danger w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
            'warning' => '<div class="alert alert-warning w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
            'info' => '<div class="alert alert-info w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
            'light' => '<div class="alert alert-light w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
            'dark' => '<div class="alert alert-dark w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
        ];

        DATA::setSession('notification', '<div class="notification">'.$alert[$type].'</div>');
        if ($refresh) {
            header('location: '.$_SERVER['HTTP_REFERER']);

            exit();
        }
    }

    public static function Alert($type, $titre, $message, $urlPost, $name, $value)
    {
        $alert = '<form class="notification" method="POST" action="'.$urlPost.'">';
        $alert .= '<div class="alert alert-'.$type.'" role="alert">';
        $alert .= '<h4 class="alert-heading text-left">'.$titre.'</h4>';
        $alert .= '<p>'.$message.'</p><hr>';
        $alert .= '<p class="mb-0">';
        $alert .= '<a href="'.$_SERVER['HTTP_REFERER'].'" class="btn btn-success">Annuler</a>';
        $alert .= '<input type="hidden" name="'.$name.'" value="'.$value.'"/>';
        $alert .= '<button type="submit" class="btn btn-danger ml-1">Confirmer</button>';
        $alert .= '</p></div>';
        $alert .= '</form>';
        DATA::setSession('notification', $alert);
    }

    public static function Select($type, $titre, $message, $urlPost, $name, $array)
    {
        $select = '<form class="notification" method="POST" action="'.$urlPost.'">';
        $select .= '<div class="alert alert-'.$type.'" role="alert">';
        $select .= '<h4 class="alert-heading text-left">'.$titre.'</h4>';
        $select .= '<p>'.$message.'</p><hr>';
        $select .= "<select name='{$name}' class='form-control'>";
        foreach ($array as $val) {
            $select .= "<option value='{$val['id']}'>{$val['name']}</option>";
        }
        $select .= '</select>';
        $select .= '<p class="mb-0 pt-3">';
        $select .= '<a href="'.$_SERVER['HTTP_REFERER'].'" class="btn btn-link">Annuler</a>';
        $select .= '<button type="submit" class="btn btn-theme ml-3">Confirmer</button>';
        $select .= '</p></div>';
        $select .= '</form>';
        DATA::setSession('notification', $select);
    }

    public static function initOutputFilter()
    {
        ob_start('ob_gzhandler');
        register_shutdown_function('ob_end_flush');
    }

    public static function random($car)
    {
        $string = '';
        $chaine = 'AZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
        srand((float) microtime() * 1000000);

        for ($i = 0; $i < $car; ++$i) {
            $string .= $chaine[rand() % strlen($chaine)];
        }

        return $string;
    }

    public static function getHistoryType($code)
    {
        $x = [
            0 => ['badge-danger', 'Error'],
            1 => ['badge-warning', 'Admin'],
            2 => ['badge-warning', 'Admin'],
            3 => ['badge-primary bg-gouv', 'Inscription'],
            4 => ['badge-success', 'Update'],
            5 => ['badge-warning', 'Recover'],
            6 => ['badge-primary bg-com', 'Contact'],
            7 => ['badge-success', 'Update'],
            8 => ['badge-danger', 'Error'],
            9 => ['badge-success', 'Connect'],
            10 => ['badge-success', 'Disconnect'],
            11 => ['badge-success', 'Buy'],
            12 => ['badge-success', 'Buy'],
            13 => ['badge-warning', 'Leave'],
            14 => ['badge-primary', 'Prospect'],
            15 => ['badge-primary', 'Subject'],
            16 => ['badge-primary', 'Reply'],
            17 => ['badge-danger', 'Hack'],
            18 => ['badge-danger', 'SQL'],
            19 => ['badge-primary bg-gouv', 'Membre'],
            20 => ['badge-success bg-perf', 'Facture'],
            21 => ['badge-warning bg-perf', 'Devis'],
            22 => ['badge-warning bg-gouv', 'Personnel'],
            23 => ['badge-warning bg-com', 'Site'],
            24 => ['badge-warning bg-perf', 'Documents'],
            25 => ['badge-warning bg-com', 'Articles'],
            26 => ['badge-warning bg-com', 'Messages'],
            27 => ['badge-warning bg-com', 'Vidéos'],
            28 => ['badge-primary', 'Analytics'],
            29 => ['badge-primary', 'Calendrier'],
            30 => ['badge-primary', 'Tâches'],
            31 => ['badge-primary', 'Discussions'],
            32 => ['badge-primary', 'Drive'],
            33 => ['badge-primary', 'Paramètres Drive'],
            34 => ['badge-primary', 'Utilisateurs Drive'],
            35 => ['badge-primary', 'Polycompetances'],
            36 => ['badge-primary', 'Liste des Argumentaires'],
            37 => ['badge-primary', 'Argumentaire'],
            38 => ['badge-primary', 'Centre de Contacts'],
            39 => ['badge-primary', 'Paramètres du Centre de Contacts'],
            40 => ['badge-primary', 'Templates du Centre de Contacts'],
            41 => ['badge-warning bg-perf', 'Dossiers'],
            42 => ['badge-primary', 'Campagne'],
            43 => ['badge-primary', 'Fiche de Campagne'],
            44 => ['badge-primary', 'Persona'],
            45 => ['badge-primary', 'Fiche de Persona'],
            46 => ['badge-primary', 'Parcours'],
            47 => ['badge-primary', 'Liste des Parcours'],
            48 => ['badge-primary', 'Presse'],
            49 => ['badge-primary', 'Salon'],
            50 => ['badge-primary', 'Template de Messagerie'],
            51 => ['badge-primary', 'Gestion des Droits'],
            52 => ['badge-info', 'Réservation'],
            53 => ['badge-info', 'Click And Collect'],
            54 => ['badge-warning bg-perf', 'Liens'],
            55 => ['badge-warning bg-com', 'Mail'],
            56 => ['badge-warning bg-com', 'Forum']
        ];

        if (isset($x[$code]))
            return [$x[$code][0], $x[$code][1]];
        return ['badge-primary', ''];
    }

    public static function addHistory($pseudo = null, $type = 0, $message = "", $link = null)
    {
        $message = htmlentities($message, ENT_QUOTES, 'UTF-8');
        $data = ['idType_historique' => $type, 'memb___id' => $pseudo, 'ip' => $_SERVER['REMOTE_ADDR'], 'isAction' => $message, 'link' => $link];

        return Generique::insert('historique', 'graphene_bsm', $data);
    }

    public static function myUrlEncode($string)
    {
        $entities = ['%21',        '%2A',          '%27',        '%28',          '%29',          '%3B',           '%3A',          '%40',          '%26',         '%3D',        '%2B',         '%24',         '%2C',         '%2F',        '%3F',        '%25',        '%23',       '%5B',       '%5D'];
        $replacements = ['!',          '*',            "'",          '(',            ')',            ';',             ':',            '@',            '&',           '=',          '+',            '$',           ',',           '/',          '?',          '%',         '#',          '[',         ']'];

        return str_replace($entities, $replacements, urlencode($string));
    }

    // Pour encoder le nom d'une page pour avoir une url propre
    public static function encodeNomPage($nom)
    {
        $newNom = strtolower(strtr(
            $nom,
            'ŔÁÂĂÄĹŕáâăäĺŇÓÔŐÖŘňóôőöřČÉĘËčéęëÇçĚÍÎĎěíîďŮÚŰÜůúűü˙Ńń',
            'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn'
        ));

        return preg_replace('`([^_a-z0-9])`i', '_', $newNom);
    }

    public function suppr_accents($str, $encoding = 'utf-8')
    {
        // transformer les caractères accentués en entités HTML
        $str = htmlentities($str, ENT_NOQUOTES, $encoding);

        // remplacer les entités HTML pour avoir juste le premier caractères non accentués
        // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
        $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);

        // Remplacer les ligatures tel que : Œ, Æ ...
        // Exemple "Å“" => "oe"
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        // Supprimer tout le reste
        return preg_replace('#&[^;]+;#', '', $str);
    }

    // Pour optimiser le code html avant envoi
    public static function compressHtml($buffer)
    {
        $poz_current = 0;
        $poz_end = strlen($buffer) - 1;
        $result = '';

        function compressbuffer_html($buffer)
        {
            $buffer = preg_replace('<!\-\- [\/\ a-zA-Z]* \-\->', '', $buffer);
            $buffer = preg_replace('#([\\s]+)#', ' ', $buffer);
            $buffer = preg_replace('#<!--.*?-->#s', '', $buffer);

            return str_replace(['<!--', '-->'], ["\n<!--\n", "\n-->\n"], $buffer);
        }

        while ($poz_current < $poz_end) {
            $t_poz_start = strpos($buffer, '<textarea', $poz_current);
            if (false === $t_poz_start) {
                $buffer_part_2strip = substr($buffer, $poz_current);
                $temp = compressbuffer_html($buffer_part_2strip);
                $result .= $temp;
                $poz_current = $poz_end;
            } else {
                $buffer_part_2strip = substr($buffer, $poz_current, $t_poz_start - $poz_current);
                $temp = compressbuffer_html($buffer_part_2strip);
                $result .= $temp;
                $t_poz_end = strpos($buffer, '</textarea>', $t_poz_start);
                $temp = substr($buffer, $t_poz_start, $t_poz_end - $t_poz_start);
                $result .= $temp;
                $poz_current = $t_poz_end;
            }
        }

        return $result;
    }

    public static function addHack($description, $doBan = false)
    {
        $post = str_replace("'", "''", DATA::getSerializedPost());
        $get = str_replace("'", "''", DATA::getSerializedGet());
        $cookies = str_replace("'", "''", DATA::getSerializedCookie());
        $desc = str_replace("'", "''", $description);

        $page = $_SERVER['REQUEST_URI'];

        // MYSQL::query(preg_replace("#\\0#", "\\\\0", 'INSERT INTO tentatives_hack
        // 	(ip,
        // 	date,
        // 	page,
        // 	post,
        // 	getGet,
        // 	cookies,
        // 	description,
        // 	pseudo)
        // 	VALUES
        // 	(\''.self::getIp().'\',
        // 	\''.time().'\',
        // 	\''.$page.'\',
        // 	\''.$post.'\',
        // 	\''.$get.'\',
        // 	\''.$cookies.'\',
        // 	\''.$desc.'\',
        // 	\''.((USER::isConnecte()) ? USER::getPseudo() : '').'\')'));

        if ($doBan) {
            $query = MYSQL::query('SELECT ip FROM ip_ban_hack WHERE ip=\''.self::getIp().'\'');
            if (MYSQL::fetchArray($query) > 0) {
                MYSQL::query('UPDATE ip_ban_hack SET tentatives=tentatives+1, derniere=\''.time().'\' WHERE ip=\''.self::getIp().'\'');
            } else {
                MYSQL::query('INSERT INTO ip_ban_hack (ip, tentatives, derniere) VALUES (\''.self::getIp().'\', 1, \''.time().'\');');
            }
        }
    }

    public static function Encode($text)
    {
        return $text;
        // $text = htmlentities($text, ENT_NOQUOTES, 'UTF-8');

        // return htmlspecialchars_decode($text);
    }

    public static function FormatNumber($n)
    {
        $n = (0 + str_replace(',', '', $n));
        if (!is_numeric($n)) {
            return false;
        }
        if ($n > 1000000000000) {
            return round(($n / 1000000000000), 1).' T';
        }
        if ($n > 1000000000) {
            return round(($n / 1000000000), 1).' B';
        }
        if ($n > 1000000) {
            return round(($n / 1000000), 1).' M';
        }
        if ($n > 1000) {
            return round(($n / 1000), 1).' K';
        }

        return number_format($n);
    }

    private static function saveMail($upAttachments, $mail, $body, $to, $bcc, $isDraft) {
        $mid = Generique::selectMaxId('mail_sent', 'graphene_bsm') + 1;
        if(isset($upAttachments) && $upAttachments != null){
            $linkAttachments = serialize($upAttachments);
        }else{
            $linkAttachments = null;
        }
        $data = [
            'subject' => utf8_encode(addslashes($mail->Subject)),
            'textHtml' => utf8_encode(addslashes($body)),
            'textPlain' => utf8_encode(addslashes(strip_tags($body))),
            'from_email' => UTILS::getFunction('usernameSMTP'),
            'to_email' => serialize($to),
            'bcc_email' => serialize($bcc),
            'attachments' => $linkAttachments,
            'MID' => $mid,
            'isDraft' => $isDraft ? 1 : 0,
            'isRead' => true,
        ];
        Generique::insert('mail_sent', 'graphene_bsm', $data);
    }

    public static function MAIL($to, $subject, $body, $attachments = null, $d = null, $save = false, $isDraft = false, $bcc = false, $replyToAddress = false, $replyToName = false)
    {
        global $phpmailer_deja_inclus;
        if (!$phpmailer_deja_inclus) {
            require 'smtp/PHPMailerAutoload.php';
            $phpmailer_deja_inclus = true;
        }

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->SMTPDebug = 0;                               // Enable verbose debug output
        $mail->isSMTP(true);

        $mail->Host = UTILS::getFunction('urlSMTP');
        $mail->Port = UTILS::getFunction('portSMTP');                         // Set mailer to use SMTP

        $webMail = UTILS::getFunction('WebmasterEmail');
        $mail->AddCustomHeader("List-Unsubscribe:  <mailto:{$webMail}?subject=unsubscribe>");

        $mail->SMTPSecure = UTILS::getFunction('sslSMTP'); // Gmail REQUIERT Le transfert securise
        $mail->SMTPAuth = true;  // Authentification SMTP active

        $mail->Username = UTILS::getFunction('usernameSMTP');
        $mail->Password = UTILS::getFunction('passwordSMTP');
        $mail->SetFrom(UTILS::getFunction('usernameSMTP'), UTILS::getFunction('StaticUrl'));
        if ($replyToAddress && $replyToName) {
            $mail->addReplyTo($replyToAddress, $replyToName); // L'adresse de réponse
        } else {
            $mail->addReplyTo(UTILS::getFunction('usernameSMTP'), UTILS::getFunction('StaticUrl')); // L'adresse de réponse
        }
        $mail->Subject = html_entity_decode($subject);

        $mail->Body = self::Encode("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'> <html xmlns='https://www.w3.org/1999/xhtml'> <head> <title>{$subject}</title> <meta http–equiv='Content-Type' content='text/html; charset=UTF-8' /> <meta http–equiv='X-UA-Compatible' content='IE=edge' /> <meta name='viewport' content='width=device-width, initial-scale=1.0' /> </head> <body> {$body} </body> </html>");

        if (is_array($to)) {
            foreach ($to as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($to);
            $to = [$to => null];
        }

        if (is_array($bcc)) {
            foreach ($bcc as $email) {
                $mail->AddBCC($email);
            }
        } else if ($bcc) {
            $mail->AddBCC($bcc);
            $bcc = [$bcc => null];
        }

        $upAttachments = [];
        if (isset($attachments) && $attachments) {
            if (is_array($attachments)) {
                foreach ($attachments as $path => $result) {
                    foreach ($result as $url => $name) {
                        $mail->addStringAttachment(file_get_contents(UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').'/'.$url), $name);
                        $upAttachments[] = $name;
                    }
                }
            } else {
                $mail->AddStringAttachment("$attachments", "invite.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
            }
        }

        $mail->isHTML(true);
        if (!$isDraft) {
            if (!$mail->send()) {
                if (ManageRights::verifyRights('Paramètres Graphene', 'Write', false, false)) {
                    UTILS::notification('danger', 'Le message n\'a pas pu être envoyé, assurez-vous que le serveur SMTP soit correctement configuré ', false, true);
                    header('location: /Administration/MessagingSMTP');
                    if ($save) {
                        self::saveMail($upAttachments, $mail, $body, $to, $bcc, true);
                    }
                } else {
                    UTILS::notification('danger', 'Le message n\'a pas pu être envoyé, serveur d\'envoie d\'email est en maintenance ', false, true);
                    header('location: '.$_SERVER['REQUEST_URI']);
                }

                return false;
            }
        }
        if ($save) {
            self::saveMail($upAttachments, $mail, $body, $to, $bcc, $isDraft);
        }
        return true;
    }

    public static function tplMail($email, $pseudo, $titre, $content, $tpl)
    {
        include 'tplEmail/tplMail.class.php';
        $template = new TPL();
        $contenu = $template->isMail($email, $pseudo, $titre, $content, $tpl);

        return $contenu;
    }

    public static function getServerMemoryUsage($getPercentage = true)
    {
        $memoryTotal = null;
        $memoryFree = null;

        if (stristr(PHP_OS, 'win')) {
            // Get total physical memory (this is in bytes)
            $cmd = 'wmic ComputerSystem get TotalPhysicalMemory';
            @exec($cmd, $outputTotalPhysicalMemory);

            // Get free physical memory (this is in kibibytes!)
            $cmd = 'wmic OS get FreePhysicalMemory';
            @exec($cmd, $outputFreePhysicalMemory);

            if ($outputTotalPhysicalMemory && $outputFreePhysicalMemory) {
                // Find total value
                foreach ($outputTotalPhysicalMemory as $line) {
                    if ($line && preg_match('/^[0-9]+$/', $line)) {
                        $memoryTotal = $line;

                        break;
                    }
                }

                // Find free value
                foreach ($outputFreePhysicalMemory as $line) {
                    if ($line && preg_match('/^[0-9]+$/', $line)) {
                        $memoryFree = $line;
                        $memoryFree *= 1024;  // convert from kibibytes to bytes

                        break;
                    }
                }
            }
        } else {
            if (is_readable('/proc/meminfo')) {
                $stats = @file_get_contents('/proc/meminfo');

                if (false !== $stats) {
                    // Separate lines
                    $stats = str_replace(["\r\n", "\n\r", "\r"], "\n", $stats);
                    $stats = explode("\n", $stats);

                    // Separate values and find correct lines for total and free mem
                    foreach ($stats as $statLine) {
                        $statLineData = explode(':', trim($statLine));

                        //
                        // Extract size (TODO: It seems that (at least) the two values for total and free memory have the unit "kB" always. Is this correct?
                        //

                        // Total memory
                        if (2 == count($statLineData) && 'MemTotal' == trim($statLineData[0])) {
                            $memoryTotal = trim($statLineData[1]);
                            $memoryTotal = explode(' ', $memoryTotal);
                            $memoryTotal = $memoryTotal[0];
                            $memoryTotal *= 1024;  // convert from kibibytes to bytes
                        }

                        // Free memory
                        if (2 == count($statLineData) && 'MemAvailable' == trim($statLineData[0])) {
                            $memoryFree = trim($statLineData[1]);
                            $memoryFree = explode(' ', $memoryFree);
                            $memoryFree = $memoryFree[0];
                            $memoryFree *= 1024;  // convert from kibibytes to bytes
                        }
                    }
                }
            }
        }

        if (is_null($memoryTotal) || is_null($memoryFree)) {
            return null;
        }
        if ($getPercentage) {
            return 100 - ($memoryFree * 100 / $memoryTotal);
        }

        return [
            'total' => $memoryTotal,
            'free' => $memoryFree,
        ];
    }

    public static function removeFromStock($factureId)
    {
        $req = MYSQL::query("SELECT * FROM facture WHERE FID = '{$factureId}'");
        while ($r = mysqli_fetch_object($req)) {
            $stock = MYSQL::selectOneValue("SELECT stock FROM products WHERE `name` = '{$r->article}'");
            if ($stock) {
                $stock -= $r->quantities;
                MYSQL::query("UPDATE products SET stock = {$stock} WHERE `name` = '{$r->article}'");
            }
        }
    }

    public static function randomTitle()
    {
        $title = ['Ex mollit Lorem magna velit aliqua duis pariatur tempor.', 'Aliquip cupidatat commodo magna nostrud aliquip reprehenderit nisi minim amet.', 'Exercitation nisi ea veniam minim veniam sint non consequat.', 'Laboris consectetur dolore sit sit.', 'Proident sit aliquip nostrud fugiat nisi minim nostrud irure deserunt mollit occaecat.', 'Velit mollit quis cillum labore est occaecat reprehenderit ea adipisicing non nostrud culpa consequat.', 'Qui ad commodo eu culpa commodo officia qui exercitation nisi in do deserunt anim ullamco.', 'Eu non labore laborum cupidatat pariatur pariatur nulla deserunt mollit.'];

        return $title[array_rand($title)];
    }

    public static function randomText()
    {
        $text = ['Laborum irure culpa ullamco aliquip. Tempor non quis laborum sunt aute ex in proident velit aliquip. Reprehenderit Lorem ut ex adipisicing pariatur eu velit.', 'Et proident do enim ullamco tempor aliqua cillum culpa ut est. Consequat in proident duis laborum. Ex ullamco ullamco exercitation mollit aliquip enim. Cupidatat minim duis labore reprehenderit ex consectetur nostrud veniam anim incididunt fugiat minim incididunt. Aliquip cupidatat sint qui quis excepteur incididunt qui. Irure eu reprehenderit duis occaecat velit.', 'Enim duis do Lorem in id consequat duis enim ex. Voluptate non minim adipisicing anim amet fugiat deserunt sint ipsum in eiusmod. Nostrud veniam non irure nostrud Lorem commodo ullamco eu. Sunt excepteur aute exercitation laboris laborum nisi aute ea occaecat excepteur ex velit sint qui.', 'Et dolore velit magna dolore duis fugiat laborum dolore eu. Do dolore duis eiusmod eu deserunt proident laboris ea esse fugiat nisi non amet veniam. Lorem do voluptate consectetur ut cupidatat commodo occaecat elit. Amet et dolor do tempor enim dolore in sit duis ipsum elit velit irure. Veniam consequat tempor est nulla. Deserunt quis sunt nulla sint sint eiusmod adipisicing nulla ad. Dolor excepteur in incididunt minim laborum cillum cupidatat aliqua cillum.', 'Consequat nisi quis nisi ex magna sunt dolore. Lorem eu quis anim minim nostrud dolor Lorem veniam duis magna qui cupidatat amet labore. Ad sint culpa nisi commodo anim. In qui aute consectetur magna Lorem Lorem mollit cupidatat aliqua et nisi commodo. Voluptate enim eiusmod elit elit in incididunt ea reprehenderit amet reprehenderit pariatur incididunt reprehenderit. Commodo laborum commodo ex ullamco nisi qui ad culpa fugiat nulla proident. Lorem fugiat cillum aliqua minim ex ut est nostrud esse amet commodo ea.', 'Nisi fugiat labore aliqua adipisicing officia incididunt reprehenderit aute pariatur aliquip magna commodo aliqua aliquip. Cupidatat pariatur irure qui laboris. Lorem minim Lorem excepteur culpa amet. Esse adipisicing ullamco ea magna aliquip esse dolor aliqua sint dolore cillum commodo. Proident ea et eiusmod fugiat in nisi sit Lorem ullamco.'];

        return $text[array_rand($text)];
    }

    public static function randomImage()
    {
        return 'https://picsum.photos/seed/'.rand().'/400';
    }

    public static function date($date, $format = 'd/m/Y \à H\hi')
    {
        if (!$date) return '';
        try {
            $date = preg_replace('/[(].+[)]/', '', $date);
            $datetime = new DateTimeFrench($date);
            $la_time = new DateTimeZone('Europe/Paris');
            $datetime->setTimezone($la_time);

            return $datetime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    public static function numerofacture()
    {
        $date = new DateTime();
        $formatDate = $date->format('Y-m-');

        $numeroFacture = MYSQL::selectOneValue("SELECT MAX(CAST(SUBSTRING(numero_facture, 9) AS UNSIGNED)) FROM facture WHERE numero_facture LIKE '{$formatDate}%'");

        if (empty($numeroFacture)) {
            $numeroFacture = $formatDate.'1';
        } else {
            $numeroFacture = $formatDate.($numeroFacture + 1);
        }

        return $numeroFacture;
    }

    public static function price($price, $symbol = false)
    {
        if (!$price) {
            $price = 0;
        }
        $number = number_format($price, 2, ',', '&#160;');

        return $symbol ? $number.'&#160;€' : $number;
    }

    public static function getClientLink($email, $id_contact)
    {
        if ($email) {
            $filter = ['Email' => $email];
            $member = Generique::selectOne('accounts', 'graphene_bsm', $filter);
            if ($member) {
                return ['type' => 'Membres', 'link' => $member->getIdClient()];
            }
        }
        if ($id_contact) {
            $filter = ['id' => $id_contact];
            $contact = Generique::selectOne('contacts', 'graphene_bsm', $filter);
            if ($contact) {
                return ['type' => 'Contacts', 'link' => $contact->getId()];
            }
        }
        if ($email) {
            $filter = ['couriel' => $email];
            $contact = Generique::selectOne('contacts', 'graphene_bsm', $filter);
            if ($contact) {
                return ['type' => 'Contacts', 'link' => $contact->getId()];
            }
        }
    }

    public static function roundUp($number, $precision = 2)
    {
        $fig = (int) str_pad('1', $precision, '0');
        return (ceil($number * $fig) / $fig);
    }

    public static function getProductPrice($price, $promo) {
        if (!$promo) return 0;
        if (strpos($promo, '%') === false) {
            $promo = number_format(round((float)$promo, 2), 2, '.', '');
            return round($price - $promo, 2);
        }
        $promo = number_format(round((float)$promo, 2), 2, '.', '');
        return round($price - ($price * $promo / 100), 2);
    }

    public static function addTVA($price, $tva) {
        return $price + ($price * $tva / 100);
    }

    public static function getFunction(string $id): ?string
    {
        return MYSQL::selectOneValue("SELECT {$id} FROM functions");
    }

    public static function getInitialesSiteName()
    {
        $nom_initiale = ''; // déclare le recipient
        $nom = str_replace("&rsquo;", '', self::getFunction('SiteName'));
        $nom = html_entity_decode($nom);
        $nom = preg_replace('/[^a-zA-ZÀ-ÿ]/', ' ', $nom);
        $nom = preg_replace("([A-Z])", " $0", $nom);
        $n_mot = explode(" ", $nom);
        foreach ($n_mot as $lettre) {
            if ($lettre)
                $nom_initiale .= $lettre[0] . '';
        }
        return strtoupper($nom_initiale);
    }
}

class DateTimeFrench extends DateTime
{
    public function format($format): string
    {
        $english_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $french_days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $english_small_days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $french_small_days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $english_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $french_months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $english_small_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $french_small_months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jui', 'Jui', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        $date = str_replace($english_days, $french_days, parent::format($format));
        $date = str_replace($english_months, $french_months, $date);
        $date = str_replace($english_small_days, $french_small_days, $date);
        $date = str_replace($english_small_months, $french_small_months, $date);
        return $date;
    }
}
