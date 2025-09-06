<?php
$tplSecurity = new Template;
$tplSecurity->setFile('security', './account/componants/security.html');


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
    $tplSecurity->bloc('IF_IS_API_KEY_GOOGLE_MAP');
}

$reqPhone = MYSQL::query('SELECT * FROM phone WHERE id_client = \''.$resultAccount->id_client.'\'');
$phone = mysqli_fetch_object($reqPhone);

$reqForm = MYSQL::query('SELECT * FROM reponses  WHERE auteur2 = \''.USER::getPseudo().'\'');
$form = mysqli_fetch_object($reqForm);


$reqSujet = MYSQL::query('SELECT * FROM sujets INNER JOIN reponses ON sujets.id_sujet=reponses.sujet_id  WHERE reponses.auteur2 = \''.USER::getPseudo().'\'');
$sujet = mysqli_fetch_object($reqSujet);


$tplSecurity->values(array(
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
    'AUTEUR' => $form->auteur2,
    'REPONSE' => $form->contenu,
    'DATE' => date('d/m/Y \à H\hi', $form->time),
    'COUNT_LIKE' => $nbr_like = MYSQL::selectOneValue('SELECT COUNT(id) FROM like_reponses WHERE id_reponses = \''.$form->id.'\''),
    'SUJET' => $sujet->titre
));

$query = MYSQL::query('SELECT * FROM phone WHERE id_client=\''.$resultAccount->id_client.'\'');

$PAGES = $tplSecurity->construire('security');
?>
