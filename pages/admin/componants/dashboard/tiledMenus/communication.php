<?php
$tplCommunication = new Template;
$tplCommunication->setFile('communication', './_admin/componants/dashboard/tiledMenus/communication.html');
$tplCommunication->values(array(
'URL' => $_SERVER['REQUEST_URI']
));
$req = MYSQL::query("SELECT url, Titre FROM user_custom_page");
while ($r = mysqli_fetch_object($req)) {
$tplCommunication->bloc('WEBPAGE_LIST', ['ID' => $r->url, "NAME" => $r->Titre]);
}
$req = MYSQL::query("SELECT id, title FROM articles");
while ($r = mysqli_fetch_object($req)) {
$tplCommunication->bloc('BLOG_LIST', ['ID' => $r->id, "NAME" => $r->title]);
}
$req = MYSQL::query("SELECT id, title FROM videos");
while ($r = mysqli_fetch_object($req)) {
$tplCommunication->bloc('VIDEO_LIST', ['ID' => $r->id, "NAME" => $r->title]);
}
$PAGES = $tplCommunication->construire('communication');
$TITRE = "Communication";
$DESCRIPTION = "Centre de gestion des communications";
?>
