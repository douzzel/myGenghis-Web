<?php
spl_autoload_register(
function ($x) {
$sources = array('../classes/' . str_replace('_', '/', $x) . '.class.php'); // chargement des classes
foreach ($sources as $source) {
if (file_exists($source)) {
require_once $source;
}
}
$sources = array('../classes/entities/' . str_replace('_', '/', $x) . '.class.php'); // chargement des classes
foreach ($sources as $source) {
if (file_exists($source)) {
require_once $source;
}
}
}
);
$templates = [
'5' => 'Accueil_1',
'6' => 'Accueil_2',
'17' => 'Accueil_contact',
'11' => 'Etape_1',
'19' => 'Etape_2',
'8' => 'Liste_1',
'9' => 'Liste_2',
'16' => 'Liste_3',
'3' => 'Services_1',
'4' => 'Services_2',
'15' => 'Contact_1',
'20' => 'Reservation_1',
'2' => 'Page_1',
'7' => 'Page_2',
'10' => 'Page_3',
'12' => 'Page_4',
'13' => 'Page_5',
'14' => 'Page_6',
'18' => 'Page_7',
'1' => 'Page_8',
'21' => 'Page_vide_banniere',
'22' => 'Page_vide',
'23' => 'Vierge',
];
$NUMBER_TEMPLATE = count($templates);
$templatesKeys = array_keys($templates);
$templateId = DATA::getGet('page');
if (!is_numeric($templateId)) {
$templateId = array_search($templateId, $templates);
}
USER::init();
ManageRights::verifyRights('Web', 'Read');
if (!$templateId || $templateId < 1 || $templateId > $NUMBER_TEMPLATE) {
header("Location: /404");
exit;
}
$templateName = $templates[$templateId];
$tplPage = new Template;
if ($templateName != 'Vierge') {
$tplPage->setFile("header", "header.html");
$tplPage->setFile("page", "page.html");
$tplPage->setFile("footer", "footer.html");
} else {
$tplPage->setFile("page", "empty_page.html");
}
$tpl = $tplPage;
$tplPage->setFile('template', "templates/{$templateName}.html");
function addText($tplPage, $bloc) {
$tplPage->bloc($bloc, array(
'TITLE' => UTILS::randomTitle(),
'DESCRIPTION' => UTILS::randomText()
));
}
function addImage($tplPage, $bloc) {
$tplPage->bloc($bloc, array(
'TITLE' => UTILS::randomTitle(),
'DESCRIPTION' => UTILS::randomText(),
'IMAGE' => UTILS::randomImage()
));
}
function addVideo($tplPage, $bloc) {
$tplPage->bloc($bloc, array(
'TITLE' => UTILS::randomTitle(),
'DESCRIPTION' => UTILS::randomText(),
'IMAGE' => UTILS::randomImage(),
'VIDEO' => "/themes/assets/video/demo.mp4"
));
}
$pageId = $tplPage;
/*
addText($pageId, 'PAGE');
/*/
switch ($templateName) {
case 'Page_1':
addImage($pageId, 'PART1') . ',' . addVideo($pageId, 'PART2') . ',' . addImage($pageId, 'PART3');
break;
case 'Accueil_1':
addImage($pageId, 'TITLE') . ',' . addImage($pageId, 'PART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3') . ',' . addImage($pageId, 'PART4');
break;
case 'Accueil_2':
addImage($pageId,'CARROUSEL1') . ',' . addImage($pageId,'CARROUSEL2') . ',' . addImage($pageId,'CARROUSEL3') . ',' . addVideo($pageId,'PART1') . ',' . addText($pageId,'PART2') . ',' . addImage($pageId,'COUNTER1') . ',' . addText($pageId,'COUNTER2') . ',' . addText($pageId,'PART3') . ',' . addImage($pageId,'PART4') . ',' . addText($pageId,'PART5');
break;
case 'Accueil_contact':
addImage($pageId,'CARROUSEL1') . ',' . addImage($pageId,'CARROUSEL2') . ',' . addText($pageId,'PART1') . ',' . addImage($pageId,'BAR') . ',' . addText($pageId, 'CONTACT');
break;
case 'Page_8':
case 'Services_1':
addText($pageId, 'TITLE') . ',' . addImage($pageId, 'PART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3');
break;
case 'Services_2':
addImage($pageId, 'PART1') . ',' . addText($pageId, 'SUBPART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3');
break;
case 'Liste_1':
addText($pageId, 'HEADER') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE');
break;
case 'Liste_2':
addText($pageId, 'HEADER') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE');
break;
case 'Liste_3':
addImage($pageId, 'CARROUSEL1') . ',' . addImage($pageId, 'CARROUSEL2') . ',' . addImage($pageId, 'CARROUSEL3') . ',' . addText($pageId, 'HEADER') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE') . ',' . addImage($pageId, 'LISTE');
break;
case 'Page_2':
addVideo($pageId, 'PART1') . ',' . addText($pageId, 'PART2') . ',' . addImage($pageId, 'PART3') . ',' . addImage($pageId, 'PART3LEFT') . ',' . addImage($pageId, 'PART3RIGHT') . ',' . addImage($pageId, 'PART4') . ',' . addText($pageId, 'ACCORDION1') . ',' . addText($pageId, 'ACCORDION2') . ',' . addText($pageId, 'ACCORDION3') . ',' . addText($pageId, 'ACCORDION4');
break;
case 'Page_3':
case 'Etape_1':
addImage($pageId, 'PART1') . ',' . addImage($pageId, 'PART2') . ',' . addImage($pageId, 'PART3') . ',' . addImage($pageId, 'PART4') . ',' . addImage($pageId, 'PART5') . ',' . addImage($pageId, 'PART6');
break;
case 'Etape_2':
addText($pageId, 'PART1') . ',' . addText($pageId, 'ACCORDION1') . ',' . addText($pageId, 'ACCORDION2') . ',' . addText($pageId, 'ACCORDION3');
break;
case 'Page_4':
addText($pageId, 'TITLE') . ',' . addImage($pageId, 'STEP1') . ',' . addImage($pageId, 'STEP2') . ',' . addImage($pageId, 'STEP3') . ',' . addImage($pageId, 'STEP4') . ',' . addImage($pageId, 'PART1') . ',' . addText($pageId, 'PART2');
break;
case 'Page_5':
addText($pageId, 'PART1') . ',' . addText($pageId, 'PART2') . ',' . addImage($pageId, 'TABHEADER') . ',' . addText($pageId, 'TAB1') . ',' . addText($pageId, 'TAB2');
break;
case 'Page_6':
addText($pageId, 'PART1') . ',' . addImage($pageId, 'STEP1') . ',' . addImage($pageId, 'STEP2') . ',' . addImage($pageId, 'STEP3') . ',' . addImage($pageId, 'PART2');
break;
case 'Page_7':
addText($pageId, 'PART1');
break;
case 'Contact_1':
addText($pageId, 'HEADER') . ',' . addVideo($pageId, 'VIDEO');
break;
case 'Reservation_1':
addText($pageId, 'PART1');
break;
case 'Page_vide_banniere':
case 'Page_vide':
case 'Vierge':
addText($pageId, 'PAGE');
break;
}
//*/
$displayWebMenu = UTILS::getFunction('displayWebMenu');
if ($displayWebMenu)
$tpl->bloc('DISPLAY_WEB_MENU');
if ($displayWebMenu) {
// Construct the menu bar
$sql = "SELECT url, pageName, id FROM user_custom_page WHERE page_group IS NULL AND visibility IS true ORDER BY sort_order";
$reqMenu = MYSQL::query($sql);
if (mysqli_num_rows($reqMenu) > 0) {
while ($r = mysqli_fetch_object($reqMenu)) {
$subHtml = "";
$subOnlyItems = "";
$sqlSub = MYSQL::query("SELECT url, pageName FROM user_custom_page WHERE page_group = '{$r->id}' AND visibility IS true ORDER BY sort_order");
if (mysqli_num_rows($sqlSub) > 0) {
$subHtml .= "<ul class='sub-menu'>";
while ($sub = mysqli_fetch_object($sqlSub)) {
$active = "";
if ($_SERVER['REQUEST_URI'] == '/'.$sub->url) {
$active = "class='active'";
}
$subOnlyItems .= "<li {$active}><a href='{$sub->url}'>{$sub->pageName}</a></li>";
}
$subHtml .= $subOnlyItems;
$subHtml .= "</ul>";
}
$active = "";
if ($_SERVER['REQUEST_URI'] == '/'.$r->url) {
$active = "class='active'";
}
$tpl->bloc('DISPLAY_WEB_MENU.IF_IS_MENU', [
'TITLE' => $r->pageName,
'URL' => $r->url,
'SUBITEMS' => $subHtml,
'SUB_ONLY_ITEMS' => $subOnlyItems,
'ACTIVE' => $active
]);
}
}
$contact = 0;
// Construct the menu bar
$sql = "SELECT url, pageName, template FROM user_custom_page WHERE visibility IS true ORDER BY sort_order";
$reqMenu = MYSQL::query($sql);
if (mysqli_num_rows($reqMenu) > 0) {
while ($r = mysqli_fetch_object($reqMenu)) {
$tpl->bloc('DISPLAY_WEB_MENU.IF_IS_MENU_MOBILE', [
'TITLE' => $r->pageName,
'URL' => $r->url
]);
if (in_array($r->template, [17]) && $contact == 0) {
$contact = 1;
$tpl->bloc('DISPLAY_WEB_MENU.IF_CONTACT', [
'URL' => $r->url
]);
}
}
}
if (UTILS::isModuleActive('STORE'))
$tpl->bloc('DISPLAY_WEB_MENU.IF_STORE');
if (UTILS::isModuleActive('ARTICLES'))
$tpl->bloc('DISPLAY_WEB_MENU.IF_ARTICLES');
if (UTILS::isModuleActive('VIDEOS'))
$tpl->bloc('DISPLAY_WEB_MENU.IF_VIDEOS');
if (UTILS::isModuleActive('CONNEXION'))
$tpl->bloc('DISPLAY_WEB_MENU.IF_CONNEXION');
}
if (UTILS::getFunction('phone'))
$tplPage->bloc('IF_PHONE');
$tplPage->values(array(
'PHONE' => UTILS::getFunction('phone'),
'ADDRESS' => UTILS::getFunction('address'),
'SITENAME' => UTILS::getFunction('SiteName'),
'FACEBOOK' => UTILS::getFunction('urlFacebook'),
'YOUTUBE' => UTILS::getFunction('urlYoutube'),
'LINKEDIN' => UTILS::getFunction('urlLinkedin'),
'INSTAGRAM' => UTILS::getFunction('urlInstagram'),
'TWITTER' => UTILS::getFunction('urlTwitter'),
'EMAIL' => UTILS::getFunction('WebmasterEmail'),
'STATIC_URL' => UTILS::getFunction('StaticUrl'),
'URL' => $_SERVER['REQUEST_URI']
));
// Construct the menu bar
$sql = "SELECT url, pageName, id FROM user_custom_page WHERE page_group IS NULL AND visibility IS true ORDER BY sort_order";
$reqMenu = MYSQL::query($sql);
if (mysqli_num_rows($reqMenu) > 0) {
while ($r = mysqli_fetch_object($reqMenu)) {
$subHtml = "";
$subOnlyItems = "";
$sqlSub = MYSQL::query("SELECT url, pageName FROM user_custom_page WHERE page_group = '{$r->id}' AND visibility IS true ORDER BY sort_order");
if (mysqli_num_rows($sqlSub) > 0) {
$subHtml .= "<ul class='sub-menu'>";
while ($sub = mysqli_fetch_object($sqlSub)) {
$active = "";
if ($_SERVER['REQUEST_URI'] == '/'.$sub->url)
$active = "class='active'";
$subOnlyItems .= "<li {$active}><a href='{$sub->url}'>{$sub->pageName}</a></li>";
}
$subHtml .= $subOnlyItems;
$subHtml .= "</ul>";
}
$active = "";
if ($_SERVER['REQUEST_URI'] == '/'.$r->url)
$active = "class='active'";
$tplPage->bloc('IF_IS_MENU', array(
'TITLE' => $r->pageName,
'URL' => $r->url,
'SUBITEMS' => $subHtml,
'SUB_ONLY_ITEMS' => $subOnlyItems,
'ACTIVE' => $active
));
}
}
$contact = 0;
// Construct the menu bar
$sql = "SELECT url, pageName, template FROM user_custom_page WHERE visibility IS true ORDER BY sort_order";
$reqMenu = MYSQL::query($sql);
if (mysqli_num_rows($reqMenu) > 0) {
while ($r = mysqli_fetch_object($reqMenu)) {
$tplPage->bloc('IF_IS_MENU_MOBILE', array(
'TITLE' => $r->pageName,
'URL' => $r->url
));
if (in_array($r->template, [17]) && $contact == 0) {
$contact = 1;
$tplPage->bloc('IF_CONTACT', array(
'URL' => $r->url
));
}
}
}
if (!in_array($templateName, ['Accueil_1', 'Accueil_2', 'Liste_3', 'Accueil_contact', 5, 6, 16, 17, 'GBSM_Video', 'GBSM_7', 'MP_index', 'Page_vide'])) {
$tplPage->bloc('IF_SHOWHEADER');
}
if (in_array($templateName, ['Reservation_1'])) { // If template reservation
$locations = Generique::select('reservation_location', 'graphene_bsm');
foreach ($locations as $r) {
$tplPage->bloc('RESERVATION_LOCATION', [
'ID' => $r->getId(),
'NAME' => $r->getName(),
'ADDRESS' => $r->getAddress(),
]);
}
}
$tplPage->values(array(
'PAGENAME' => 'Template '.$templateName,
'TITLE' => 'Template '.$templateName,
'IMAGE' => UTILS::randomImage(),
'PAGE' => UTILS::Encode($tplPage->construire('template'))
));
if (UTILS::isModuleActive('STORE'))
$tplPage->bloc('IF_STORE');
if (UTILS::isModuleActive('ARTICLES'))
$tplPage->bloc('IF_ARTICLES');
if (UTILS::isModuleActive('VIDEOS'))
$tplPage->bloc('IF_VIDEOS');
if (UTILS::isModuleActive('CONNEXION'))
$tplPage->bloc('IF_CONNEXION');
if (UTILS::isModuleActive('CONTACT'))
$tplPage->bloc('IF_CONTACT_FORM');
$buffer = "";
if ($templateName != 'Vierge')
$buffer .= UTILS::Encode($tplPage->construire('header'));
$buffer .= UTILS::Encode($tplPage->construire('page'));
$templatePosition = array_search($templateId, $templatesKeys);
if ($templatePosition > 1)
$buffer .= UTILS::Encode('<div class="template-prev" title="Template Précédent" onclick="window.location = \'/site/template.php?page='.($templatesKeys[$templatePosition - 1]).'\'"><i></i></div>');
if ($templatePosition < $NUMBER_TEMPLATE - 1)
$buffer .= UTILS::Encode('<div class="template-next" title="Template Suivant" onclick="window.location = \'/site/template.php?page='.($templatesKeys[$templatePosition + 1]).'\'"><i></i></div>');
$buffer .= "<div class='m-4'><span class='mr-4'><b>Template : </b></span>";
foreach ($templates as $key => $value) {
$active = $value == $templateName ? 'bg-dark color-white' : '';
$buffer .= '<button class="btn '.$active.'" onclick="window.location = \'/site/template.php?page='.$key.'\'" title="Template '.$value.'">'.$value.'</button>';
}
$buffer .= "</div>";
if ($templateName != 'Vierge')
$buffer .= UTILS::Encode($tplPage->construire('footer'));
echo UTILS::compressHtml($buffer);
