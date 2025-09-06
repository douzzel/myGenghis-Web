<?php

$tplSettings = new Template();
$tplSettings->setFile('settings', './_admin/componants/users/accountSettings.html');

$idClient = USER::getPseudo();

if (DATA::isPost('deleteAccount')) {
    UTILS::Alert('danger', 'Suppression de votre compte, voulez-vous vraiment le supprimer ?', 'Cet action est irreversible', $_SERVER['REQUEST_URI'], 'deleteAccountOK', DATA::getPost('deleteAccount'));
}

if (DATA::isPost('deleteAccountOK')) {
    $filter = ['Pseudo' => $idClient];

    /*
    // check if a NC account exists under the same pseudo
    $ncAccountCheck = NCMYSQL::query("SELECT * FROM oc_accounts WHERE uid = '".$idClient."'");
    if (mysqli_num_rows($ncAccountCheck) > 0) {
        // deactivate NC user account
        nupa::disableUser($idClient);
    }
    else {
        // if pseudos are not the same, look for a NC account that has the same email address and retrieve pseudo from it
        $reqPseudo = MYSQL::query("SELECT * FROM accounts WHERE pseudo = '" . $idClient . "'");
        $mgAccount = mysqli_fetch_object($reqPseudo);
        $ncFetchAccount = NCMYSQL::query('SELECT uid FROM oc_accounts WHERE data LIKE \'%"email":{"value":"'.$mgAccount->Email.'",%\'');
        if (mysqli_num_rows($ncFetchAccount) > 0) {
            // deactivate NC user account
            nupa::disableUser($ncFetchAccount->uid);
        }
    }
    */

    // check if HumHub account exists
    $hhFile = "./classes/humhub";
    if(is_dir($hhFile)) {
        $reqHHAccount = hupa::idFromUsername($idClient);
        if (is_numeric($reqHHAccount)) {
            // delete sessions and soft-delete user
            hupa::sessionDel($idClient);
            hupa::softDelUser($idClient);
        }
    }

    // remove member from Graphene
    Generique::delete('accounts', 'graphene_bsm', $filter);
    // confirmation of action
    UTILS::notification('success', 'Votre compte a été supprimé avec succès', false, true);
    header('Location: /Deconnexion');

    exit;
}

// $request = (true == $AdminMemberID ? "id_client = '" . DATA::getGet('Member') . "'" : "pseudo = '" . USER::getPseudo() . "'");
$request = 'pseudo = \''.USER::getPseudo().'\'';

