<?php
$tplPB = new Template;
$tplPB->setFile('PB', './_admin/componants/AddPage/index.html');
function randomContent() {
$title = UTILS::randomTitle();
$text = UTILS::randomText();
return ", '{$title}', '{$text}'";
}
function addText($pageId, $label) {
$r = randomContent();
return "({$pageId}, '$label', NULL, NULL {$r})";
}
function addImage($pageId, $label) {
$r = randomContent();
$image = UTILS::randomImage();
return "({$pageId}, '$label', '{$image}', NULL {$r})";
}
function addVideo($pageId, $label) {
$r = randomContent();
$image = UTILS::randomImage();
return "({$pageId}, '$label', '{$image}', '/themes/assets/video/demo.mp4' {$r})";
}
function createBloc($values) {
MYSQL::query("INSERT INTO onepage (`page`, label, `image`, `video`, `title`, `description`) VALUES {$values}");
}
if(DATA::isGet('act')){
// $templates = [ '1' => 'Page_8', '2' => 'Page_1', '3' => 'Services_1', '4' => 'Services_2', '5' => 'Accueil_1', '6' => 'Accueil_2', '7' => 'Page_2', '8' => 'Liste_1', '9' => 'Liste_2', '10' => 'Page_3', '11' => 'Etape_1', '12' => 'Page_4', '13' => 'Page_5', '14' => 'Page_6', '15' => 'Contact_1', '16' => 'Liste_3', '17' => 'Accueil_contact', '18' => 'Page_7', '19' => 'Etape_2'];
ManageRights::verifyRights('Web', 'Write');
$tplPB->bloc('NEW');
$templates = [
'Accueil' => ['Accueil_1', 'Accueil_2', 'Accueil_contact'],
'Etape' => ['Etape_1', 'Etape_2'],
'Liste' => ['Liste_1', 'Liste_2', 'Liste_3'],
'Services' => ['Services_1', 'Services_2'],
'Divers' => ['Contact_1', 'Reservation_1'],
'Page' => ['Page_1', 'Page_2', 'Page_3', 'Page_4', 'Page_5', 'Page_6', 'Page_7', 'Page_8'],
'Utilisateurs avancés' => ['Page_vide_banniere', 'Page_vide', 'Vierge']
];
foreach ($templates as $key => $value) {
$tplPB->bloc('NEW.TEMPLATE', ['NAME' => $key]);
foreach ($value as $temp) {
$tplPB->bloc('NEW.TEMPLATE.VALUE', ['ID' => $temp]);
}
}
if(DATA::isPost('url') && DATA::isPost('PageName') && DATA::isPost('template')){
$pageUrl = DATA::getPost('url');
if (MYSQL::selectOneValue("SELECT id FROM user_custom_page WHERE url = '{$pageUrl}'")) {
UTILS::notification('danger', "Erreur : Une page avec l'URL « {$pageUrl} » existe déjà.");
}
$sortOrder = MYSQL::selectOneValue("SELECT max(sort_order) FROM user_custom_page") + 1;
$visibility = DATA::getPost('visibility') == 'visible' ? 'true' : 'false';
MYSQL::query('INSERT INTO user_custom_page SET url = \''.DATA::getPost('url').'\', PageName = \''.DATA::getPost('PageName').'\', Titre = \''.DATA::getPost('PageName').'\', Description = "", template = \''.DATA::getPost('template')."', date = NOW(), sort_order = '{$sortOrder}', `visibility` = {$visibility}");
UTILS::notification('success', 'Votre nouvelle page est prête, vous devez maintenant remplir son contenu.', false, true);
$templateId = DATA::getPost('template');
$pageId = MYSQL::selectOneValue('SELECT max(id) FROM user_custom_page');
$values = "";
/*
createBloc(addText($pageId, 'PAGE'));
/*/
switch ($templateId) {
case 'Page_1':
createBloc(addImage($pageId, 'PART1') . ',' . addVideo($pageId, 'PART2') . ',' . addImage($pageId, 'PART3'));
break;
case 'Accueil_1':
createBloc(addImage($pageId, 'TITLE') . ',' . addImage($pageId, 'PART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3') . ',' . addImage($pageId, 'PART4'));
break;
case 'Accueil_2':
createBloc(addImage($pageId,'CARROUSEL1') . ',' . addImage($pageId,'CARROUSEL2') . ',' . addImage($pageId,'CARROUSEL3') . ',' . addVideo($pageId,'PART1') . ',' . addText($pageId,'PART2') . ',' . addImage($pageId,'COUNTER1') . ',' . addText($pageId,'COUNTER2') . ',' . addText($pageId,'PART3') . ',' . addImage($pageId,'PART4') . ',' . addText($pageId,'PART5'));
break;
case 'Accueil_contact':
createBloc(addImage($pageId,'CARROUSEL1') . ',' . addImage($pageId,'CARROUSEL2') . ',' . addText($pageId,'PART1') . ',' . addImage($pageId,'BAR') . ',' . addText($pageId, 'CONTACT'));
break;
case 'Page_8':
case 'Services_1':
createBloc(addText($pageId, 'TITLE') . ',' . addImage($pageId, 'PART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3'));
break;
case 'Services_2':
createBloc(addImage($pageId, 'PART1') . ',' . addText($pageId, 'SUBPART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3'));
break;
case 'Liste_1':
createBloc(addText($pageId, 'HEADER') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE'));
break;
case 'Liste_2':
createBloc(addText($pageId, 'HEADER') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE'));
break;
case 'Liste_3':
createBloc(addImage($pageId, 'CARROUSEL1') . ',' . addImage($pageId, 'CARROUSEL2') . ',' . addImage($pageId, 'CARROUSEL3') . ',' . addText($pageId, 'HEADER') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE'));
break;
case 'Page_2':
createBloc(addVideo($pageId, 'PART1') . ',' . addText($pageId, 'PART2') . ',' . addImage($pageId, 'PART3') . ',' . addImage($pageId, 'PART3LEFT') . ',' . addImage($pageId, 'PART3RIGHT') . ',' . addImage($pageId, 'PART4') . ',' . addText($pageId, 'ACCORDION1') . ',' . addText($pageId, 'ACCORDION2') . ',' . addText($pageId, 'ACCORDION3') . ',' . addText($pageId, 'ACCORDION4'));
break;
case 'Page_3':
case 'Etape_1':
createBloc(addImage($pageId, 'PART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3') . ',' . addImage($pageId, 'PART4') . ',' . addImage($pageId, 'PART5') . ',' . addImage($pageId, 'PART6'));
break;
case 'Etape_2':
createBloc(addText($pageId, 'PART1') . ',' . addText($pageId, 'ACCORDION1') . ',' . addText($pageId, 'ACCORDION2') . ',' . addText($pageId, 'ACCORDION3'));
break;
case 'Page_4':
createBloc(addText($pageId, 'TITLE') . ',' . addImage($pageId, 'STEP1') . ',' . addImage($pageId, 'STEP2') . ',' . addImage($pageId, 'STEP3') . ',' . addImage($pageId, 'STEP4') . ',' . addImage($pageId, 'PART1') . ',' . addText($pageId, 'PART2'));
break;
case 'Page_5':
createBloc(addText($pageId, 'PART1') . ',' . addText($pageId, 'PART2') . ',' . addImage($pageId, 'TABHEADER') . ',' . addText($pageId, 'TAB1') . ',' . addText($pageId, 'TAB2'));
break;
case 'Page_6':
createBloc(addText($pageId, 'PART1') . ',' . addImage($pageId, 'STEP1') . ',' . addImage($pageId, 'STEP2') . ',' . addImage($pageId, 'STEP3') . ',' . addImage($pageId, 'PART2'));
break;
case 'Page_7':
createBloc(addText($pageId, 'PART1'));
break;
case 'Contact_1':
createBloc(addText($pageId, 'HEADER') . ',' . addVideo($pageId, 'VIDEO'));
break;
case 'Reservation_1':
createBloc(addText($pageId, 'PART1'));
break;
case 'Page_vide':
case 'Page_vide_banniere':
case 'Vierge':
createBloc(addText($pageId, 'PAGE'));
break;
}
//*/
header('location: /Administration/Page/'.DATA::getPost('url'));
NOTIFICATIONS::add("web", "Nouvelle page web <b>{$title}</b> par " . NOTIFICATIONS::CreateTag(USER::getId()), "/".DATA::getPost('url'), [], "Web");
exit;
}
} else {
$req = MYSQL::query('SELECT * FROM user_custom_page ORDER BY sort_order');
if(mysqli_num_rows($req) > 0){
$tplPB->bloc('IF_IS_PAGE');
while($r = mysqli_fetch_object($req)){
$tplPB->bloc('IF_IS_PAGE.LISTE', array(
'PAGE_NAME' => $r->PageName,
'URL' => $r->url,
'IMAGE' => $r->image ? "src='{$r->image}'" : '',
'ORDRE' => $r->sort_order,
'DATE' => date('d/m/Y', strtotime($r->date)),
'GET_SITE_INITIAL' => UTILS::getInitialesSiteName(),
'ID' => $r->id,
'TEMPLATE_ID' => $r->template
));
}
}
}
$req = MYSQL::query("SELECT id, title, image, summary FROM articles ORDER BY id DESC");
while ($r = mysqli_fetch_object($req)) {
$tplPB->bloc('ARTICLES', array(
'ID' => $r->id,
'TITLE' => $r->title,
'IMAGE' => $r->image ? "src='{$r->image}'" : '',
'SUMMARY' => substr($r->summary, 0, 100)
));
}
$req = MYSQL::query("SELECT id, title, image, video FROM videos ORDER BY id DESC");
while ($r = mysqli_fetch_object($req)) {
$tplPB->bloc('VIDEOS', array(
'ID' => $r->id,
'TITLE' => $r->title,
'MINIATURE' => $r->image ? "<img src='{$r->image}' class='page-image'>" : "<video class='page-image' src='{$r->video}' preload='metadata'></video>"
));
}
if (!empty($_POST) && (DATA::isPost('deleteArticle') || DATA::isPost('deleteArticleConfirm') || DATA::isPost('deleteVideo') || DATA::isPost('deleteVideoConfirm')) && ManageRights::verifyRights('Articles & Vidéos', 'Write', false)) {
if (DATA::isPost('deleteArticle')) {
UTILS::Alert('danger', 'Suppression d\'un article, Voulez-vous vraiment supprimer cet article ?', 'L\'action sera irréversible', $_SERVER['REQUEST_URI'], 'deleteArticleConfirm', DATA::getPost('deleteArticle'));
}
if (DATA::isPost('deleteArticleConfirm')) {
$articleId = DATA::getPost('deleteArticleConfirm');
$articleTitle = MYSQL::selectOneValue("SELECT title FROM articles WHERE id='{$articleId}'");
MYSQL::query("DELETE FROM articles WHERE `id` = '{$articleId}'");
UTILS::notification('success', 'Article supprimé avec succès', false, true);
UTILS::addHistory(USER::getPseudo(), 25, "Article « {$articleTitle} » supprimé");
header('location: '.$_SERVER['REQUEST_URI']);
exit;
}
if (DATA::isPost('deleteVideo')) {
UTILS::Alert('danger', 'Suppression d\'une vidéo, Voulez-vous vraiment supprimer cette vidéo ?', 'L\'action sera irréversible', $_SERVER['REQUEST_URI'], 'deleteVideoConfirm', DATA::getPost('deleteVideo'));
}
if (DATA::isPost('deleteVideoConfirm')) {
$videoId = DATA::getPost('deleteVideoConfirm');
$videoTitle = MYSQL::selectOneValue("SELECT title FROM videos WHERE id='{$videoId}'");
MYSQL::query("DELETE FROM videos WHERE `id` = '{$videoId}'");
UTILS::notification('success', 'Vidéo supprimé avec succès', false, true);
UTILS::addHistory(USER::getPseudo(), 27, "Vidéo « {$videoTitle} » supprimée");
header('location: '.$_SERVER['REQUEST_URI']);
exit;
}
}
if (!empty($_POST) && (DATA::isPost('delete') || DATA::isPost('deletePage')) && ManageRights::verifyRights('Web', 'Write', false)) {
if (DATA::isPost('delete')) {
UTILS::Alert('danger', 'Suppression d\'une page, Voulez-vous vraiment supprimer cette page ?', 'L\'action sera irréversible', $_SERVER['REQUEST_URI'], 'deletePage', DATA::getPost('delete'));
}
if (DATA::isPost('deletePage')) {
$pageId = DATA::getPost('deletePage');
MYSQL::query("DELETE FROM user_custom_page WHERE id = '{$pageId}'");
MYSQL::query("DELETE FROM onepage WHERE `page` = '{$pageId}'");
UTILS::notification('success', 'Page supprimée avec succès', false, true);
header('location: '.$_SERVER['REQUEST_URI']);
exit;
}
}
$TITRE = "Web";
$DESCRIPTION = "Liste des pages";
$ACTION_MENU = createActionMenu([
['href' => '/Administration/Settings#nav-web', 'icon' => 'settings', 'title' => 'Paramètres'],
['href' => '/Administration/Site/New', 'icon' => 'web', 'title' => 'Nouvelle page du site'],
['href' => '/Administration/Article', 'icon' => 'article', 'title' => 'Nouvel article'],
['href' => '/Administration/Video', 'icon' => 'movie', 'title' => 'Nouvelle vidéo'],
]);
$tplPB->values(array(
'TITRE' => $TITRE,
'DESCRIPTION' => $DESCRIPTION
));
$PAGES = $tplPB->construire('PB');
