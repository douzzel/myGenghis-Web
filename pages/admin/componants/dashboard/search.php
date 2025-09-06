<?php
ini_set('display_errors', 0);
$tplSearch = new Template;
$tplSearch->setFile('search', './_admin/componants/dashboard/search.html');
$tplSearch->values(array(
'URL' => $_SERVER['REQUEST_URI']
));
$req = MYSQL::query("SELECT id, title FROM documents WHERE ISNULL(`ref`)");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('DOC_LIST', ['ID' => $r->id, "NAME" => $r->title]);
}
$req = MYSQL::query("SELECT id, name FROM documents_files");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('DOCFILE_LIST', ['ID' => $r->id, "NAME" => $r->name]);
}
$req = MYSQL::query('SELECT * FROM `accounts`');
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('MEMBRES', array('NAME' => "{$r->Nom} {$r->Prenom}", 'REF' => $r->id_client, 'PSEUDO' => $r->Pseudo));
}
$req = MYSQL::query('SELECT * FROM `contacts`');
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('CONTACTS', array('NAME' => $r->nom, 'REF' => $r->id));
}
$req = MYSQL::query("SELECT * FROM facture WHERE FID = id AND deleted_by is NULL AND (statut = 0 OR statut = 1 OR statut = 30 OR statut = 31)");
while ($r = mysqli_fetch_object($req)) {
$cart = round($r->total_cart);
$date = UTILS::date($r->date, 'd/m/Y');
$tplSearch->bloc('DEVISSEARCH', array('NAME' => "[{$r->FID}] {$r->nom} {$r->prenom} - {$cart}€ - {$date} - {$r->cp} {$r->ville}", 'REF' => $r->id));
}
$req = MYSQL::query("SELECT * FROM facture WHERE FID = id AND deleted_by is NULL AND statut = 2");
while ($r = mysqli_fetch_object($req)) {
$cart = round($r->total_cart);
$date = UTILS::date($r->date, 'd/m/Y');
$tplSearch->bloc('BCSEARCH', array('NAME' => "[{$r->numero_facture}] {$r->nom} {$r->prenom} - {$cart}€ - {$date} - {$r->cp} {$r->ville}", 'REF' => $r->id));
}
$req = MYSQL::query("SELECT * FROM facture WHERE FID = id AND deleted_by is NULL AND (statut = 3 OR statut = 4 OR statut = 5)");
while ($r = mysqli_fetch_object($req)) {
$cart = round($r->total_cart);
$ttcv = UTILS::price($r->total_cart + $r->tva_total, true);
$date = UTILS::date($r->date, 'd/m/Y');
$tplSearch->bloc('FACTURESEARCH', ['NAME' => "[{$r->numero_facture} {$r->id}] {$r->nom} {$r->prenom} - {$cart}€ HT / {$ttcv} TTC - {$date} - {$r->cp} {$r->ville}", 'REF' => $r->id,]);
}
$req = MYSQL::query("SELECT * FROM facture WHERE FID = id AND deleted_by is NULL AND statut = 32");
while ($r = mysqli_fetch_object($req)) {
$cart = round($r->total_cart);
$ttcv = UTILS::price($r->total_cart + $r->tva_total, true);
$date = UTILS::date($r->date, 'd/m/Y');
$tplSearch->bloc('AVENANTSEARCH', ['NAME' => "[{$r->numero_facture} {$r->id}] {$r->nom} {$r->prenom} - {$cart}€ HT / {$ttcv} TTC - {$date} - {$r->cp} {$r->ville}", 'REF' => $r->id,]);
}
$req = MYSQL::query("SELECT id, `name` FROM products WHERE deleted = 0");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('PRODUCT_LIST', ['ID' => $r->id, "NAME" => $r->name]);
}
$req = MYSQL::query("SELECT id, `name` FROM product_categories WHERE active = 1 AND deleted = 0");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('CATEGORY_LIST', ['ID' => $r->id, "NAME" => $r->name]);
}
$req = MYSQL::query("SELECT id, `name` FROM product_family WHERE active = 1 AND deleted = 0");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('FAMILLY_LIST', ['ID' => $r->id, "NAME" => $r->name]);
}
$req = MYSQL::query("SELECT url, Titre FROM user_custom_page");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('WEBPAGE_LIST', ['ID' => $r->url, "NAME" => $r->Titre]);
}
$req = MYSQL::query("SELECT id, title FROM articles");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('BLOG_LIST', ['ID' => $r->id, "NAME" => $r->title]);
}
$req = MYSQL::query("SELECT id, title FROM videos");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('VIDEO_LIST', ['ID' => $r->id, "NAME" => $r->title]);
}
$req = MYSQL::query("SELECT id, `name` FROM persona");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('PERSONA_LIST', ['ID' => $r->id, "NAME" => $r->name]);
}
$req = MYSQL::query("SELECT id, titre FROM argumentaire");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('ARGUMENTAIRE_LIST', ['ID' => $r->id, "NAME" => $r->titre]);
}
$req = MYSQL::query("SELECT id, titre FROM parcours");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('PARCOURS_LIST', ['ID' => $r->id, "NAME" => $r->titre]);
}
$req = MYSQL::query("SELECT id, titre FROM campagne_pub");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('PUB_LIST', ['ID' => $r->id, "NAME" => $r->titre]);
}
$req = MYSQL::query("SELECT id, name, subject, from_email FROM mail_receive");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('INMAIL_LIST', ['ID' => $r->id, "NAME" => $r->name . ' - ' . $r->from_email, "TITLE" => $r->subject]);
}
$req = MYSQL::query("SELECT id, subject, to_email FROM mail_sent");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('OUTMAIL_LIST', ['ID' => $r->id, "NAME" => $r->to_email, "TITLE" => $r->subject]);
}
$req = MYSQL::query("SELECT id, titre FROM template_email");
while ($r = mysqli_fetch_object($req)) {
$tplSearch->bloc('TEMPLATE_LIST', ['ID' => $r->id, "NAME" => $r->titre]);
}
$PAGES = $tplSearch->construire('search');
$TITRE = "Recherche";
$DESCRIPTION = "Centre de recherche";
?>