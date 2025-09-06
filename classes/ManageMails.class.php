<?php

class ManageMails
{
    /**
     * mailboxMails - Load mails from IMAP Server.
     *
     * @param mixed $folder      (name of folder to load or false)
     * @param mixed $pagination1 (offset of pagination)
     * @param mixed $pagination2 (length of pagination)
     */
    public static function mailboxMails($folder, $pagination1, $pagination2)
    {
        $mailbox = self::getMailbox($folder);

        try {
            $mails_ids = $mailbox->searchMailbox('All');

            rsort($mails_ids);
            $mails_ids = array_slice($mails_ids, $pagination1, $pagination2 * 3);
        } catch (Exception $e) {
            UTILS::notification('warning', 'Connexion IMAP echoué, vérifier vos informations', false, true);
            header('location: /Administration/Settings#nav-messagerie');

            exit;
        }

        $mailbox->setPathDelimiter('/');
        $mailbox->setServerEncoding('UTF-8');
        $mailbox->setAttachmentsDir(__DIR__.'/../uploads/ticket-system/imap/attachments');

        $countNewSubject = 0;

        $attachments = [];

        foreach ($mails_ids as $mail_id) {
            $req = MYSQL::query('SELECT MID FROM mail_receive WHERE MID = \''.$mail_id.'\' AND FOLDER = \''.$folder.'\'');

            if (mysqli_num_rows($req) < 1) {
                $email = $mailbox->getMail(
                    $mail_id, // ID of the email, you want to get
                     false // Do NOT mark emails as seen
                );

                if (isset($email->fromName)) {
                    $from_name = $email->fromName;
                } else {
                    $from_name = $email->fromAddress;
                }

                $resulttoEmail = serialize($email->to);
                $resultccEmail = serialize($email->cc);
                $getAttachments = $email->getAttachments();
                if ($email->hasAttachments()) {
                    foreach ($getAttachments as $value) {
                        $content = explode('/', $value->filePath);
                        $contentId = substr(end($content), 0, -4);
                        $attachments[] = $value->contentId.'::'.$value->name.'::'.$contentId;
                    }
                } else {
                    unset($attachments);
                }

                if (isset($attachments) && null != $attachments) {
                    $linkAttachments = serialize($attachments);
                } else {
                    $linkAttachments = null;
                }

                MYSQL::query('INSERT INTO mail_receive SET
                     name = \''.($from_name ? utf8_encode(addslashes($from_name)) : '').'\',
                     subject = \''.($email->subject ? utf8_encode(addslashes($email->subject)) : '').'\',
                     textHtml = \''.($email->textHtml ? utf8_encode(addslashes($email->textHtml)) : '').'\',
                     textPlain = \''.($email->textPlain ? utf8_encode(addslashes($email->textPlain)) : '').'\',
                     from_email = \''.($email->fromAddress ? addslashes($email->fromAddress) : '').'\',
                     to_email = \''.($resulttoEmail ? addslashes($resulttoEmail) : '').'\',
                     cc_email = \''.($resultccEmail ? addslashes($resultccEmail) : '').'\',
                     attachments =\''.($linkAttachments ? addslashes($linkAttachments) : '').'\',
                     date = \''.$email->date.'\',
                     MID = \''.$email->id.'\',
                     FOLDER = \''.($folder ? addslashes($folder) : '').'\'
                 ');

                ++$countNewSubject;
            }

            unset($attachments);
        }
        $mailbox->disconnect();
    }

    // * Get filter for queries
    public static function getFilter()
    {
        $f = DATA::getGet('act');
        if ('Sent' == $f || 'S' == $f) {
            return ['isDraft' => false, 'deleted' => false];
        }
        if ('Draft' == $f || 'D' == $f) {
            return ['isDraft' => true, 'deleted' => false];
        }
        if ('Important' == $f || 'I' == $f) {
            return ['deleted' => false, 'Important' => true];
        }
        if ('Trash' == $f || 'T' == $f) {
            return ['deleted' => true];
        }
        if (preg_match('/^F_.+$/', $f) || preg_match('/^FR_.+$/', $f)) {
            return ['deleted' => false, 'FOLDER' => substr($f, 2)];
        }

        return ['deleted' => false];
    }

    // * Get filter name
    public static function getFilterName()
    {
        $f = DATA::getGet('act');
        if (preg_match('/^F_.+$/', $f)) {
            return 'FR_'.substr($f, 2);
        }
        if ('Sent' == $f || 'S' == $f) {
            return 'S';
        }
        if ('Draft' == $f || 'D' == $f) {
            return 'D';
        }
        if ('Important' == $f || 'I' == $f) {
            return 'I';
        }
        if ('Trash' == $f || 'T' == $f) {
            return 'T';
        }

        return 'R';
    }

    /**
     * loadMails - GET list of mails.
     *
     * @param mixed $act (type or folder of mails)
     */
    public static function loadMails()
    {
        if (DATA::isGet('act') && (in_array(DATA::getGet('act'), ['S', 'R', 'T', 'I', 'D']) || preg_match('/^FR_.+$/', DATA::getGet('act')))) {
            $f = DATA::getGet('act');
            $folder = preg_match('/^FR_.+$/', $f) ? $folder = substr($f, 3) : '';

            $nbrMails = UTILS::getFunction('paginationEmail');
            $filter = self::getFilter();
            if ('S' == DATA::getGet('act')) {
                $table = 'mail_sent';
            } else {
                $table = 'mail_receive';
            }
            $offset = DATA::getGet('name') + 1;
            $filter = self::getFilter();
            $mails = Generique::select($table, 'graphene_bsm', $filter, 'date DESC', " {$offset}, {$nbrMails}");
            if (!$mails) {
                self::mailboxMails($folder, $offset, $nbrMails);
                $mails = Generique::select($table, 'graphene_bsm', $filter, 'date DESC', " {$offset}, {$nbrMails}");
            }

            $data = '';
            foreach ($mails as $mail) {
                $data .= $mail->getHtmlList();
            }
            print_r(json_encode([
                'data' => $data,
                'state' => true,
                'offset' => $offset + count($mails),
                // 'moreMails' => count($mails) == $nbrMails,
            ]));

            if (count($mails) < $nbrMails) {
                self::mailboxMails($folder, $offset, $nbrMails);
            }

            exit;
        }
    }

    /**
     * reloadMails - POST load new mails from serever.
     *
     * @param bool $reloadMails
     */
    public static function reloadMails()
    {
        if (DATA::isPost('reloadMails')) {
            $f = DATA::getGet('act');
            $folder = preg_match('/^FR_.+$/', $f) ? $folder = substr($f, 3) : '';
            $nbrMails = UTILS::getFunction('paginationEmail');
            self::mailboxMails($folder, 0, $nbrMails);

            exit;
        }
    }

    /**
     * loadOneMail - GET one mail.
     *
     * @param int $name
     */
    public static function loadOneMail()
    {
        if (DATA::isGet('name') && is_numeric(DATA::getGet('name'))) {
            $id = DATA::getGet('name');
            if ('Sent' == DATA::getGet('act')) {
                $table = 'mail_sent';
            } else {
                $table = 'mail_receive';
            }
            $filter = ['id' => $id];
            $mail = Generique::selectOne($table, 'graphene_bsm', $filter);
            if (!$mail) {
                print_r(json_encode(['state' => false]));

                exit;
            }
            if (false == $mail->getIsRead()) {
                $data = ['isRead' => true];
                Generique::update($table, 'graphene_bsm', $filter, $data);
                $mailbox = self::getMailbox();
                $mailbox->markMailAsRead($mail->getMID());
                $mailbox->disconnect();
            }
            $mail = [
                'data' => ['subject' => $mail->getSubject(),
                    'textHtml' => stripslashes($mail->getTextHtml()),
                    'fromEmail' => $mail->getFrom_email(),
                    'to' => $mail->getTo_emailLink(),
                    'toBcc' => $mail->getToBcc_emailLink(),
                    'toEmail' => $mail->getTo_email(),
                    'ccLink' => $mail->getCC_emailLink(),
                    'dest' => $mail->getDestLink(),
                    // 'dest' => ((explode('">', (explode("/Administration/Contacts/", $mail->getDestLink())[1]))[0] != $mail->getTo_email()) ? $mail->getFrom_email() : $mail->getDestLink()),
                    'date' => $mail->getLongFormatDate(),
                    'MID' => $mail->getMID(),
                    'id' => $mail->getId(),
                    'cc' => unserialize($mail->getSerializeCC()),
                    'bcc' => unserialize($mail->getSerializeBCC()),
                    'attachments' => $mail->getAttachments(),
                ],
                'state' => true,
            ];

            print_r(json_encode($mail, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE));

            exit;
        }
    }

    /**
     * sendMail - POST to send mails.
     *
     * @param mixed  $cc
     * @param mixed  $bcc
     * @param string $object
     * @param string $content
     * @param File   $files
     */
    public static function sendMail()
    {
        if (DATA::isPost('object')) {
            $attachments = [];

            $files = [];
            foreach ($_FILES['attachments'] as $k => $l) {
                foreach ($l as $i => $v) {
                    if (!array_key_exists($i, $files)) {
                        $files[$i] = [];
                    }
                    $files[$i][$k] = $v;
                }
            }

            include 'classes/upload.class.php';
            if (isset($files)) {
                foreach ($files as $file) {
                    $handle = new upload($file, 'fr_FR');
                    if ($handle->uploaded) {
                        $handle->process('uploads/attachments');
                        if ($handle->processed) {
                            $attachments[] = ['uploads/attachments/'.$handle->file_dst_name => $handle->file_dst_name];
                        }
                    }
                }
            }
            if (DATA::isPost('includeDraftAttachments') && DATA::isPost('id')) {
                $filter = ['id' => DATA::getPost('id')];
                $prevMail = Generique::selectOne('mail_sent', 'graphene_bsm', $filter);
                if ($prevMail) {
                    $prevAttachments = $prevMail->getAttachments();
                    foreach ($prevAttachments as $pA) {
                        $attachments[] = [$pA['url'] => $pA['name']];
                    }
                }
            }

            $bcc = DATA::isPost('bcc') ? array_filter(explode(',', str_replace(' ', ',', DATA::getPost('bcc')))) : false;
            $cc = array_filter(explode(',', str_replace(' ', ',', DATA::getPost('cc'))));
            $isDraft = DATA::isPost('draft') || !(DATA::isPost('cc') || DATA::isPost('bcc'));
            $object = DATA::getPost('object', false);
            $content = UTILS::tplMail(false, false, $object, DATA::getPost('content', false), 'column');
            UTILS::MAIL($cc, $object, $content, $attachments, null, true, $isDraft, $bcc);
            if (DATA::isPost('id')) {
                $filter = ['id' => DATA::getPost('id')];
                Generique::delete('mail_sent', 'graphene_bsm', $filter);
            }
            if (!$isDraft) {
                if (is_array($cc)) {
                    $countCC = count($cc);
                    UTILS::addHistory(USER::getPseudo(), 55, "« {$object} »  envoyé à {$countCC} contacts");
                } else {
                    UTILS::addHistory(USER::getPseudo(), 55, "« {$object} »  envoyé à {$cc}");
                }
                UTILS::notification('success', 'Votre message a été envoyé', true, true);
            } else {
                UTILS::addHistory(USER::getPseudo(), 55, "Brouillon « {$object} » enregistré");
                UTILS::notification('success', 'Le brouillon a été enregistré', true, true);
            }

            exit;
        }
    }

    /**
     * deleteMail - POST to delete mails.
     *
     * @param bool  $trash
     * @param mixed $id
     */
    public static function deleteMail()
    {
        if (DATA::isPost('trash') && DATA::isPost('id') && (is_array($_POST['id']) || is_numeric(DATA::getPost('id')))) {
            $deleteTrash = false;
            if (DATA::isGet('act') && 'Trash' == DATA::getGet('act')) {
                $deleteTrash = true;
            }
            if (DATA::isGet('act') && 'Sent' == DATA::getGet('act')) {
                $table = 'mail_sent';
            } else {
                $table = 'mail_receive';
            }

            if ($deleteTrash) {
                $mailbox = self::getMailbox();
            }

            $data = ['deleted' => true];
            if (is_array($_POST['id'])) {
                foreach ($_POST['id'] as $value) {
                    $filter = ['id' => $value];
                    if ($deleteTrash) {
                        $mail = Generique::selectOne($table, 'graphene_bsm', $filter);
                        $mailbox->deleteMail($mail->getMID());
                        Generique::delete($table, 'graphene_bsm', $filter);
                    } else {
                        Generique::update($table, 'graphene_bsm', $filter, $data);
                    }
                }
            } else {
                $filter = ['id' => DATA::getPost('id')];
                if ($deleteTrash) {
                    $mail = Generique::selectOne($table, 'graphene_bsm', $filter);
                    $mailbox->deleteMail($mail->getMID());
                    Generique::delete($table, 'graphene_bsm', $filter);
                } else {
                    Generique::update($table, 'graphene_bsm', $filter, $data);
                }
            }

            if ($deleteTrash) {
                $mailbox->disconnect();
            }

            exit;
        }
    }

    /**
     * importantMail - POST to mark mails as important.
     *
     * @param bool  $important
     * @param mixed $id
     */
    public static function importantMail()
    {
        if (isset($_POST['important']) && DATA::isPost('id') && (is_array($_POST['id']) || is_numeric(DATA::getPost('id')))) {
            if (DATA::isGet('act') && 'Sent' == DATA::getGet('act')) {
                $table = 'mail_sent';
            } else {
                $table = 'mail_receive';
            }

            $mailbox = self::getMailbox();
            $data = ['important' => DATA::getPost('important')];
            if (is_array($_POST['id'])) {
                $MIDS = [];
                foreach ($_POST['id'] as $value) {
                    $filter = ['id' => $value];
                    Generique::update($table, 'graphene_bsm', $filter, $data);
                    $mail = Generique::selectOne($table, 'graphene_bsm', $filter);
                    $MIDS[] = $mail->getMID();
                }
                $mailbox->markMailsAsImportant($MIDS);
            } else {
                $id = DATA::getPost('id');
                $filter = ['id' => $id];
                Generique::update($table, 'graphene_bsm', $filter, $data);
                $mail = Generique::selectOne($table, 'graphene_bsm', $filter);
                $mailbox->markMailAsImportant($mail->getMID());
            }
            print_r(json_encode(['state' => true, 'important' => DATA::getPost('important')]));

            $mailbox->disconnect();

            exit;
        }
    }

    /**
     * markReadMails - POST to mark mails as read.
     *
     * @param bool  $read
     * @param mixed $id
     */
    public static function markReadMails()
    {
        if (DATA::isPost('read') && DATA::isPost('id') && (is_array($_POST['id']) || is_numeric(DATA::getPost('id')))) {
            if (DATA::isGet('act') && 'Sent' == DATA::getGet('act')) {
                $table = 'mail_sent';
            } else {
                $table = 'mail_receive';
            }

            $mailbox = self::getMailbox();
            $data = ['isRead' => true];
            if (is_array($_POST['id'])) {
                $MIDS = [];
                foreach ($_POST['id'] as $value) {
                    $filter = ['id' => $value];
                    Generique::update($table, 'graphene_bsm', $filter, $data);
                    $mail = Generique::selectOne($table, 'graphene_bsm', $filter);
                    $MIDS[] = $mail->getMID();
                }
                $mailbox->markMailsAsRead($MIDS);
            } else {
                $id = DATA::getPost('id');
                $filter = ['id' => $id];
                Generique::update($table, 'graphene_bsm', $filter, $data);
                $mail = Generique::selectOne($table, 'graphene_bsm', $filter);
                $mailbox->markMailAsRead($mail->getMID());
            }
            print_r(json_encode(['state' => true]));

            $mailbox->disconnect();

            exit;
        }
    }

    // * GET ROUTE to load Folders
    public static function loadFolders()
    {
        if (DATA::isPost('loadFolder')) {
            print_r(json_encode(['state' => true, 'data' => self::getFolders()]));

            exit;
        }
    }

    // * Functions to get Folders
    private static function getFolders()
    {
        // function getFolder($folder)
        // {
        //     if (preg_match('/}/i', $folder)) {
        //         $arr = explode('}', $folder);
        //     }

        //     return $arr[1];
        // }
        $mailbox = ManageMails::getMailbox();
        $folders = [];
        foreach ($mailbox->getListingFolders() as $folder) {
            if (preg_match('/}/i', $folder)) {
                $arr = explode('}', $folder);
            }

            //also remove the ] if it exists, normally Gmail have them
            if (preg_match('/]/i', $folder)) {
                $arr = explode(']/', $folder);
            }

            if (isset($arr[1]) && 0 == MYSQL::selectOneValue("SELECT count(*) FROM mail_receive WHERE FOLDER = '{$arr[1]}' AND isRead = false")) {
                $folders[] = $arr[1];
            }
        }

        return $folders;
    }

    /**
     * getMailbox - Get $maiblox variable for query.
     *
     * @param mixed $folder (name of folder or false)
     *
     * @return PhpImap\Mailbox $mailbox
     */
    private static function getMailbox($folder = false)
    {
        if (!UTILS::getFunction('emailIMAP')) {
            UTILS::notification('danger', 'La messagerie n\'est pas configuré.', false, true);
            header('location: /Administration/Settings#nav-messagerie');

            exit;
        }

        $nocert = '';
        $imap = UTILS::getFunction('IMAP');
        if ('imap.gmail.com' == $imap) {
            $nocert = '/novalidate-cert';
        }

        // Create PhpImap\Mailbox instance for all further actions
        return new PhpImap\Mailbox(
            '{'.UTILS::getFunction('IMAP').':'.UTILS::getFunction('portMessaging').'/imap/ssl'.$nocert.'}'.($folder && false != $folder ? htmlentities($folder) : ''), // IMAP server and mailbox folder  {imap.gmail.com:993/imap/ssl}INBOX
            UTILS::getFunction('emailIMAP'), // Username for the before configured mailbox
            UTILS::getFunction('passwordIMAP'), // Password for the before configured username
            __DIR__.'/../uploads/ticket-system/imap/attachments', // Directory, where attachments will be saved (optional)
            'UTF-8' // Server encoding (optional)
        );
    }

    public static function searchMail()
    {
        if (DATA::isPost('search')) {
            $search = html_entity_decode(DATA::getPost('search'));
            $mails = [];
            $request = MYSQL::query("SELECT id, name, subject, date FROM mail_receive WHERE name LIKE '%{$search}%' OR subject LIKE '%{$search}%' OR cc_email LIKE '%{$search}%' OR to_email LIKE '%{$search}%' OR from_email LIKE '%{$search}%' OR textHtml LIKE '%{$search}%' ORDER BY `date` DESC LIMIT 100");
            while ($r = mysqli_fetch_object($request)) {
                $mails[] = ['value' => $r->id, 'label' => UTILS::date($r->date, 'd/m/Y'). ' ['.iconv('utf-8', 'latin1', $r->name) . '] ' . iconv('utf-8', 'latin1', $r->subject)];
            }
            echo json_encode($mails);
            exit;
        }
    }
}
