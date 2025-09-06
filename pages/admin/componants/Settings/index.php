<?php
ini_set('display_errors', 0);
ManageRights::verifyRights('Paramètres Graphene', 'Read');
$writePermission = ManageRights::verifyRights('Paramètres Graphene', 'Write', false, false);
if (!$writePermission) {
$DISABLE_FORM = true;
}
$tplSettings = new Template();
$tplSettings->setFile('adminSettings', './_admin/componants/Settings/index.html');
$updateOK = false;
$reqImages = MYSQL::query("SELECT `image` FROM documents WHERE ref = 'DEVIS_IMAGE_DEFAULT' OR ref = 'DEVIS_IMAGE_LEFT' OR ref = 'DEVIS_IMAGE_RIGHT'");
$reqConditions = MYSQL::selectOneValue("SELECT content FROM documents WHERE ref='DEVIS_CONDITIONS'");
$idClient = USER::getPseudo();
$StripePublishableKey = MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'StripePublishableKey'");
$StripeSecretKey = MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'StripeSecretKey'");
$StripeEndpointSecret = MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'StripeEndpointSecret'");
$facebookStatus = FACEBOOK::isEnabled() ? 'Votre page facebook est déjà connectée' : 'Connecter une page Facebook';
$linkedinStatus = LINKEDIN::isEnabled() ? 'Votre page LinkedIn est déjà connectée' : 'Connecter une page LinkedIn';
$reservationOrclickAndCollect = false;
// * Load reservation locations
if (UTILS::isModuleActive('RESERVATION')) {
$tplSettings->bloc('IF_RESERVATION');
$reqLocations = MYSQL::query('SELECT * FROM reservation_location');
function getDays($day) {
$days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
return $days[$day];
}
while ($r = mysqli_fetch_object($reqLocations)) {
$days = explode(',', $r->days);
$openDays = [];
foreach ($days as $d) {
if ($d != '')
$openDays[] = getDays($d);
}
$tplSettings->bloc('IF_RESERVATION.LOCATION', [
'ID' => $r->id,
'NAME' => $r->name,
'ADDRESS' => $r->address,
'H_MIN' => ($r->h_min != "" ? $r->h_min : "00:00"),
'H_MAX' => ($r->h_max != "" ? $r->h_max : "23:59"),
'H_MIN2' => $r->h_min2,
'H_MAX2' => $r->h_max2,
'H_MIN3' => $r->h_min3,
'H_MAX3' => $r->h_max3,
'OPENING_HOURS_1' => ($r->h_min ?: "00:00") . " " . ($r->h_max ?: "23:59"),
'OPENING_HOURS_2' => $r->h_min2 && $r->h_max2 ? "{$r->h_min2} {$r->h_max2}" : "",
'OPENING_HOURS_3' => $r->h_min3 && $r->h_max3 ? "{$r->h_min3} {$r->h_max3}" : "",
'DURATION' => $r->duration,
'NUMBER' => $r->number,
'TEXT_DAYS' => implode(', ', $openDays),
'DAYS' => $r->days,
'CREATED' => UTILS::date($r->created, 'd/m/Y'),
'UPDATED' => UTILS::date($r->updated, 'd/m/Y'),
]);
}
$reservationOrclickAndCollect = true;
}
// * Load click and collects places
if (UTILS::isModuleActive('CLICK&COLLECT')) {
$tplSettings->bloc('IF_CLICKANDCOLLECT');
$req = MYSQL::query('SELECT *, click_and_collect_settings.name as pointName FROM accounts LEFT JOIN click_and_collect_settings ON accounts.id_client = click_and_collect_settings.accounts_id WHERE clickCollect = true');
while ($r = mysqli_fetch_object($req)) {
$pointName = $r->pointName ?? "{$r->Nom} {$r->Prenom}";
$tplSettings->bloc('IF_CLICKANDCOLLECT.PLACE', [
'ID' => $r->id_client,
'NAME' => $pointName,
'ADDRESS' => "{$r->Adresse} {$r->CodePostal} {$r->Ville}",
]);
}
$reservationOrclickAndCollect = true;
}
if ($reservationOrclickAndCollect)
$tplSettings->bloc('IF_RESERVATION_OR_CLICKANDCOLLECT');
// Load additional email accounts for settings
// $reqMailAccounts = MYSQL::query('SELECT * FROM email_accounts_extra');
// while($rma = mysqli_fetch_object($reqMailAccounts)){
// $tplSettings->bloc('MAILLISTE', array(
// 'ID' => $rma->id,
// 'EMAIL' => ($rma->imap_email != "" ? $rma->imap_email : $rma->smtp_email),
// 'IMAP' => ($rma->imap_email != "" ? "✔" : "✘"),
// 'IMAP_EMAIL' => $rma->imap_email,
// 'IMAP_PWD' => $rma->imap_pwd,
// 'IMAP_SRV' => $rma->imap_srv,
// 'IMAP_PORT' => ($rma->imap_port != 0 ? $rma->imap_port : ""),
// 'IMAP_NBR' => $rma->imap_nbr,
// 'SMTP' => ($rma->smtp_email != "" ? "✔" : "✘"),
// 'SMTP_EMAIL' => $rma->smtp_email,
// 'SMTP_PWD' => $rma->smtp_pwd,
// 'SMTP_SRV' => $rma->smtp_srv,
// 'SMTP_PORT' => ($rma->smtp_port != 0 ? $rma->smtp_port : ""),
// 'SMTP_SSL' => $rma->smtp_ssl
// ));
// }
// Get Products Columns
$filter = [];
$productColumns = Generique::select('product_columns_settings', 'graphene_bsm', $filter, 'sort_order ASC');
foreach ($productColumns as $col) {
$tplSettings->bloc('PRODUCTCOLUMNS', [
'ID' => $col->getId(),
'NAME' => $col->getName(),
'TYPE' => $col->getFrenchType(),
'HIDDEN_STORE' => !$col->getHiddenStore() ? '<i class="material-icons color-theme">shopping_cart</i>' : '<i class="material-icons" style="color:#454d55;">remove_shopping_cart</i>'
]);
}
$tplSettings->values([
'HTTP' => UTILS::getFunction('isHttps'),
'SITE_NAME' => UTILS::getFunction('SiteName'),
'TEXT_UNIVERSE' => UTILS::getFunction('textUniverse'),
'ALIAS' => UTILS::getFunction('Alias'),
'SITE_EMAIL' => UTILS::getFunction('WebmasterEmail'),
'SITE_DOMAIN' => UTILS::getFunction('StaticUrl'),
'FACEBOOK' => UTILS::getFunction('urlFacebook'),
'TWITTER' => UTILS::getFunction('urlTwitter'),
'YOUTUBE' => UTILS::getFunction('urlYoutube'),
'LINKEDIN' => UTILS::getFunction('urlLinkedin'),
'INSTAGRAM' => UTILS::getFunction('urlInstagram'),
'SITE_ADDRESS' => UTILS::getFunction('address'),
'SITE_PHONE' => UTILS::getFunction('phone'),
'API_GOOGLE_MAP' => UTILS::getFunction('API_KEYS_GOOGLE_MAP'),
'DEVIS_IMAGE_DEFAULT' => mysqli_fetch_row($reqImages)[0],
'DEVIS_IMAGE_LEFT' => mysqli_fetch_row($reqImages)[0],
'DEVIS_IMAGE_RIGHT' => mysqli_fetch_row($reqImages)[0],
'DEVIS_CONDITIONS' => $reqConditions,
'EMAILIMAP' => UTILS::getFunction('emailIMAP'),
'PASSWORDIMAP' => UTILS::getFunction('passwordIMAP'),
'IMAP' => UTILS::getFunction('IMAP'),
'PORTIMAP' => UTILS::getFunction('portMessaging'),
'NBR_MESSAGEIMAP' => UTILS::getFunction('paginationEmail'),
'EMAILSMTP' => UTILS::getFunction('usernameSMTP'),
'PASSWORDSMTP' => UTILS::getFunction('passwordSMTP'),
'SMTP' => UTILS::getFunction('urlSMTP'),
'PORTSMTP' => UTILS::getFunction('portSMTP'),
'SSLSMTP' => UTILS::getFunction('sslSMTP'),
'PRODUCT_IMAGES_DEVIS' => false == UTILS::getFunction('productImagesDevis') ? '' : 'checked',
'STRIPEPUBLISHABLEKEY' => $StripePublishableKey,
'STRIPESECRETKEY' => $StripeSecretKey,
'STRIPEENDPOINTSECRET' => $StripeEndpointSecret,
'FACEBOOK_STATUS' => $facebookStatus,
'LINKEDIN_STATUS' => $linkedinStatus,
'TVA_NUMBER' => UTILS::getFunction('TvaNumber'),
'STOCK_0_MESSAGE' => MYSQL::selectOneValue("SELECT content FROM documents WHERE REF = 'STOCK_0_MESSAGE'"),
'TEXT_TARIF_STORE' => UTILS::getFunction('textTarifStore'),
'MESSAGE_CLICKCOLLECT' => UTILS::getFunction('messageClickCollect'),
'DESCRIPTION_IN_FACTURE' => false == UTILS::getFunction('descriptionInFacture') ? '' : 'checked',
'FAMILY_IN_FACTURE' => false == UTILS::getFunction('familyInFacture') ? '' : 'checked',
'PRODUCTID_IN_FACTURE' => false == UTILS::getFunction('productIdInFacture') ? '' : 'checked',
'ENTREPRISE_NAME_IN_FACTURE' => false == UTILS::getFunction('entrepriseNameInFacture') ? '' : 'checked',
'BORDER_REF_IN_FACTURE' => false == UTILS::getFunction('borderRefInFacture') ? '' : 'checked',
'REFERENCE_IN_STORE' => false == UTILS::getFunction('referenceInStore') ? '' : 'checked',
'TTC_IN_STORE' => false == UTILS::getFunction('TTCInStore') ? '' : 'checked',
'COMMENT_IN_STORE' => false == UTILS::getFunction('CommentInStore') ? '' : 'checked',
'LOGIN_IMAGE' => UTILS::getFunction('loginImage'),
'LOGIN_TEXT' => UTILS::getFunction('loginText'),
'TEXT_CART' => MYSQL::selectOneValue('SELECT content FROM documents WHERE ref = "TEXT_CART"'),
'TEXT_PAIEMENT' => MYSQL::selectOneValue('SELECT content FROM documents WHERE ref = "TEXT_PAIEMENT"'),
'TEXT_REMERCIEMENT' => UTILS::getFunction("messageStore"),
'TEXT_VALIDATE_PAIEMENT' => MYSQL::selectOneValue('SELECT content FROM documents WHERE ref = "TEXT_VALIDATE_PAIEMENT"'),
'CACHE_LOGO' => filemtime('./themes/assets/images/logo.png'),
'CACHE_FAVICON' => filemtime('./favicon.ico'),
'ANALYTICS_TRACKING' => UTILS::getFunction('analyticsTracking'),
'YOURLS_NAME' => MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'yourls.id'"),
'YOURLS_PASS' => MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'yourls.pass'"),
'RESUME_TVA' => false == UTILS::getFunction('resumeTVA') ? '' : 'checked',
'STORE_NAME' => MYSQL::selectOneValue("SELECT ModuleTitle FROM modulespages WHERE NameModule = 'STORE'"),
'NTFY' => UTILS::getFunction('ntfy')
]);
//* Load Fonts
$fonts = ["Arial", "Arial Black", "Courier New", "Georgia", "Helvetica", "Impact", "Lato", "Palatino", "Roboto", "Tahoma", "Times New Roman", "Trebuchet MS", "Verdana", ...$nameFonts ];
sort($fonts);
$fontTitle = UTILS::getFunction('fontTitle') ?? 'Lato';
$fontText = UTILS::getFunction('fontText') ?? 'Roboto';
$fontMyGenghis = UTILS::getFunction('fontMyGenghis') ?? 'Roboto';
foreach ($fonts as $font) {
$tplSettings->bloc('FONT_LIST', [
'NAME' => $font,
'TITLE_SELECTED' => $font == $fontTitle ? 'selected' : '',
'TEXT_SELECTED' => $font == $fontText ? 'selected' : '',
'MYGENGHIS_SELECTED' => $font == $fontMyGenghis ? 'selected' : '',
]);
}
if ($writePermission && DATA::getMethod() == 'POST') {
// Manage Product Columns
ManageProductColumns::load();
// Matomo tracking code
if (DATA::isPost('analyticstracking')) {
MYSQL::query('UPDATE functions SET analyticsTracking = \''.DATA::getPost('analyticsTracking').'\'');
UTILS::notification('success', 'Configuration du code de tracabilité "Analytics" terminée', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
// URL shortener
if (DATA::isPost('yourlsconf')) {
MYSQL::query("UPDATE api_token SET token = '".DATA::getPost('yourlsId')."' WHERE `service` = 'yourls.id'");
MYSQL::query("UPDATE api_token SET token = '".DATA::getPost('yourlsPwd')."' WHERE `service` = 'yourls.pass'");
UTILS::notification('success', 'Configuration des paramètres du générateur de liens-courts terminée', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
// main Email account settings
if ((DATA::isPost('emailimap') && DATA::isPost('passwordimap') && DATA::isPost('imap') && DATA::isPost('portimap') && DATA::isPost('nbr')) || (DATA::isPost('emailsmtp') && DATA::isPost('passwordsmtp') && DATA::isPost('smtp') && DATA::isPost('portsmtp') && DATA::isPost('ssl'))) {
MYSQL::query(
'UPDATE functions SET
emailIMAP = \''.DATA::getPost('emailimap').'\',
passwordIMAP = \''.DATA::getPost('passwordimap').'\',
IMAP = \''.DATA::getPost('imap').'\',
portMessaging = \''.DATA::getPost('portimap').'\',
paginationEmail = \''.DATA::getPost('nbr').'\',
usernameSMTP = \''.DATA::getPost('emailsmtp').'\',
passwordSMTP = \''.DATA::getPost('passwordsmtp').'\',
urlSMTP = \''.DATA::getPost('smtp').'\',
portSMTP = \''.DATA::getPost('portsmtp').'\',
sslSMTP = \''.DATA::getPost('ssl').'\''
);
UTILS::notification('success', 'Configuration de la boîte au lettres principale terminée', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
/* additional email account settings */
// ADD
if (DATA::isPost('emailAddImapEmail') || DATA::isPost('emailAddSmtpEmail')) {
MYSQL::query('INSERT INTO email_accounts_extra (imap_email, imap_pwd, imap_srv, imap_port, imap_nbr, smtp_email, smtp_pwd, smtp_srv, smtp_port, smtp_ssl) VALUES (
\''.DATA::getPost('emailAddImapEmail').'\',
\''.DATA::getPost('emailAddImapPwd').'\',
\''.DATA::getPost('emailAddImapSrv').'\',
\''.(DATA::getPost('emailAddImapPort') != "" ? DATA::getPost('emailAddImapPort') : 0).'\',
\''.(DATA::getPost('emailAddImapNbr') != "" ? DATA::getPost('emailAddImapNbr') : 5).'\',
\''.DATA::getPost('emailAddSmtpEmail').'\',
\''.DATA::getPost('emailAddSmtpPwd').'\',
\''.DATA::getPost('emailAddSmtpSrv').'\',
\''.(DATA::getPost('emailAddSmtpPort') != "" ? DATA::getPost('emailAddSmtpPort') : 0).'\',
\''.DATA::getPost('emailAddSmtpSsl').'\')'
);
UTILS::notification('success', "Configuration d'une boîte au lettres additionnelle terminée", false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
// EDIT
if (DATA::isPost('emailImapEmail') || DATA::isPost('emailImapPwd') || DATA::isPost('emailImapSrv') || DATA::isPost('emailImapPort') || DATA::isPost('emailImapNbr') || DATA::isPost('emailSmtpEmail') || DATA::isPost('emailSmtpPwd') || DATA::isPost('emailSmtpSrv') || DATA::isPost('emailSmtpPort') || DATA::isPost('emailSmtpSsl')) {
MYSQL::query('UPDATE email_accounts_extra SET
imap_email = \''.DATA::getPost('emailImapEmail').'\',
imap_pwd = \''.DATA::getPost('emailImapPwd').'\',
imap_srv = \''.DATA::getPost('emailImapSrv').'\',
imap_port = \''.(DATA::getPost('emailImapPort') != "" ? DATA::getPost('emailImapPort') : 0).'\',
imap_nbr = \''.(DATA::getPost('emailImapNbr') != "" ? DATA::getPost('emailImapNbr') : 5).'\',
smtp_email = \''.DATA::getPost('emailSmtpEmail').'\',
smtp_pwd = \''.DATA::getPost('emailSmtpPwd').'\',
smtp_srv = \''.DATA::getPost('emailSmtpSrv').'\',
smtp_port = \''.(DATA::getPost('emailSmtpPort') != "" ? DATA::getPost('emailSmtpPort') : 0).'\',
smtp_ssl = \''.DATA::getPost('emailSmtpSsl').'\'
WHERE id = \''.DATA::getPost('emailEditId').'\''
);
UTILS::notification('success', "Modification de la configuration d'une boîte au lettres additionnelle terminée", false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
// DELETE
if (DATA::isPost('deleteExtraMailAccount')) {
UTILS::Alert('danger', 'Suppression d\'une configuration de boîte mail additionnelle. Voulez-vous vraiment effectuer cette suppression ?', 'Attention, l\'action est irréversible', $_SERVER['REQUEST_URI'], 'deleteExtraMailOK', DATA::getPost('deleteExtraMailAccount'));
}
if (DATA::isPost('deleteExtraMailOK')) {
MYSQL::query('DELETE FROM email_accounts_extra WHERE id = \''.DATA::getPost('deleteExtraMailOK').'\'');
UTILS::notification('success', 'Configuration de la boîte au lettres additionnelle supprimée', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
/* hierachy links */
// ADD
if (DATA::isPost('hierachyAddSuperior') || DATA::isPost('hierachyAddPosition')) {
MYSQL::query('INSERT INTO hierachy (superior, position) VALUES (
\''.DATA::getPost('hierachyAddSuperior').'\',
\''.DATA::getPost('hierachyAddPosition').'\')'
);
UTILS::notification('success', "Ajout d'une nouvelle liaison hiérachique terminée", false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
// DELETE
if (DATA::isPost('deleteHierachyLink')) {
UTILS::Alert('danger', 'Suppression d\'une liaison hiérachique. Voulez-vous vraiment effectuer cette suppression ?', 'Attention, l\'action est irréversible', $_SERVER['REQUEST_URI'], 'deleteHierachyLinkOK', DATA::getPost('deleteHierachyLink'));
}
if (DATA::isPost('deleteHierachyLinkOK')) {
MYSQL::query('DELETE FROM hierachy WHERE id = \''.DATA::getPost('deleteHierachyLinkOK').'\'');
UTILS::notification('success', 'Liaison hiérachique supprimée', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
// add log
exit;
}
// * Reservation Location settings */
// ADD
if (DATA::isPost(['resLocAddName', 'resLocAddDuration', 'resLocAddNumber'])) {
$data = ['name' => DATA::getPost('resLocAddName'),
'address' => DATA::getPost('resLocAddAddress'),
'days' => DATA::getPost('resLocAddDays'),
'h_min' => DATA::getPost('resLocAddHmin'),
'h_max' => DATA::getPost('resLocAddHmax'),
'h_min2' => DATA::getPost('resLocAddHmin2'),
'h_max2' => DATA::getPost('resLocAddHmax2'),
'h_min3' => DATA::getPost('resLocAddHmin3'),
'h_max3' => DATA::getPost('resLocAddHmax3'),
'duration' => DATA::getPost('resLocAddDuration'),
'number' => DATA::getPost('resLocAddNumber')
];
Generique::insert('reservation_location', 'graphene_bsm', $data);
UTILS::addHistory(USER::getPseudo(), 52, "Lieu de Réservation « ".DATA::getPost('resLocAddName')." » ajouté");
UTILS::notification('success', "Lieu de Réservation ajouté");
}
// EDIT
if (DATA::isPost(['resLocEditName', 'resLocEditDuration', 'resLocEditNumber'])) {
$filter = ['id' => DATA::getPost('resLocEditId')];
$data = ['name' => DATA::getPost('resLocEditName'),
'address' => DATA::getPost('resLocEditAddress'),
'days' => DATA::getPost('resLocEditDays'),
'h_min' => DATA::getPost('resLocEditHmin'),
'h_max' => DATA::getPost('resLocEditHmax'),
'h_min2' => DATA::getPost('resLocEditHmin2'),
'h_max2' => DATA::getPost('resLocEditHmax2'),
'h_min3' => DATA::getPost('resLocEditHmin3'),
'h_max3' => DATA::getPost('resLocEditHmax3'),
'duration' => DATA::getPost('resLocEditDuration'),
'number' => DATA::getPost('resLocEditNumber')
];
Generique::update('reservation_location', 'graphene_bsm', $filter, $data);
UTILS::addHistory(USER::getPseudo(), 52, "Lieu de Réservation « ".DATA::getPost('resLocEditName')." » modifié");
UTILS::notification('success', "Lieu de Réservation modifié");
}
// DELETE
if (DATA::isPost('deleteResLoc')) {
UTILS::Alert('danger', 'Suppression d\'un lieu de Réservation. Voulez-vous vraiment effectuer cette suppression ?', 'Attention, l\'action est irréversible', $_SERVER['REQUEST_URI'], 'deleteResLocOK', DATA::getPost('deleteResLoc'));
}
if (DATA::isPost('deleteResLocOK')) {
$filter = ['id' => DATA::getPost('deleteResLocOK')];
Generique::delete('reservation_location', 'graphene_bsm', $filter);
UTILS::addHistory(USER::getPseudo(), 52, "Lieu de Réservation supprimé");
UTILS::notification('success', 'Lieu de Réservation supprimé');
}
// DELETE
if (DATA::isPost('deleteClickLoc')) {
UTILS::Alert('danger', 'Suppression d\'un de Click&Collect. Voulez-vous vraiment effectuer cette suppression ?', 'Attention, l\'action est irréversible', $_SERVER['REQUEST_URI'], 'deleteClickLocOK', DATA::getPost('deleteClickLoc'));
}
if (DATA::isPost('deleteClickLocOK')) {
Generique::update('accounts', 'graphene_bsm', ['id_client' => DATA::getPost('deleteClickLocOK')], ['clickCollect' => 0]);
UTILS::addHistory(USER::getPseudo(), 53, "Lieu de Click&Collect supprimé");
UTILS::notification('success', 'Lieu de Click&Collect supprimé');
}
if (DATA::isPost('global')) {
$handle = new upload($_FILES['favicon'], 'fr_FR');
if ($handle->uploaded) {
unlink('./favicon.ico');
$handle->allowed = ['image/*'];
$handle->file_new_name_body = 'favicon';
$handle->file_new_name_ext = 'ico';
$handle->image_resize = true;
$handle->image_x = 16;
$handle->image_y = 16;
$handle->process('./');
}
$handle2 = new upload($_FILES['logo'], 'fr_FR');
if ($handle2->uploaded) {
unlink('./themes/assets/images/logo.png');
$handle2->allowed = ['image/*'];
$handle2->file_new_name_body = 'logo';
$handle2->file_new_name_ext = 'png';
$handle2->image_resize = false;
$handle2->process('./themes/assets/images');
}
$data = [
'SiteName' => DATA::getPost('siteName'),
'textUniverse' => DATA::getPost('textUniverse'),
'Alias' => DATA::getPost('alias'),
'WebmasterEmail' => DATA::getPost('siteEmail'),
'StaticUrl' => DATA::getPost('siteDomain'),
'UrlFacebook' => DATA::getPost('facebook'),
'UrlTwitter' => DATA::getPost('twitter'),
'UrlYoutube' => DATA::getPost('youtube'),
'UrlLinkedin' => DATA::getPost('linkedin'),
'UrlInstagram' => DATA::getPost('instagram'),
'phone' => DATA::getPost('phone'),
'address' => DATA::getPost('address'),
'API_KEYS_GOOGLE_MAP' => DATA::getPost('apiKeyGoogleMap'),
'TvaNumber' => DATA::getPost('TvaNumber'),
'fontTitle' => DATA::getPost('fontTitle'),
'fontText' => DATA::getPost('fontText'),
'fontMyGenghis' => DATA::getPost('fontMyGenghis'),
'isHttps' => DATA::getPost('ssl_domain'),
'loginText' => DATA::getPost('loginText'),
];
if ($image = IMAGE::upload($_FILES['loginImage'], 'uploads/documents', 'loginImage')) {
$data['loginImage'] = "..{$image}";
}
if ($_FILES['loadFont']) {
$handle = new upload($_FILES['loadFont'], 'fR_FR');
if ($handle->uploaded) {
$handle->allowed = ['font/*', 'application/vnd.ms-opentype'];
$handle->process("uploads/fonts");
}
}
Generique::update('functions', 'graphene_bsm', [], $data);
EntityApiToken::updateToken('StripePublishableKey', DATA::getPost('StripePublishableKey'));
EntityApiToken::updateToken('StripeSecretKey', DATA::getPost('StripeSecretKey'));
EntityApiToken::updateToken('StripeEndpointSecret', DATA::getPost('StripeEndpointSecret'));
$updateOK = true;
}
}
if ('https' === UTILS::getFunction('isHttps')) {
$tplSettings->value('HTTPS_ACTIVE', 'selected');
} else {
$tplSettings->value('HTTPS_NOT_ACTIVE', 'selected');
}
$moduleRequest = MYSQL::query('SELECT * FROM modulespages WHERE visible = 1');
if (mysqli_num_rows($moduleRequest) > 0) {
while ($r = mysqli_fetch_object($moduleRequest)) {
if ($r->NameModule != 'LOGIN') {
$tplSettings->bloc('MODULE', [
'NAME' => $r->NameModule,
'PAGE_NAME' => $r->NamePage,
'MODULETITLE' => $r->ModuleTitle,
'MODULEDESCRIPTION' => $r->ModuleDescription,
'URL' => $r->Url,
'SELECTED' => 'ON' === $r->ModuleActive ? "selected" : ""
]);
if ('ON' === $r->ModuleActive) {
$tplSettings->bloc('MODULE.ACTIVE');
} else {
$tplSettings->bloc('MODULE.INACTIVE');
}
if ($writePermission) {
if (DATA::isPost($r->NameModule)) {
if ('ON' != $r->ModuleActive && 'ON' == DATA::getPost($r->NameModule)) {
MYSQL::query('UPDATE modulespages SET ModuleActive = "ON" WHERE NameModule = \''.$r->NameModule.'\'');
} elseif ('OFF' != $r->ModuleActive && 'OFF' == DATA::getPost($r->NameModule)) {
MYSQL::query('UPDATE modulespages SET ModuleActive = "OFF" WHERE NameModule = \''.$r->NameModule.'\'');
}
$updateOK = true;
}
}
}
}
if ($updateOK) {
UTILS::notification('success', 'Paramètres sauvegardés avec succès.', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
exit;
}
}
$inputCustom = MYSQL::QUERY('SELECT * FROM user_custom_field ORDER BY id ASC');
if (mysqli_num_rows($inputCustom) > 0) {
$tplSettings->bloc('IF_IS_CUSTOM_INPUT');
$i = 1;
while ($inputResult = mysqli_fetch_object($inputCustom)) {
$tplSettings->bloc('IF_IS_CUSTOM_INPUT.LISTE_INPUT', [
'NOM' => $inputResult->nameInput,
'VALEUR' => $inputResult->name,
'PLACEHOLDER' => $inputResult->placeholder,
'TYPE' => $inputResult->type,
'REQUIRED' => (($inputResult->required) && 1 == $inputResult->required ? 'Requis' : 'Non requis'),
'ID' => $i,
'ID_CHAMP' => $inputResult->id,
//'PAGE' => $inputResult->modulesPages
]);
++$i;
}
}
// * Hierachy
$hdata = "";
$req = MYSQL::query('SELECT * FROM hierachy ORDER BY id');
while($r = mysqli_fetch_object($req)){
$tplSettings->bloc('HIERACHY', array(
'SUPERIOR' => $r->superior,
'POSITION' => $r->position,
'ID' => $r->id
));
$hdata .= "['".$r->superior."', '".$r->position."'], ";
}
$tplSettings->values(['HIERACHYDATA' => $hdata]);
// * OrgaData
$orgdata = "";
$req = MYSQL::query('SELECT * FROM personnel ORDER BY id');
while($r = mysqli_fetch_object($req)){
$orgdata .= "{ id: '".$r->position."', title: '".$r->poste."', name: '".$r->prenom." ".$r->nom."', image: '".UTILS::GetIdAvatar($r->id_client)."' }, ";
}
$tplSettings->values(['ORGADATA' => $orgdata]);
// * WebPage Setings
$defaultPage = UTILS::getFunction('defaultPage');
$req = MYSQL::query('SELECT * FROM user_custom_page ORDER BY sort_order');
if(mysqli_num_rows($req) > 0){
$i = 0;
$tplSettings->bloc('IF_IS_PAGE');
while($r = mysqli_fetch_object($req)){
$tplSettings->bloc('IF_IS_PAGE.LISTE', array(
'PAGE_NAME' => $r->PageName,
'URL' => $r->url,
'IMAGE' => $r->image ? "src='{$r->image}'" : '',
'ORDRE' => $r->sort_order,
'DATE' => date('d/m/Y', strtotime($r->date)),
'GET_SITE_INITIAL' => UTILS::getInitialesSiteName(),
'ID' => $r->id,
'TEMPLATE_ID' => $r->template,
'SELECTED' => $defaultPage == '/'.$r->url ? "selected" : ""
));
$i++;
}
}
$pages = [
array('URL' => 'Connexion', 'NAME' => 'Connexion'),
// array('URL' => 'Store', 'NAME' => 'Store'),
array('URL' => 'Articles', 'NAME' => 'Articles'),
array('URL' => 'Videos', 'NAME' => 'Vidéos')];
foreach ($pages as $p) {
$tplSettings->bloc('PAGES', array(
'URL' => '/'.$p['URL'],
'PAGE_NAME' => $p['NAME'],
'SELECTED' => $defaultPage == '/'.$p['URL'] ? "selected" : ""
));
}
$contactMode = MYSQL::selectOneValue("SELECT Texte FROM modulespages WHERE NameModule = 'CONTACT'");
if ($contactMode == "1")
$tplSettings->value('CONTACT_MODE_1', 'checked');
else if ($contactMode == "2")
$tplSettings->value('CONTACT_MODE_2', 'checked');
else
$tplSettings->value('CONTACT_MODE_3', 'checked');
$store = MYSQL::selectOneValue("SELECT `image` FROM modulespages WHERE NameModule = 'STORE'");
if ($store)
$tplSettings->bloc('IF_IMG_STORE');
$articles = MYSQL::selectOneValue("SELECT `image` FROM modulespages WHERE NameModule = 'ARTICLES'");
if ($articles)
$tplSettings->bloc('IF_IMG_ARTICLES');
$videos = MYSQL::selectOneValue("SELECT `image` FROM modulespages WHERE NameModule = 'VIDEOS'");
if ($videos)
$tplSettings->bloc('IF_IMG_VIDEOS');
$contact = MYSQL::selectOneValue("SELECT `image` FROM modulespages WHERE NameModule = 'CONTACT'");
if ($contact)
$tplSettings->bloc('IF_IMG_CONTACT');
$img_espace_membre = MYSQL::selectOneValue("SELECT image FROM documents WHERE ref = 'ESPACE_MEMBRE'");
if ($img_espace_membre)
$tplSettings->bloc('IF_IMG_ESPACE_MEMBRE');
$tplSettings->values(array(
'IMG_STORE' => $store,
'IMG_ARTICLES' => $articles,
'IMG_VIDEOS' => $videos,
'IMG_CONTACT' => $contact,
'IMG_ESPACE_MEMBRE' => $img_espace_membre,
'DISPLAY_WEB_MENU' => UTILS::getFunction('displayWebMenu') ? 'checked' : '',
'DISPLAY_ADDRESS' => UTILS::getFunction('displayAddress') ? 'checked' : '',
'DISPLAY_EMAIL' => UTILS::getFunction('displayEmail') ? 'checked' : '',
'WEB_LOGO_LINK' => UTILS::getFunction('webLogoLink'),
'SITE_DESCRIPTION' => MYSQL::selectOneValue("SELECT content FROM documents WHERE ref = 'SITE_DESCRIPTION'")
));
if ($writePermission) {
if (DATA::isPost('tarif')) {
if ($devis_image_default = IMAGE::upload($_FILES['devis_image_default'], "themes/assets/images/billing")) {
MYSQL::query("UPDATE documents SET `image` = '{$devis_image_default}' WHERE ref='DEVIS_IMAGE_DEFAULT'");
}
if ($devis_image_left = IMAGE::upload($_FILES['devis_image_left'], "themes/assets/images/billing")) {
MYSQL::query("UPDATE documents SET `image` = '{$devis_image_left}' WHERE ref='DEVIS_IMAGE_LEFT'");
}
if ($devis_image_right = IMAGE::upload($_FILES['devis_image_right'], "themes/assets/images/billing")) {
MYSQL::query("UPDATE documents SET `image` = '{$devis_image_right}' WHERE ref='DEVIS_IMAGE_RIGHT'");
}
$conditions = DATA::getPost('conditions');
$productImagesDevis = 'on' == DATA::getPost('productImagesDevis') ? 'true' : 'false';
$descriptionInFacture = 'on' == DATA::getPost('descriptionInFacture') ? 'true' : 'false';
$familyInFacture = 'on' == DATA::getPost('familyInFacture') ? 'true' : 'false';
$productIdInFacture = 'on' == DATA::getPost('productIdInFacture') ? 'true' : 'false';
$entrepriseNameInFacture = 'on' == DATA::getPost('entrepriseNameInFacture') ? 'true' : 'false';
$borderRefInFacture = 'on' == DATA::getPost('borderRefInFacture') ? 'true' : 'false';
$resumeTVA = 'on' == DATA::getPost('resumeTVA') ? 'true' : 'false';
MYSQL::query("UPDATE functions SET productImagesDevis = {$productImagesDevis}, descriptionInFacture = {$descriptionInFacture}, familyInFacture = {$familyInFacture}, productIdInFacture = {$productIdInFacture}, entrepriseNameInFacture = {$entrepriseNameInFacture}, borderRefInFacture = {$borderRefInFacture}, resumeTVA = {$resumeTVA}");
MYSQL::query("UPDATE documents SET content = '{$conditions}' WHERE ref='DEVIS_CONDITIONS'");
UTILS::notification('success', 'Paramètres de documents sauvegardés avec succès.', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
exit;
}
if (DATA::isPost('store')) {
$referenceInStore = 'on' == DATA::getPost('referenceInStore') ? 'true' : 'false';
$TTCInStore = 'on' == DATA::getPost('TTCInStore') ? 'true' : 'false';
$CommentInStore = 'on' == DATA::getPost('CommentInStore') ? 'true' : 'false';
$textTarifStore = DATA::getPost('textTarifStore');
MYSQL::query("UPDATE functions SET referenceInStore = {$referenceInStore}, TTCInStore = {$TTCInStore}, CommentInStore = {$CommentInStore}, textTarifStore = '{$textTarifStore}'");
$stock_zero_message = DATA::getPost('stock_0_message');
MYSQL::query("UPDATE documents SET content = '{$stock_zero_message}' WHERE ref='STOCK_0_MESSAGE'");
$text_cart = DATA::getPost('text_cart');
MYSQL::query("UPDATE documents SET content = '{$text_cart}' WHERE ref='TEXT_CART'");
$text_paiement = DATA::getPost('text_paiement');
MYSQL::query("UPDATE documents SET content = '{$text_paiement}' WHERE ref='TEXT_PAIEMENT'");
$text_remerciement = DATA::getPost('text_remerciement');
MYSQL::query("UPDATE functions SET messageStore = '{$text_remerciement}'");
$text_validate_paiement = DATA::getPost('text_validate_paiement');
MYSQL::query("UPDATE documents SET content = '{$text_validate_paiement}' WHERE ref='TEXT_VALIDATE_PAIEMENT'");
$storeName = DATA::getPost('storeName');
MYSQL::query("UPDATE modulespages SET ModuleTitle = '{$storeName}' WHERE NameModule = 'STORE'");
UTILS::notification('success', 'Paramètres de store sauvegardés avec succès.', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
exit;
}
// FACEBOOK Auth
if (DATA::isPost('facebookToken')) {
if (!FACEBOOK::getLongLiveUserToken(DATA::getPost('facebookToken'))) {
UTILS::notification('danger', 'Erreur de connexion avec le compte Facebook');
}
$pages = FACEBOOK::getListPages();
if (!$pages) {
UTILS::notification('danger', "Aucune page n'est associé a votre compte ou vous n'avez pas autorisé l'accès à vos pages.");
}
UTILS::Select('secondary', 'Sélection de la page', 'Sur quelle page Facebook voulez vous poster les messages ?', $_SERVER['REQUEST_URI'], 'facebookPage', $pages);
exit;
}
if (DATA::isPost('facebookPage')) {
$page = FACEBOOK::getPageToken(DATA::getPost('facebookPage'));
UTILS::notification('success', "La page « {$page->name} » est maintenant votre page de publication");
}
if (DATA::isPost('updateClickAndCollect')) {
Generique::update('functions', 'graphene_bsm', [], ['messageClickCollect' => DATA::getPost('messageClickCollect')]);
UTILS::notification('success', "Message de click&Collect mise à jour");
}
if (DATA::isPost('webSettings')) {
$filter = [];
$data = [
'defaultPage' => DATA::getPost('defaultPage'),
'displayAddress' => DATA::getPost('display_address') == 'on' ? '1' : '0',
'displayEmail' => DATA::getPost('display_email') == 'on' ? '1' : '0',
'displayWebMenu' => DATA::getPost('display_web_menu') == 'on' ? '1' : '0',
'webLogoLink' => DATA::getPost('web_logo_link')
];
Generique::update('functions', 'graphene_bsm', $filter, $data);
if ($image_store = IMAGE::upload($_FILES['image_store'], 'site/images', "", 1366)) {
MYSQL::query("UPDATE modulespages SET `image` = '{$image_store}' WHERE NameModule = 'STORE'");
}
if ($image_articles = IMAGE::upload($_FILES['image_articles'], 'site/images', "", 1366)) {
MYSQL::query("UPDATE modulespages SET `image` = '{$image_articles}' WHERE NameModule = 'ARTICLES'");
}
if ($image_videos = IMAGE::upload($_FILES['image_videos'], 'site/images', "", 1366)) {
MYSQL::query("UPDATE modulespages SET `image` = '{$image_videos}' WHERE NameModule = 'VIDEOS'");
}
if ($image_contact = IMAGE::upload($_FILES['image_contact'], 'site/images', "", 1366)) {
MYSQL::query("UPDATE modulespages SET `image` = '{$image_contact}' WHERE NameModule = 'CONTACT'");
MYSQL::query("UPDATE `documents` SET `image` = '{$image_contact}' WHERE `ref` = 'CGU'");
}
if ($image_espace_membre = IMAGE::upload($_FILES['image_espace_membre'], 'site/images', "", 1366)) {
if (!isset($img_espace_membre) && !MYSQL::selectOneValue("SELECT id FROM documents WHERE ref = 'ESPACE_MEMBRE'")) {
MYSQL::query("INSERT INTO documents SET ref = 'ESPACE_MEMBRE', `image` = '{$image_espace_membre}'");
} else {
MYSQL::query("UPDATE documents SET `image` = '{$image_espace_membre}' WHERE ref = 'ESPACE_MEMBRE'");
}
}
$data = ['Texte' => DATA::getPost('contact_mode')];
$filter = ['NameModule' => 'Contact'];
Generique::update('modulespages', 'graphene_bsm', $filter, $data);
$description = DATA::getPost('site_description');
MYSQL::query("UPDATE documents SET content = '{$description}' WHERE ref = 'SITE_DESCRIPTION'");
UTILS::notification('success', 'Paramètres modifié avec succès', false, true);
header("Location: {$_SERVER['REQUEST_URI']}");
exit;
} else if (DATA::isPost('webDeleteImage')) {
$ref = DATA::getPost('webDeleteImage');
if ($ref == 'ESPACE_MEMBRE') {
MYSQL::query("UPDATE documents SET `image` = NULL WHERE ref = '{$ref}'");
} else {
MYSQL::query("UPDATE modulespages SET `image` = NULL WHERE NameModule = '{$ref}'");
}
exit;
}
}
// LINKEDIN AUTH
if (preg_match('/^\\/Administration\\/Settings\\?code=([0-9a-zA-Z\\_-]+)&state=([0-9a-zA-Z\\_]+)$/', $_SERVER['REQUEST_URI'])) {
$parts = parse_url($_SERVER['REQUEST_URI']);
parse_str($parts['query'], $query);
LINKEDIN::saveUserToken($query['code'], $query['state']);
$pages = LINKEDIN::getListPages();
if (!$pages) {
UTILS::notification('danger', "Aucune page n'est associé a votre compte ou vous n'avez pas autorisé l'accès à vos pages.", true, true);
exit;
}
UTILS::Select('secondary', 'Sélection de la page', 'Sur quelle page Linkedin voulez vous poster les messages ?', '/Administration/Settings', 'linkedinPage', $pages);
header('Location: /Administration/Settings');
exit;
}
if (DATA::isPost('linkedinPage')) {
LINKEDIN::savePageURN(DATA::getPost('linkedinPage'));
UTILS::notification('success', 'Votre page LinkedIn est maintenant votre page de publication', true, true);
exit;
}
if (DATA::isPost('akauntingSync')) {
    $file = "./classes/akaunting";
    if(is_dir($file)) {
        // Sync contacts
        UTILS::notification('info', 'Synchronisation des contacts...');
        // ---
        Generique::delete('0so_contacts', 'graphene_akaunting', []);
        $contacts = Generique::select('contacts', 'graphene_bsm');
        foreach ($contacts as $c) {
        $type = strtoupper($c->getCategory()) == 'FOURNISSEUR' ? 'vendor' : 'customer';
        $data = ['company_id' => 1, 'type' => $type, 'currency_code' => 'EUR', 'enabled' => 1, 'name' => html_entity_decode($c->getFullName()), 'email' => $c->getCouriel(), 'phone' => $c->getPhone(), 'address' => html_entity_decode($c->getFullAdresse()), 'reference' => "CO-{$c->getId()}", 'created_at' => $c->getCreationDate()];
        Generique::insert('0so_contacts', 'graphene_akaunting', $data);
        }
        UTILS::notification('info', 'Synchronisation des comptes membres...');
        // ---
        $accounts = Generique::select('accounts', 'graphene_bsm');
        foreach ($accounts as $a) {
        $type = strtoupper($a->getCategory()) == 'FOURNISSEUR' ? 'vendor' : 'customer';
        $data = ['company_id' => 1, 'type' => $type, 'currency_code' => 'EUR', 'enabled' => 1, 'name' => html_entity_decode("{$a->getCivilite()} {$a->getNameOrPseudo()}"), 'email' => $a->getEmail(), 'phone' => '', 'address' => html_entity_decode($a->getFullAdresse()), 'reference' => "AC-{$a->getIdClient()}", 'created_at' => $a->getDate_Inscription()];
        Generique::insert('0so_contacts', 'graphene_akaunting', $data);
        }
        UTILS::notification('info', 'Synchronisation des entreprises...');
        // ---
        $tiers = Generique::select('tiers', 'graphene_erp');
        foreach ($tiers as $t) {
        $type = strtoupper($t->getCategory()) == 'FOURNISSEUR' ? 'vendor' : 'customer';
        $data = ['company_id' => 1, 'type' => $type, 'currency_code' => 'EUR', 'enabled' => 1, 'name' => html_entity_decode($t->getCompleteName()), 'email' => $t->getEmail(), 'phone' => '', 'address' => html_entity_decode($t->getFullAdresse()), 'reference' => "TI-{$t->getId()}", 'created_at' => $t->getDateCreated()];
        Generique::insert('0so_contacts', 'graphene_akaunting', $data);
        }
        //Sync factures
        UTILS::notification('info', 'Synchronisation des factures...');
        // ---
        Generique::delete('0so_transactions', 'graphene_akaunting', []);
        Generique::delete('0so_document_totals', 'graphene_akaunting', []);
        Generique::delete('0so_documents', 'graphene_akaunting', []);
        Generique::delete('0so_document_items', 'graphene_akaunting', []);
        $factures = Generique::customSelect('facture', 'graphene_bsm', 'WHERE (statut = '.FACT_DELIVERY.' OR statut = '.FACT_RUNNING.' OR statut = '.FACT_TERMINATED.') AND id = FID');
        foreach ($factures as $f) {
        $f->saveToAkaunting();
        }
        // Sync product taxes
        UTILS::notification('info', 'Synchronisation des taxes...');
        // ---
        Generique::delete('0so_taxes', 'graphene_akaunting', []);
        $taxes = Generique::select('category_tva', 'graphene_bsm');
        foreach ($taxes as $t) {
        $data = $t->toAkaunting();
        Generique::insert('0so_taxes', 'graphene_akaunting', $data);
        }
        // Sync products
        UTILS::notification('info', 'Synchronisation des produits...');
        // ---
        Generique::delete('0so_items', 'graphene_akaunting', []);
        Generique::delete('0so_item_taxes', 'graphene_akaunting', []);
        $products = Generique::select('products', 'graphene_bsm');
        foreach ($products as $p) {
        if ($p->getId() > 0) {
        $date = new DateTime();
        $data = ['id' => $p->getId(), 'company_id' => 1, 'name' => html_entity_decode($p->getName()), 'description' => html_entity_decode($p->getDescription()), 'sale_price' => $p->getPrice(), 'purchase_price' => $p->getPurchasePrice() ?? 0, 'quantity' => $p->getStock(), 'tax_id' => $p->getIdCategoryTva(), 'enabled' => 1, 'created_at' => $p->getCreated()];
        Generique::insert('0so_items', 'graphene_akaunting', $data);
        $data = ['company_id' => 1, 'item_id' => $p->getId(), 'tax_id' => $p->getIdCategoryTva()];
        Generique::insert('0so_item_taxes', 'graphene_akaunting', $data);
        }
        }
        UTILS::notification('success', 'Toutes vos données ont été synchronisées avec le module de comptabilité');
    } else {
        UTILS::notification('info', 'Vous ne disposez pas du module "Akaunting", veuillez contacter Graphene-BSM afin de pouvoir en bénéficier');
    }
}

$PAGES = $tplSettings->construire('adminSettings');
$TITRE = 'Paramètres myGenghis';
$DESCRIPTION = 'Configurer votre site à votre image';
