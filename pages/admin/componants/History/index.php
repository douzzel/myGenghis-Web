<?php
    $tplHist = new Template;
    $tplHist->setFile('hist', './_admin/componants/History/index.html');

    UTILS::addHistory(USER::getPseudo(), 28, "Consultation de l'Historique des Actions", "");

    $iframed = "/Account/History";

    $TITRE = "Historique";
    $DESCRIPTION = "Historique des Actions";
    $listMenuArray = array(
      array($TITRE, '', true)
    );
    $tplHist->values(array(
      'URL' => $_SERVER['REQUEST_URI'],
      'INNER_PAGE' => $iframed
    ));
    $PAGES = $tplHist->construire('hist');

    /*
    ManageRights::verifyRights('Membres', 'Read');
    $tplHistory = new Template;
    $tplHistory->setFile('history', './_admin/componants/history/index.html');

    $req = MYSQL::query('SELECT DISTINCT(isDate) FROM historique ORDER BY isDate desc');
    if(mysqli_num_rows($req) > 0){
        $tplHistory->bloc('HISTORIQUE');
        while($d = mysqli_fetch_object($req)){
            $tplHistory->bloc('HISTORIQUE.CAT_DATE', array(
                'DATE' => date("d/m/Y", strtotime($d->isDate))
            ));

            $list = MYSQL::query('SELECT * FROM historique WHERE isDate = \''.$d->isDate.'\' ORDER BY id desc');
            if(mysqli_num_rows($list) > 0){
                while($r = mysqli_fetch_object($list)){
                    list ($type_puce, $text_puce) = UTILS::getHistoryType($r->idType_historique);
                    $tplHistory->bloc('HISTORIQUE.CAT_DATE.LISTE', array(
                        'TYPE_PUCE' => $type_puce,
                        'TEXT_PUCE' => $text_puce,
                        'DESCRIPTION' => html_entity_decode($r->isAction),
                        'PSEUDO' => ($r->memb___id ? '<strong>Pseudo : ' .$r->memb___id .'</strong>' : null)
                    ));
                }
            }
        }
    }
    $PAGES = $tplHistory->construire('history');
    $TITRE = "Historique du site";
    $DESCRIPTION = "consulter l'historique de toutes les actions faite sur le site";
    */
?>
