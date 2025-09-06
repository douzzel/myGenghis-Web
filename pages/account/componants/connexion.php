<?php
if (!USER::isConnecte()) {
$tplconnexion = new Template;
$tplconnexion->setFile('connexion', './account/componants/connexion.html');
// ########## i18n - l10n - g11n ########
$site = UTILS::getFunction('StaticUrl');
if ($site == 'graphene-bsm.com') {
// SIGN_IN
$tplconnexion->value('I18N_SIGN_IN', "Connection");
$tplconnexion->value('I18N_SIGN_IN_ID', 'Login');
$tplconnexion->value('I18N_SIGN_IN_PASS', "Password");
$tplconnexion->value('I18N_SIGN_IN_BTN', "Sign in");
$tplconnexion->value('I18N_SIGN_IN_FORGET', "Forgotten password?");
// SIGN_UP
$tplconnexion->value('I18N_SIGN_UP', "Inscription");
$tplconnexion->value('I18N_SIGN_UP_ID', "Username");
$tplconnexion->value('I18N_SIGN_UP_ID_MSG', "At least 4 characters long! Only use letters without accents and numbers, other characters (such as '_', '.' and '-') are not permitted.");
$tplconnexion->value('I18N_SIGN_UP_FNAME', "First Name");
$tplconnexion->value('I18N_SIGN_UP_LNAME', "Last Name");
$tplconnexion->value('I18N_SIGN_UP_EMAIL', "Email");
$tplconnexion->value('I18N_SIGN_UP_EMAIL_R', "Repeat email");
$tplconnexion->value('I18N_SIGN_UP_PASS', "Password");
$tplconnexion->value('I18N_SIGN_UP_PASS_MSG', "Minimum 10 characters!");
$tplconnexion->value('I18N_SIGN_UP_PASS_R', "Repeat password");
$tplconnexion->value('I18N_SIGN_UP_TERMS_MSG', "I accept the");
$tplconnexion->value('I18N_SIGN_UP_TERMS_LINK', "Terms of Use");
$tplconnexion->value('I18N_SIGN_UP_BTN', "Create my account");
// SWITCHER
$tplconnexion->value('I18N_SIGN_UP_HEAD', "Not yet signed up?");
$tplconnexion->value('I18N_SIGN_UP_TEXT', "You don't have an account but want to access our offers?<br />It's just a few clicks away!");
$tplconnexion->value('I18N_SIGN_UP_ACT_BTN', "Create my account");
$tplconnexion->value('I18N_SIGN_IN_HEAD', "Already signed up?");
$tplconnexion->value('I18N_SIGN_IN_TEXT', "You have an account and just want to access your member-space?<br />It's over here!");
$tplconnexion->value('I18N_SIGN_IN_ACT_BTN', "Login");
} else { // default to French :
// SIGN_IN
$tplconnexion->value('I18N_SIGN_IN', "Connexion");
$tplconnexion->value('I18N_SIGN_IN_ID', "Identifiant");
$tplconnexion->value('I18N_SIGN_IN_PASS', "Mot de passe");
$tplconnexion->value('I18N_SIGN_IN_BTN', "Se connecter");
$tplconnexion->value('I18N_SIGN_IN_FORGET', "Mot de passe perdu ?");
// SIGN_UP
$tplconnexion->value('I18N_SIGN_UP', "Inscription");
$tplconnexion->value('I18N_SIGN_UP_ID', "Pseudo");
$tplconnexion->value('I18N_SIGN_UP_ID_MSG', "Minimum 4 caratères de longeur ! Utilisez uniquement des lettres sans accent et des chiffres, les autres caractères (dont '_', '.' et '-') ne sont pas acceptés.");
$tplconnexion->value('I18N_SIGN_UP_FNAME', "Prénom");
$tplconnexion->value('I18N_SIGN_UP_LNAME', "Nom de famille");
$tplconnexion->value('I18N_SIGN_UP_EMAIL', "Email");
$tplconnexion->value('I18N_SIGN_UP_EMAIL_R', "Répéter email");
$tplconnexion->value('I18N_SIGN_UP_PASS', "Mot de passe");
$tplconnexion->value('I18N_SIGN_UP_PASS_MSG', "Minimum 10 caratères !");
$tplconnexion->value('I18N_SIGN_UP_PASS_R', "Répéter passe");
$tplconnexion->value('I18N_SIGN_UP_TERMS_MSG', "J'accepte les");
$tplconnexion->value('I18N_SIGN_UP_TERMS_LINK', "Conditions d'utilisation");
$tplconnexion->value('I18N_SIGN_UP_BTN', "Créer mon compte");
// SWITCHER
$tplconnexion->value('I18N_SIGN_UP_HEAD', "Vous n'êtes pas inscrit ?");
$tplconnexion->value('I18N_SIGN_UP_TEXT', "Pas encore membre mais vous souhaitez accéder à nos offres ?<br />C'est à quelques cliques !");
$tplconnexion->value('I18N_SIGN_UP_ACT_BTN', "Créer mon compte");
$tplconnexion->value('I18N_SIGN_IN_HEAD', "Vous êtes déjà membre ?");
$tplconnexion->value('I18N_SIGN_IN_TEXT', "Vous avez un compte et souhaitez accéder à votre espace ?<br />C'est par ici !");
$tplconnexion->value('I18N_SIGN_IN_ACT_BTN', "Se connecter");
}
// ##########
if (UTILS::isModuleActive('INSCRIPTION')) {
$tplconnexion->bloc('IF_INSCRIPTIONS');
}
// ########## SIGN-IN ##########
if (DATA::isPost('login') && DATA::isPost('password')) {
if (USER::Login(DATA::getPost('login'), DATA::getPost('password'))) {
if (DATA::getPost('login') == "admin") {
header('Location: /classes/onboarding/wizard.html');
exit;
} else {
/*
// HumHub login
hupa::login(DATA::getPost('login'), DATA::getPost('password'));
*/
// redirect
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
}
} else {
UTILS::notification('warning', 'Les champs n\'ont pas été correctement complété', false, true);
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
}
}
// ########## SIGN-UP ##########
if (
DATA::isPost('pseudo') && preg_match('/^[a-z0-9]+$/i', DATA::getPost('pseudo'))
&& DATA::isPost('fname')
&& DATA::isPost('lname')
&& DATA::isPost('email')
&& DATA::isPost('emailCheck')
&& DATA::isPost('password')
&& DATA::isPost('passwordCheck')
&& DATA::isPost('checked')
) {
$rd = '';
$reqAccount = MYSQL::query('SELECT Pseudo, Email FROM accounts WHERE Pseudo=\''.DATA::getPost('pseudo').'\' OR Email=\''.DATA::getPost('email').'\'');
$type = 'warning';
if (DATA::getPost('email') === DATA::getPost('emailCheck')) {
if ((preg_match('/^[a-z0-9]+$/i', DATA::getPost('pseudo', false))) and preg_match('/^[[:alnum:]]([+-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\\.([a-z]{2,4})$/i', DATA::getPost('email'))) {
if (DATA::getPost('password') === DATA::getPost('passwordCheck')) {
if (mysqli_num_rows($reqAccount) < 1) {
$type = 'success';
$messages = '';
$password = hash('sha256', DATA::getPost('password'));
// create 'account'
$data = ['Pseudo' => DATA::getPost('pseudo'), 'Prenom' => DATA::getPost('fname'), 'Nom' => DATA::getPost('lname'), 'Email' => DATA::getPost('email'), 'Password' => $password];
Generique::insert('accounts', 'graphene_bsm', $data);
/*
// check if NextCloud ("Drive") account exists for this member
$reqNCAccount = NCMYSQL::query('SELECT uid FROM oc_accounts WHERE data REGEXP \''.DATA::getPost('email').'\'');
// create NextCloud ("Drive") account if none found
if (mysqli_num_rows($reqNCAccount) < 1) {
// create user because it doens't exist
$newusername = DATA::getPost('pseudo');
$newuserpass = DATA::getPost('password');
nupa::addUser($newusername, $newuserpass);
// update user's other details
$ncuseremail = DATA::getPost('email');
nupa::editUser($newusername, "email", $ncuseremail);
// add user to defaut "member" group
nupa::addUserToGroup($newusername, "member");
}
*/
// create HumHub account
$hhFile = "./classes/humhub";
if(is_dir($hhFile)) {
      hupa::addUser(DATA::getPost('pseudo'), DATA::getPost('email'), DATA::getPost('fname'), DATA::getPost('lname'), "", "", "", "", "", "", "", "", DATA::getPost('password'));
}
// create PHP-Calendar account
$pcFile = "./classes/php-calendar";
if(is_dir($pcFile)) {
      cupa::new(DATA::getPost('pseudo'), DATA::getPost('password'));
}
// create PHProject account
$ppFile = "./classes/phproject";
if(is_dir($ppFile)) {
      pupa::new(DATA::getPost('pseudo'), DATA::getPost('email'), DATA::getPost('fname').' '.DATA::getPost('lname'), DATA::getPost('password'));
}

$userId = MYSQL::selectOneValue('SELECT max(id_client) FROM accounts');
UTILS::addHistory(DATA::getPost('pseudo'), 3, 'Nouvelle inscription de '.DATA::getPost('pseudo'), "/Administration/Membres/{$userId}");
NOTIFICATIONS::add("contacts", "Nouveau membre " . NOTIFICATIONS::createTag($userId), "/Administration/Membres/{$userId}", [], "Membres");
USER::Login(DATA::getPost('pseudo'), DATA::getPost('password'));
$messages = 'Votre inscription s\'est déroulée avec succès.';

// Save accounts to Akaunting
$akFile = "./classes/akaunting";
if(is_dir($akFile)) {
      $filter = ['id_client' => $userId];
    $a = Generique::selectOne('accounts', 'graphene_bsm', $filter);
    $type = strtoupper($a->getCategory()) == 'FOURNISSEUR' ? 'vendor' : 'customer';
    $data = ['company_id' => 1, 'type' => $type, 'currency_code' => 'EUR', 'enabled' => 1, 'name' => html_entity_decode("{$a->getCivilite()} {$a->getNameOrPseudo()}"), 'email' => $a->getEmail(), 'phone' => '', 'address' => html_entity_decode($a->getFullAdresse()), 'reference' => "AC-{$a->getIdClient()}", 'created_at' => $a->getDate_Inscription()];
    Generique::insert('0so_contacts', 'graphene_akaunting', $data);
}

// redirect or display "error" message
if (isset($basket)) {
$rd = $basket->compterArticles() ? '/Store/Cart' : '/Account';
}
$type = 'success';
} else { // un compte porte déjà l'email ou le pseudo
$messages = 'Ce pseudo ou cette adresse email est déjà utilisée...';
}
} else { // mot de passe pas identique
$messages = 'Vos deux mots de passe ne sont pas identiques...';
}
} else {
$messages = 'Le pseudo ou l\'adresse email est incorrect...';
}
} else { // adresse email pas identique
$messages = 'Vos deux adresses email ne sont pas identiques...';
}
$corpMessages = '<div class="notification">';
$corpMessages .= '<div class="alert alert-'.$type.' w-50 d-flex align-items-center justify-content-between" role="alert">';
$corpMessages .= $messages;
$corpMessages .= '</div>';
$corpMessages .= '</div>';
$data = ['html' => $corpMessages, 'rd' => $rd];
exit(json_encode($data));
}
// ########## RE-SIGN-IN ##########
if (DATA::isSession('last_login')) {
$tplconnexion->value('LOGIN', DATA::getSession('last_login'));
}
$PAGE = $tplconnexion->construire('connexion');
$TITRE = 'Connexion';
$DESCRIPTION = 'Connexion';
} else {
if (ManageRights::verifyRights('Dashboard', 'Read', false, false))
header('location: /Administration/Site');
else
header('location: /Account');
exit;
}