if (DATA::isPost('update')) {
    IMAGE::upload($_FILES['avatar'], 'themes/assets/images/avatars', USER::getPseudo(), 256, true);

    $req = MYSQL::query('SELECT * FROM accounts WHERE ' . $request);
    $resultAccount = mysqli_fetch_object($req);

    if (DATA::isPost('phone_home') or DATA::isPost('phone_cell') or DATA::isPost('phone_job')) {
        $isUserChecked = MYSQL::query('SELECT id_client FROM phone WHERE id_client = \'' . $resultAccount->id_client . '\'');
        $filter = ['id_client' => $resultAccount->id_client];
        $data = ['Home' => DATA::getPost('phone_home'), 'Cellular' => DATA::getPost('phone_cell'), 'Job' => DATA::getPost('phone_job')];
        if (mysqli_num_rows($isUserChecked) > 0) {
            Generique::update('phone', 'graphene_bsm', $filter, $data);
        } else {
            $data = array_merge($filter, $data);
            Generique::insert('phone', 'graphene_bsm', $data);
        }
    }

    if (strlen(DATA::getPost('month')) < 2) {
        $month = '0' . DATA::getPost('month');
    } else {
        $month = DATA::getPost('month');
    }

    if (DATA::getPost('theme')) {
        $reqCol = MYSQL::selectOneRow("SELECT * FROM couleur_theme WHERE Pseudo = '{$idClient}'");
        $filter = ['Pseudo' => $idClient];
        $data = ['theme' => DATA::getPost('theme'), 'color1' => DATA::getPost('color1'), 'color2' => DATA::getPost('color2'), 'color3' => DATA::getPost('color3'), 'color4' => DATA::getPost('color4'), 'bgcolor' => DATA::getPost('bgcolor'), 'darkcolor' => DATA::getPost('darkcolor')];
        if ($reqCol) {
            Generique::update('couleur_theme', 'graphene_bsm', $filter, $data);
        } else {
            $data = array_merge($filter, $data);
            Generique::insert('couleur_theme', 'graphene_bsm', $data);
        }
    }

    $data = ['Prenom' => DATA::getPost('firstname'), 'Nom' => DATA::getPost('lastname'), 'civilite' => DATA::getPost('civilite'), 'DateNaissance' => DATA::getPost('year') . '-' . $month . '-' . DATA::getPost('days'), 'Pays' => DATA::getPost('country'), 'Adresse' => DATA::getPost('address'), 'CodePostal' => DATA::getPost('postal_code'), 'ville' => DATA::getPost('locality'), 'signature' => DATA::getPost('signature'), 'about' => DATA::getPost('description')];
    // Avoid multiple accounts with same mail
    if (!Generique::selectOne('accounts', 'graphene_bsm', ['Email' => DATA::getPost('email')])) {
        $data['Email'] = DATA::getPost('email');
    }
    $filter = ['id_client' => $resultAccount->id_client];
    Generique::update('accounts', 'graphene_bsm', $filter, $data);

    /*
    // check if a NC account exists under the same pseudo
    $ncAccountCheck = NCMYSQL::query("SELECT * FROM oc_accounts WHERE uid = '".$resultAccount->Pseudo."'");
    if (mysqli_num_rows($ncAccountCheck) > 0) {
        // update NC account details
        $ncusername = $resultAccount->Pseudo;
        $ncusermail = DATA::getPost('email');
        $ncuserdisp = DATA::getPost('firstname') . DATA::getPost('lastname');
        nupa::editUser($ncusername, "email", $ncusermail);
        if ($ncuserdisp != "") {
            $ncuserdisp = DATA::getPost('firstname') . ' ' . DATA::getPost('lastname');
            nupa::editUser($ncusername, "displayname", $ncuserdisp);
        }
    }
    else {
        // if pseudos are not the same, look for a NC account that has the same email address and retrieve pseudo from it
        $ncFetchAccount = NCMYSQL::query('SELECT uid FROM oc_accounts WHERE data LIKE \'%"email":{"value":"'.$resultAccount->Email.'",%\'');
        if (mysqli_num_rows($ncFetchAccount) > 0) {
            // update NC account details
            $ncusername = $ncFetchAccount->uid;
            $ncusermail = DATA::getPost('email');
            $ncuserdisp = DATA::getPost('firstname') . DATA::getPost('lastname');
            nupa::editUser($ncusername, "email", $ncusermail);
            if ($ncuserdisp != "") {
                $ncuserdisp = DATA::getPost('firstname') . ' ' . DATA::getPost('lastname');
                nupa::editUser($ncusername, "displayname", $ncuserdisp);
            }
        }
    }
    */

    // update HumHub account
    $hhFile = "./classes/humhub";
    if(is_dir($hhFile)) {
        hupa::updateUser($resultAccount->Pseudo, DATA::getPost('email'), DATA::getPost('firstname'), DATA::getPost('lastname'), DATA::getPost('civilite'), DATA::getPost('address'), DATA::getPost('postal_code'), DATA::getPost('locality'), DATA::getPost('country'), DATA::getPost('year') . '-' . $month . '-' . DATA::getPost('days'), DATA::getPost('phone_home'), DATA::getPost('phone_job'), DATA::getPost('phone_cell'), "");
    }

    // update "contact" details from account changes
    $data = ['nom' => DATA::getPost('lastname'), 'prenom' => DATA::getPost('firstname'), 'adresse' => DATA::getPost('address'), 'cp' => DATA::getPost('postal_code'), 'ville' => DATA::getPost('locality'), 'pays' => DATA::getPost('country'), 'telephone' => DATA::getPost('phone_job'), 'portable' => DATA::getPost('phone_cell'), 'tel_perso' => DATA::getPost('phone_home'), 'couriel' => DATA::getPost('email')];
    Generique::update('contacts', 'graphene_bsm', $filter, $data);

    UTILS::addHistory(USER::getPseudo(), 4, ' Modification des informations du compte membre', '/Account/Settings');

    /*
    // as it is no longer possible for a member to change their 'pseudo', this is not needed
    if (DATA::getPost('username') != USER::getPseudo()) {
        UTILS::notification('success', 'Vos informations ont été mis à jour avec succès. Vous devez vous reconnecter', false, true);
        header('location: /Deconnexion');
        exit;
    }
    */

    // if ($AdminMemberID) {
    //     UTILS::notification('success', 'Les informations de ' . $resultAccount->Pseudo . ' ont été mis à jour avec succès.');
    // } else {
    UTILS::notification('success', 'Vos informations ont été mis à jour avec succès.');
    // }
}

