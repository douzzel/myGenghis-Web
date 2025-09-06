<?php
if (!USER::isConnecte()) {
  header('Location: /Connexion');
  exit;
}
$DISABLE_FORM = false;
$ACTION_MENU = "";

switch (DATA::getGet('Action')) {
case 'Settings':
include('componants/Settings/index.php');
break;
/*
case 'addCategoryTva':
include('componants/Settings/addCategoryTva.php');
break;
case 'listCategoryTva':
include('componants/Settings/listCategoryTva.php');
break;
case 'editCategoryTva':
include('componants/Settings/editCategoryTva.php');
break;
*/
case 'History':
include('componants/History/index.php');
break;
/*
case 'Membres':
include('componants/users/membre.php');
break;
case 'Contacts':
include('componants/users/contacts.php');
break;
case 'AddGroupe':
include('componants/users/addGroupe.php');
break;
case 'NotesDeFrais':
include('componants/users/notesdefrais.php');
break;
*/
// * Website
case 'Site':
include('componants/AddPage/index.php');
break;
case 'OnePage':
include('componants/AddPage/onepage.php');
break;
case 'Page':
include('componants/AddPage/page.php');
break;
case 'Article':
include('componants/AddPage/article.php');
break;
case 'Video':
include('componants/AddPage/video.php');
break;
// * Business
case 'CGV':
include('componants/business/vente/documentation/cgv.php');
break;
case 'CGU':
include('componants/business/vente/documentation/cgu.php');
break;
/*
case 'Devis':
include('componants/business/vente/documentation/devis.php');
break;
case 'DevisListe':
include('componants/business/vente/documentation/devis-list.php');
break;
case 'AddDevis':
include('componants/business/vente/documentation/add-devis.php');
break;
case 'EditDevis':
include('componants/business/vente/documentation/edit-devis.php');
break;
case 'DraftDevis':
include('componants/business/vente/documentation/draft-devis.php');
break;
case 'AddAvenant':
include('componants/business/vente/documentation/add-avenant.php');
break;
case 'BonLivraison':
include('componants/business/vente/documentation/bon-livraison.php');
break;
case 'BCListe':
include('componants/business/vente/documentation/bc-list.php');
break;
case 'AddBC':
include('componants/business/vente/documentation/add-bon-livraison.php');
break;
case 'Facture':
include('componants/business/vente/documentation/facture.php');
break;
case 'FactureListe':
include('componants/business/vente/documentation/facture-list.php');
break;
case 'AddFacture':
include('componants/business/vente/documentation/add-facture.php');
break;
case 'EditFacture':
include('componants/business/vente/documentation/edit-facture.php');
break;
case 'DraftFacture':
include('componants/business/vente/documentation/draft-facture.php');
break;
case 'ClickAndCollect':
include('componants/business/vente/clickandcollect.php');
break;
// case 'Avoir':
// include('componants/business/vente/documentation/avoir.php');
// break;
case 'Produits':
include('componants/business/vente/produit.php');
break;
case 'Tarif':
include('componants/Store/componants/delivery.php');
break;
case 'Categorie':
include('componants/business/vente/categorie.php');
break;
case 'Familles':
include('componants/business/vente/familles.php');
break;
case 'Conditionnement':
include('componants/Store/componants/conditionnement.php');
break;
case 'Scenarios':
include('componants/Store/componants/scenarios.php');
break;
case 'Add':
include('componants/business/vente/add.php');
break;
case 'Reservations':
include('componants/business/vente/reservation-list.php');
break;
//TRESORERIE
case 'Banque':
include('componants/tresorerie/banque.php');
break;
// * Social
case 'Personnel':
include('componants/social/personnel.php');
break;
case 'FicheDePoste':
include('componants/social/poste.php');
break;
case 'DocumentUnique':
include('componants/social/documentUnique.php');
break;
case 'Entretien':
include('componants/social/entretien.php');
break;
// case 'Salaire':
// include('componants/social/salaire/index.php');
// break;
// case 'BulletinSalaire':
// include('componants/social/salaire/bulletinSalaire.php');
// break;
case 'SecuriteProtocole':
include('componants/social/securiteProtocol.php');
break;
// * Recruitment
case 'Candidatures':
include('componants/recrutement/base.php');
break;
*/
case 'Test':
include('componants/recrutement/test.php');
break;
/*
case 'Campagne':
include('componants/recrutement/campagne.php');
break;
// * Marketing
case 'Persona':
include('componants/marketing/persona.php');
break;
case 'FichePersona':
include('componants/marketing/fiche-persona.php');
break;
case 'CampagnesListe':
include('componants/marketing/campagne.php');
break;
case 'CampagneFiche':
include('componants/marketing/campagne-fiche.php');
break;
case 'Litiges':
include('componants/marketing/litiges.php');
break;
case 'Parcours':
include('componants/marketing/parcours.php');
break;
case 'ParcoursListe':
include('componants/marketing/parcours-list.php');
break;
case 'Press':
include('componants/marketing/press.php');
break;
case 'Salon':
include('componants/marketing/salon.php');
break;
// * Production
case 'Polycompetance':
include('componants/production/polycompetance.php');
break;
case 'Deck':
include('componants/production/deck.php');
break;
case 'Kanban':
include('componants/production/kanboard.php');
break;
case 'Formulaire':
include('componants/production/formulaire.php');
break;
case 'NCSettings':
include('componants/production/ncSettings.php');
break;
case 'NCUsers':
include('componants/production/ncUsers.php');
break;
case 'Calendrier':
include('componants/production/calendar.php');
break;
case 'CalendrierMG':
include('componants/production/calendar_mg.php');
break;
case 'Discussion':
include('componants/production/discussion.php');
break;
*/
case 'BlocNotes':
include('componants/production/bloc-notes.php');
break;
case 'UrlShortener':
include('componants/production/url-shortnener.php');
break;
/*
case 'Drive':
include('componants/production/drive.php');
break;
*/
case 'FileManager':
include('componants/production/filemanager.php');
break;
/*
case 'Documents':
include('componants/Documents/liste.php');
break;
*/
case 'Analytics':
include('componants/production/analytics.php');
break;
/*
case 'Compta':
include('componants/production/akaunting.php');
break;
case 'Dashboard':
include('componants/dashboard/dashboard.php');
break;
// * COMMERCIAL
case 'Rappel':
include('componants/commercial/rappel.php');
break;
case 'Argumentaire':
include('componants/commercial/argumentaire.php');
break;
case 'ArgumentaireListe':
include('componants/commercial/argumentaire-list.php');
break;
// * Contact Center
case 'LiveChat':
include('componants/contactCenter/liveChat.php');
break;
case 'ChatBot':
include('componants/contactCenter/chatBot.php');
break;
case 'VideoChat':
include('componants/contactCenter/videoChat.php');
break;
// * Mails
case 'Mails':
include('componants/Mails/mails.php');
break;
case 'MailsPerso':
include('componants/production/snappymail.php');
break;
case 'MessagingTemplate':
include('componants/contactCenter/template.php');
break;
case 'MessagingSMTP':
include('componants/contactCenter/smtp.php');
break;
// * ERP
case 'AddContrat':
include('componants/Erp/addContrat.php');
break;
case 'AddRemboursement':
include('componants/Erp/addRemboursement.php');
break;
case 'AddDeblocage':
include('componants/Erp/addDeblocageMultiple.php');
break;
case 'AddAffectations':
include('componants/Erp/addAffectations.php');
break;
case 'AddFluxTresorerie':
include('componants/Erp/addFluxTresorerie.php');
break;
//********************************ERP PARAMETRAGES * /
case 'AddModeCalcul':
include('componants/Erp/addModeCalcul.php');
break;
case 'AddcategorieAffection':
include('componants/Erp/addCategorie.php');
break;
case 'AddTypeGarantie':
include('componants/Erp/addTypeGarantie.php');
break;
case 'AddPeriodicite':
include('componants/Erp/addPeriodicite.php');
break;
case 'AddType':
include('componants/Erp/addType.php');
break;
case 'ParcoursErp':
include('componants/Erp/parcours.php');
break;
//*******************************RIGHTS***************************** */
case 'ManageRights' :
include('componants/Rights/manageRights.php');
break;
case 'DataModules' :
include('componants/Rights/dataModules.php');
break;
//*******************************UTILS***************************** */
case 'Upload' :
include('componants/utils/upload.php');
break;
case 'List' :
include('componants/utils/list.php');
break;
//*******************************EXTRAS***************************** */
case 'Recherche' :
include('componants/dashboard/search.php');
break;
case 'OpenCode' :
include('componants/production/opencode.php');
break;
/*
case 'Domotique' :
include('componants/production/domotique.php');
break;
case 'HumHub' :
include('componants/production/humhub.php');
break;
case 'ServerMon':
include('componants/production/server-monitor.php');
break;
//*******************************ACCOUNT**************************** */
case 'accountSettings' :
include('componants/users/accountSettings.php');
break;
//*******************************TILES****************************** */
case 'AccesRapide':
include('componants/dashboard/tiledMenus/acces_rapide.php');
break;
case 'ActionRapide':
include('componants/dashboard/tiledMenus/action_rapide.php');
break;
case 'Communication':
include('componants/dashboard/tiledMenus/communication.php');
break;
/*
case 'DashboardMenu':
include('componants/dashboard/tiledMenus/dashboard.php');
break;
case 'Emails':
include('componants/dashboard/tiledMenus/mail.php');
//include('componants/contactCenter/emails.php');
break;
case 'Gouvernance':
include('componants/dashboard/tiledMenus/gouvernance.php');
break;
case 'Marketing':
include('componants/dashboard/tiledMenus/marketing.php');
break;
case 'Performance':
include('componants/dashboard/tiledMenus/performance.php');
break;
case 'Production':
include('componants/dashboard/tiledMenus/production.php');
break;
case 'Ventes':
include('componants/dashboard/tiledMenus/ventes.php');
break;
//************************"CUSTOM" DASHBOARDS*********************** * /
case 'GouvernancePlus':
include('componants/dashboard/tiledMenus/gouvernance_plus.php');
break;
//*******************************DEFAULT**************************** */
default:
//include('componants/index.php');
include('componants/AddPage/index.php');
break;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
UTILS::addHistory(USER::getPseudo(), 1, 'Action administrateur sur la page module ' . DATA::getGet('Action') . (DATA::isGet('act') ? ' <strong>[Action]</strong> : ' . DATA::getGet('act') : null), $_SERVER['REQUEST_URI']);
}
if (DATA::isGet('Action') && DATA::isGet('act') && DATA::isGet('name')) {
$PAGES = null;
include('componants/' . DATA::getGet('Action') . '/' . DATA::getGet('act') . '.php');
}
$js_disable_form = $DISABLE_FORM ? '<script>document.addEventListener("DOMContentLoaded", () => disableForms()); </script>' : '';
$tplAdmin = [
'PAGES' => $PAGES,
'TITRE' => $TITRE,
'DESCRIPTION' => $DESCRIPTION,
'ACTION_MENU' => $ACTION_MENU,
'JS_DISABLE_FORM' => $js_disable_form,
'CUSTOM_MENU' => UTILS::getInitialesSiteName()
];
// * Top Menu
if (ManageRights::verifyRights('Menu', 'Read', false, false)) {
$account = Generique::selectOne('accounts', 'graphene_bsm', ['id_client' => USER::getId()]);
$tplAdmin['IF_IS_CONNECTE'] = [
'PSEUDO' => USER::getPseudo(),
'AVATAR' => UTILS::getAvatar(USER::getPseudo()),
'colors' => Generique::selectOne('couleur_theme', 'graphene_bsm', ['Pseudo' => USER::getPseudo()])
];
$idClient = USER::getPseudo();
if ($idClient && MYSQL::selectOneValue("SELECT clickCollect FROM accounts WHERE pseudo = '{$idClient}'")) {
$tplAdmin['IF_IS_CLICKANDCOLLECT_PLACE'] = true;
}
$demoSite = ('demo.graphene-bsm.com' == $site);
$topMenu = $twig->render('Templates/menu-top.twig', ['ds' => $demoSite, 'u' => DATA::getGet('Action'), 'p' => DATA::getGet('Page')]);
$questPanel = $twig->render('Templates/quest-panel.twig', [
'NOM_USER' => $account->getCompleteName(),
'EMAIL_USER' => $account->getEmail(),
'SITE_NAME' => UTILS::getFunction('SiteName'),
'quests' => Generique::selectOne('accounts_quests', 'graphene_bsm', ['id_client' => USER::getId()]),
'location' => DATA::getGet('Action')
]);
$tplAdmin = array_merge($tplAdmin, [
'TOP_MENU' => $topMenu,
'QUEST_PANEL' => $questPanel,
'IF_IS_ADMIN' => true,
'NOM_USER' => $account->getCompleteName(),
'EMAIL_USER' => $account->getEmail(),
]);
if (!in_array($site, ['graphene-bsm.com', 'mygenghis.graphene-bsm.com'])) {
$tplAdmin['IF_IS_NOT_MYGENGHIS'] = true;
} else {
$tplAdmin['IF_IS_MYGENGHIS'] = true;
}
// * Nofications
$notificationList = NOTIFICATIONS::getNotification();
$notif_array = $notif_button = "";
if (!sizeof($notificationList)) {
$notif_array .= "<div class='notifEmpty'><i class='material-icons'>notifications</i><h6 style='color: black;'>Aucune notification</h6></div>";
} else {
foreach($notificationList as $key => $notif) {
$notif_array .= "<div id='deleteNotification_{$notif->getId()}'>";
$notif_array .= "<button type='button' onclick='deleteNotification(event, {$notif->getId()});' class='btn btn-link cursor-pointer deleteNotification' title='Supprimer la notification'><i class='material-icons'>close</i></button>";
$notif_array .= "<a href='{$notif->getLien()}' class='notificationCore' title='Voir''>
<div class='notificationDiv'><i class='material-icons'>{$notif->getIcon()}</i></div>
<div class='notificationText'><h6 style='color: " . ($notif->getView() ? 'black' : 'orange') . ";'>{$notif->getMessage()}</h6>
<span class='color-notification-date'>". $notif->getDate() . " </span></div>
</a>";
if ($key != sizeof($notificationList) - 1) {
$notif_array .= "<div class='line'></div>";
}
$notif_array .= "</div>";
}
$notif_button .= "<button type='button' onclick='deleteNotification(event, false);' class='btn btn-link cursor-pointer deleteAllNotification text-center'><span>Supprimer les notifications</span><i class='material-icons vertical-align'>close</i></button>";
}
if(DATA::isPost('deleteNotification')) {
$id_notif = DATA::getPost('id');
if (!$id_notif){
NOTIFICATIONS::deleteNotification();
} else {
NOTIFICATIONS::deleteNotification($id_notif);
}
}
if(DATA::isPost('viewNotification')) {
NOTIFICATIONS::resetNotificationNumber();
}
$tplAdmin = array_merge($tplAdmin, [
'NOTIFICATIONS_NUMBER' => NOTIFICATIONS::getNumberTotal(),
'NOTIFICATION_MENU' => $notif_array,
'NOTIFICATION_DELETE' => $notif_button
]);
} else {
$tplAdmin['IF_PAS_ADMIN'] = true;
}
$PAGE = $twig->render('./_admin/index.twig', $tplAdmin);
$DESCRIPTION = 'Administration du site ' . UTILS::getFunction('SiteName');
