<?php
$tplAccesRapide = new Template;
$tplAccesRapide->setFile('acces_rapide', './_admin/componants/dashboard/tiledMenus/acces_rapide.html');
$tplAccesRapide->values(array(
'URL' => $_SERVER['REQUEST_URI']
));
$PAGES = $tplAccesRapide->construire('acces_rapide');
$TITRE = "Accès Rapide";
$DESCRIPTION = "Centre des Accès Rapides de la plateforme";
?>