$req = MYSQL::query('SELECT * FROM accounts WHERE ' . $request);
$resultAccount = mysqli_fetch_object($req);

// Menu déroulant années
foreach (range(date('Y'), 1901) as $x) {
    $tplSettings->bloc('SELECT_YEAR_LISTE', [
        'VALUES' => $x,
    ]);

    if ($resultAccount->DateNaissance && date('Y', strtotime($resultAccount->DateNaissance)) == $x) {
        $tplSettings->bloc('SELECT_YEAR_LISTE.SELECTED');
    }
}

$mois_fr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

for ($i = 1; $i <= 12; ++$i) {
    $tplSettings->bloc('SELECT_MONTH_LISTE', [
        'VALUES' => $mois_fr[$i],
        'NUMBER' => $i,
    ]);

    if ($resultAccount->DateNaissance && date('m', strtotime($resultAccount->DateNaissance)) == $i) {
        $tplSettings->bloc('SELECT_MONTH_LISTE.SELECTED');
    }
}

for ($i = 1; $i <= 31; ++$i) {
    $tplSettings->bloc('SELECT_DAY_LISTE', [
        'VALUES' => $i,
    ]);

    if ($resultAccount->DateNaissance && date('d', strtotime($resultAccount->DateNaissance)) == $i) {
        $tplSettings->bloc('SELECT_DAY_LISTE.SELECTED');
    }
}

if (null != UTILS::getFunction('API_KEYS_GOOGLE_MAP')) {
    $tplSettings->bloc('IF_IS_API_KEY_GOOGLE_MAP');
}

if (ManageRights::verifyRights('Menu', 'Read', false, false)) {
    $tplSettings->bloc('IF_PERM_MENU');
}

$reqPhone = MYSQL::query('SELECT * FROM phone WHERE id_client = \'' . $resultAccount->id_client . '\'');
$phone = mysqli_fetch_object($reqPhone);

$reqCol = MYSQL::selectOneRow("SELECT * FROM couleur_theme WHERE Pseudo = '{$idClient}'");
$col = $reqCol ? $reqCol : MYSQL::selectOneRow("SELECT * FROM couleur_theme WHERE Pseudo = 'default'");

