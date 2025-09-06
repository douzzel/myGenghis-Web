<?php
$tplHistory = new Template;
$tplHistory->setFile('history', './account/componants/history.html');



switch($AdminMemberID){
    case true :
        $request = 'id_client = \''.DATA::getGet('Member').'\'';
    break;
    default :
        $request = 'pseudo = \''.USER::getPseudo().'\'';
    break;
}

$req = MYSQL::query('SELECT * FROM accounts WHERE '.$request);
$resultAccount = mysqli_fetch_object($req);

$dateInscription = date('d M Y', strtotime($resultAccount->Date_Inscription));


if(UTILS::getFunction('API_KEYS_GOOGLE_MAP') != null){
    $tplHistory->bloc('IF_IS_API_KEY_GOOGLE_MAP');
}

$reqPhone = MYSQL::query('SELECT * FROM phone WHERE id_client = \''.$resultAccount->id_client.'\'');
$phone = mysqli_fetch_object($reqPhone);

$tplHistory->values(array(
    'AVATAR' => UTILS::getAvatar($resultAccount->Pseudo),
    'PSEUDO' => USER::getPseudo(),
    'NOM' => $resultAccount->Nom,
    'PRENOM' => $resultAccount->Prenom,
    'EMAIL' => $resultAccount->Email,
    'DATE_INSCRIPTION' => $dateInscription,
    'PAYS' => $resultAccount->Pays,
    'URL' => UTILS::getFunction('StaticUrl'),
    'ABOUT' => $resultAccount->about,
    'VILLE' => $resultAccount->Ville,
    'SIGNATURE' => $resultAccount->signature,
));

$query = MYSQL::query('SELECT * FROM phone WHERE id_client=\''.$resultAccount->id_client.'\'');

    $req = MYSQL::query("SELECT DISTINCT(DATE(isDate)) as isDate FROM historique WHERE memb___id = '{$resultAccount->Pseudo}' ORDER BY isDate DESC LIMIT 30");
    if(mysqli_num_rows($req) > 0){
        $tplHistory->bloc('HISTORIQUE');
        while($d = mysqli_fetch_object($req)){
            $tplHistory->bloc('HISTORIQUE.CAT_DATE', array(
                'DATE' => date("d/m/Y", strtotime($d->isDate))
            ));

            $list = MYSQL::query("SELECT * FROM historique WHERE cast(isDate as date) = '{$d->isDate}' AND memb___id = '{$resultAccount->Pseudo}' ORDER BY id desc");
            if(mysqli_num_rows($list) > 0){
                while($r = mysqli_fetch_object($list)){
                    list ($type_puce, $text_puce) = UTILS::getHistoryType($r->idType_historique);
                    $tplHistory->bloc('HISTORIQUE.CAT_DATE.LISTE', array(
                        'TYPE_PUCE' => $type_puce,
                        'TEXT_PUCE' => $text_puce,
                        'DESCRIPTION' => $r->isAction ? html_entity_decode($r->isAction) : '',
                    ));
                }
            }
        }
    }

$PAGES = $tplHistory->construire('history');
?>
