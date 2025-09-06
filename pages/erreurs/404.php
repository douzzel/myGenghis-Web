<?php
$tpl404 = new Template;
$tpl404->setFile('404', './erreurs/404.html');

$PAGE = $tpl404->construire('404');
$TITRE = 'Page introuvable';
$DESCRIPTION = 'La page rechercher est introuvable, merci de vérifier l\'adresse';

?>