$tplSettings->values([
    'THEME' => $col['theme'],
    'COLOR1' => $col['color1'],
    'COLOR2' => $col['color2'],
    'COLOR3' => $col['color3'],
    'COLOR4' => $col['color4'],
    'BGCOLOR' => $col['bgcolor'],
    'DARKCOLOR' => $col['darkcolor'],
    'AVATAR' => UTILS::getAvatar($resultAccount->Pseudo),
    'BGPROFIL' => UTILS::GetBgProfil($resultAccount->Pseudo),
    'PSEUDO' => $resultAccount->Pseudo,
    'NOM' => $resultAccount->Nom,
    'PRENOM' => $resultAccount->Prenom,
    'CIVILITE' => $resultAccount->civilite,
    'EMAIL' => $resultAccount->Email,
    'DATE_INSCRIPTION' => $resultAccount->Date_Inscription,
    'PAYS' => $resultAccount->Pays,
    'ADRESSE' => $resultAccount->Adresse,
    'CP' => $resultAccount->CodePostal,
    'VILLE' => $resultAccount->Ville,
    'URL' => UTILS::getFunction('StaticUrl'),
    'API_KEYS_GOOGLE' => UTILS::getFunction('API_KEYS_GOOGLE_MAP'),
    'ABOUT' => $resultAccount->about,
    'SIGNATURE' => $resultAccount->signature,
    'URL_ACT' => $_SERVER['REQUEST_URI'],
    'PHONE_HOME' => (isset($phone->Home) ? $phone->Home : null),
    'PHONE_CELL' => (isset($phone->Cellular) ? $phone->Cellular : null),
    'PHONE_JOB' => (isset($phone->Job) ? $phone->Job : null),
]);

if (DATA::isPost('password') && DATA::isPost('newPassword') && DATA::isPost('newPasswordConfirm')) {
    $req = MYSQL::query('SELECT Password FROM accounts WHERE id_client = \'' . $resultAccount->id_client . '\' AND Password = \'' . hash('sha256', DATA::getPost('password')) . '\'');
    if (mysqli_num_rows($req) > 0) {
        if (DATA::getPost('newPassword') === DATA::getPost('newPasswordConfirm')) {
            $filter = ['id_client' => $resultAccount->id_client];
            $data = ['Password' => hash('sha256', DATA::getPost('newPassword'))];
            Generique::update('accounts', 'graphene_bsm', $filter, $data);

            /*
            // check if a NC account exists under the same pseudo
            $ncAccountCheck = NCMYSQL::query("SELECT * FROM oc_accounts WHERE uid = '".$resultAccount->Pseudo."'");
            if (mysqli_num_rows($ncAccountCheck) > 0) {
                // update user's NC details
                $ncusername = $resultAccount->Pseudo;
                $ncuserpass = DATA::getPost('newPassword');
                nupa::editUser($ncusername, "password", $ncuserpass);
            }
            else {
                // if pseudos are not the same, look for a NC account that has the same email address and retrieve pseudo from it
                $ncFetchAccount = NCMYSQL::query('SELECT uid FROM oc_accounts WHERE data LIKE \'%"email":{"value":"'.$resultAccount->Email.'",%\'');
                if (mysqli_num_rows($ncFetchAccount) > 0) {
                    // update user's NC details
                    $ncusername = $ncFetchAccount->uid;
                    $ncuserpass = DATA::getPost('newPassword');
                    nupa::editUser($ncusername, "password", $ncuserpass);
                }
            }
            */

            // update HumHub account
            $hhFile = "./classes/humhub";
            if(is_dir($hhFile)) {
                hupa::updateUser($resultAccount->Pseudo, DATA::getPost('email'), DATA::getPost('firstname'), DATA::getPost('lastname'), DATA::getPost('civilite'), DATA::getPost('address'), DATA::getPost('postal_code'), DATA::getPost('locality'), DATA::getPost('country'), DATA::getPost('year') . '-' . $month . '-' . DATA::getPost('days'), DATA::getPost('phone_home'), DATA::getPost('phone_job'), DATA::getPost('phone_cell'), DATA::getPost('newPassword'));
            }

            UTILS::addHistory(USER::getPseudo(), 4, ' Modification du mot de passe', '/Account/Settings');
            UTILS::notification('success', 'Mot de passe changé avec succès.', false, true);
        } else {
            UTILS::notification('warning', 'Le mot de passe de confirmation ne correspond pas.', false, true);
        }
    } else {
        UTILS::notification('danger', 'Merci de correctement saisir votre mot de passe actuel.', false, true);
    }
}

$tplSettings->bloc('PASSWORD');

$TITRE = $DESCRIPTION = 'Paramètres du compte';
$PAGES = $tplSettings->construire('settings');
