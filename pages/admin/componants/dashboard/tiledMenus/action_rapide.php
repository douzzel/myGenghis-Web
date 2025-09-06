<?php
$tplActionRapide = new Template;
$tplActionRapide->setFile('action_rapide', './_admin/componants/dashboard/tiledMenus/action_rapide.html');
$tplActionRapide->values(array(
'URL' => $_SERVER['REQUEST_URI']
));
$PAGES = $tplActionRapide->construire('action_rapide');
$TITRE = "Action Rapide";
$DESCRIPTION = "Centre des Actions Rapides de la plateforme";
?>
