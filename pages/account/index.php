<?php
    $PAGES = null;
    $AdminMemberID = false;
    $permission = ManageRights::verifyRights('Membres', 'Read', false, false);
    if (USER::isConnecte()) {
        // if (DATA::isGet('Member')) {
        //     if ($permission || DATA::getGet('Page') == 'Messages') {
        //         $AdminMemberID = true;
        //     } else {
        //         header('location: /404');
        //     }
        // }

        $titre = "Erreur de page";
        $PAGE = null;
        $tplIndex = new Template;
        $tplIndex->setFile('index', './account/index.html');
        // $tplIndex->bloc('IF_IS_CONNECTE');

        // if (DATA::isGet('Member')) {
        //   $id = '/ID-'.DATA::getGet('Member');
        //   $tplIndex->bloc('IF_IS_CONNECTE.IS_IS_MEMBER');

        //   if ($permission) {
        //     $tplIndex->bloc('IF_IS_CONNECTE.IS_IS_MEMBER.IF_IS_ADMIN');
        //   }
        // }

        // $tplIndex->bloc(UTILS::isModuleActive('STORE') ? 'IF_IS_CONNECTE.IF_STORE' : 'IF_IS_CONNECTE.IF_NOT_STORE');

        // if (UTILS::isModuleActive('FORUM'))
        //     $tplIndex->bloc('IF_IS_CONNECTE.IF_FORUM');

        // if (ManageRights::verifyRights('Menu', 'Read', false, false)) {
        //     $tplIndex->bloc('IF_IS_CONNECTE.IS_MENU');
        // }

        $storeName = MYSQL::selectOneValue("SELECT ModuleTitle FROM modulespages WHERE NameModule = 'STORE'");

        switch (DATA::getGet('Page')) {
            case 'Settings':
                $titre = "Paramètres";
                $description = 'Gestion des paramètres du compte.';
                require('componants/settings.php');
                // $tplIndex->values(array('SETTINGS' => "color-theme"));
                break;
            // case 'Security':
            //     $titre = "Securité";
            //     $description = 'Gestion des paramètres de securité.';
            //     require('componants/security.php');
            //     break;
            // case 'Access':
            //     $titre = "Accès";
            //     $description = 'Gestion des paramètres d\'accès.';
            //     require('componants/access.php');
            //     break;
            case 'History':
                $titre = "Historique";
                $description = 'Gestion de l\'historique.';
                require('componants/history.php');
                break;

            // case 'Wallet':
            //     $titre = "Porte monnaie";
            //     $description = 'Gestion du porte monnaie virtuel.';
            //     require('componants/wallet.php');
            //     break;
            // case 'Profil':
            //     $titre = "Actualités";
            //     $description = 'Dernières actualités';
            //     require('componants/profil.php');
            //     $tplIndex->value('PROFIL', 'active');
                // break;
            case 'Documents':
                $description = 'Gestion des documents';
                require('componants/documents.php');
                $titre = $TITRE;
                // $tplIndex->value('DOCUMENTS', 'active');
                break;
            // case 'Messages':
            //     $titre = "Messages";
            //     $description = '';
            //     require('componants/messages.php');
            //     $tplIndex->value('MESSAGES', 'active');
            //     break;
            // case 'Discussion':
            //     $titre = "Discussion";
            //     $description = '';
            //     require('componants/discussion.php');
            //     $tplIndex->value('DISCUSSION', 'active');
            //     break;
            case 'Store':
                $titre = $storeName;
                $description = '';
                require('componants/store.php');
                // $tplIndex->value('STORE', 'active');
                break;
            case 'Site':
                $titre = "Site";
                $description = '';
                require('componants/site.php');
                // $tplIndex->value('SITE', 'active');
                break;
            case 'ClickAndCollect':
                $titre = "ClickAndCollect";
                $description = '';
                require('componants/click&collect.php');
                // $tplIndex->value('CLICKANDCOLLECT', 'active');
                break;

            // Forum
            // case 'Forum':
            //     require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/index.php');
            //     $titre = $TITRE;
            //     $description = $DESCRIPTION;
            //     $tplIndex->value('FORUM', 'active');
            //     break;
            // case 'Section':
            //     require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/section.php');
            //     $titre = $TITRE;
            //     $description = $DESCRIPTION;
            //     $tplIndex->value('FORUM', 'active');
            //     break;
            // case 'Sujet':
            //     require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/sujet.php');
            //     $titre = $TITRE;
            //     $description = $DESCRIPTION;
            //     $tplIndex->value('FORUM', 'active');
            //     break;
            // case 'Modifier':
            //     require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/modifier.php');
            //     $titre = $TITRE;
            //     $description = $DESCRIPTION;
            //     $tplIndex->value('FORUM', 'active');
            //     break;
            // case 'Nouveau':
            //     require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/nouveau.php');
            //     $titre = $TITRE;
            //     $description = $DESCRIPTION;
            //     $tplIndex->value('FORUM', 'active');
            //     break;
            default:
                header('location: /classes/humhub');
                exit;
                break;
        }

        // if (isset($MENU)) {
        //     $tplIndex->bloc($MENU[0], array(
        //         'URL' => $MENU[1]
        //     ));
        // }

        // switch($AdminMemberID){
        //     case true :
        //         $request = 'id_client = \''.DATA::getGet('Member').'\'';
        //     break;
        //     default :
        // $request = 'pseudo = \''.USER::getPseudo().'\'';
        //     break;
        // }

        // $req = MYSQL::query('SELECT * FROM accounts WHERE ' . $request);
        // $resultAccount = mysqli_fetch_object($req);

        // $dateInscription = date('d M Y', strtotime($resultAccount->Date_Inscription));


        // $reqPhone = MYSQL::query('SELECT * FROM phone WHERE id_client = \'' . $resultAccount->id_client . '\'');
        // $phone = mysqli_fetch_object($reqPhone);

        // $userPseudo = USER::getPseudo();
        // if (MYSQL::selectOneValue("SELECT clickCollect FROM accounts WHERE pseudo = '$userPseudo'")) {
        //   $tplIndex->bloc('IF_IS_CONNECTE.IF_IS_CLICKANDCOLLECT_PLACE');
        // }

        // $bg = MYSQL::selectOneValue("SELECT image FROM documents WHERE ref = 'ESPACE_MEMBRE'");
        // if (!$bg)
        //   $bg = MYSQL::selectOneValue("SELECT image FROM user_custom_page");

        // $dateConnexion = MYSQL::selectOneValue("SELECT isDate FROM historique WHERE	memb___id = '{$resultAccount->Pseudo}' AND idType_historique = 9 ORDER BY id DESC LIMIT 1");

        //   $alias = UTILS::getFunction('Alias');
        //   $textUniverse = UTILS::getFunction('textUniverse');
          $tplIndex->values(array(
            'PAGE' => $PAGES,
        //     'ID_MEMBRE' =>$resultAccount->id_client,
        //     'TITRE' => $titre,
        //     'DESCRIPTION' => $description,
        //     'PSEUDO' => $resultAccount->Pseudo,
        //     'BGPROFIL' => $bg,
        //     'AVATAR' => UTILS::GetAvatar($resultAccount->Pseudo),
        //     'NOM' => $resultAccount->Nom,
        //     'PRENOM' => $resultAccount->Prenom,
        //     'EMAIL' => $resultAccount->Email,
        //     'PHONE' => $phone->Cellular ?? ($phone->Home ?? ($phone->Job ?? '')),
        //     'DATE_INSCRIPTION' => $dateInscription,
        //     // 'DATE_CONNEXION' => date('d M Y', strtotime($dateConnexion)),
        //     'PAYS' => $resultAccount->Pays != 'France' ? $resultAccount->Pays : "",
        //     'VILLE' => $resultAccount->Ville,
        //     'ADRESSE' => "{$resultAccount->Adresse} {$resultAccount->CodePostal}",
        //     'URL' => UTILS::getFunction('StaticUrl'),
        //     'ABOUT' => $resultAccount->about,
        //     'SIGNATURE' => $resultAccount->signature,
        //     'ALIAS' => $alias ? html_entity_decode($alias) : '',
        //     'CACHE_LOGO' => filemtime('./themes/assets/images/logo.png'),
        //     'textUniverse' => $textUniverse ? html_entity_decode($textUniverse) : '',
        //     'STORE_NAME' => $storeName
        ));



        $PAGE = $tplIndex->construire('index');
        $TITRE = $titre;
        $DESCRIPTION = $description;
    } else {
        // switch (DATA::getGet('Page')) {
        //   case 'Forum':
        //       require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/index.php');
        //       break;
        //   case 'Section':
        //       require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/section.php');
        //       break;
        //   case 'Sujet':
        //       require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/sujet.php');
        //       break;
        //   case 'Modifier':
        //       require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/modifier.php');
        //       break;
        //   case 'Nouveau':
        //       require($_SERVER['DOCUMENT_ROOT'].'/pages/forum/nouveau.php');
        //       break;
        //   default:
              UTILS::notification('warning', 'Vous devez être connecté pour accéder à cette page.', false, true);
              header('location: /Connexion');
              exit;
            //   break;
        //   }
        //   $PAGE = $PAGES;
    }
