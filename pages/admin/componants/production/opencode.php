<?php
    $tplOC = new Template;
    $tplOC->setFile('opencode', './_admin/componants/production/opencode.html');

    UTILS::addHistory(USER::getPseudo(), 28, "Accès OpenCode", "");
    
    $iframed = "";
    $file = "./classes/tinyfilemanager";
    if(is_dir($file)) {
      $iframed = "/classes/tinyfilemanager/tinyfilemanager.php";
    } else {
      $iframed = "/classes/_appNotInstalled.php";
    }

    $TITRE = "OpenCode";
    $DESCRIPTION = "OpenCode";
    $listMenuArray = array(
      array($TITRE, '', true)
    );
    $tplOC->values(array(
      'URL' => $_SERVER['REQUEST_URI'],
      'INNER_PAGE' => $iframed
    ));
    $PAGES = $tplOC->construire('opencode');
?>
