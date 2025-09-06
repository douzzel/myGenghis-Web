<?php
    $tplMatomo = new Template;
    $tplMatomo->setFile('analytics', './_admin/componants/production/analytics.html');

    UTILS::addHistory(USER::getPseudo(), 28, "Consultation d'Analytics", "");

    $TITRE = "Analytics";
    $DESCRIPTION = "Web Analytics";
    $listMenuArray = array(
      array($TITRE, '', true)
    );
    $tplMatomo->values(array(
      'URL' => $_SERVER['REQUEST_URI'],
      'INNER_PAGE' => "http://185.41.152.250/",
      'FIL_ARIANNE' => MENU::filArianne($listMenuArray, 'perf')
    ));
    $PAGES = $tplMatomo->construire('analytics');
?>
