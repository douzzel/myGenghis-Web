<?php
    $tplMembre = new Template;
    $tplMembre->setFile('projet', './_admin/componants/production/bloc-notes.html');

    $tplMembre->values(array(
        'URL' => $_SERVER['REQUEST_URI'],
        'INNER_PAGE' => "/classes/writty/index.html"
    ));

    $PAGES = $tplMembre->construire('projet');
    $TITRE = "Projet ";
    $DESCRIPTION = "Bloc-notes";
?>
