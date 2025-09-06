<?php
ManageRights::verifyRights('Dashboard', 'Read');
$tplDashboard = new Template;
$tplDashboard->setFile('adminDashboard', './_admin/componants/dashboard/index.html');

if (DATA::isPost('quest_update')) {
  $questId = DATA::getPost('quest_update');
  $filter = ['id_client' => USER::getId()];
  if (DATA::issetPost('quest_update_status')) {
    $data = [$questId => DATA::getPost('quest_update_status'), 'id_client' => USER::getId()];
  } else {
    $data = [$questId => true, 'id_client' => USER::getId()];
  }
  Generique::updateOrInsert('accounts_quests', 'graphene_bsm', $filter, $data);
  exit;
}

// NB CLIENTS
$Client = MYSQL::selectOneValue("SELECT COUNT(id) as 'nb' FROM contacts WHERE UPPER(categorie) = 'CLIENT'");

// NB PROSPECT
$Prospet = MYSQL::selectOneValue("SELECT COUNT(id) as 'nb' FROM contacts WHERE UPPER(categorie) = 'PROSPECT'");

// NB FOURNISSEUR
$Fournisseur = MYSQL::selectOneValue("SELECT COUNT(id) as 'nb' FROM contacts WHERE UPPER(categorie) = 'FOURNISSEUR'");

// NB CONTACT
$Contact = MYSQL::selectOneValue("SELECT COUNT(id) as 'nb' FROM contacts");

// NB MEMBRES
$Membres = MYSQL::selectOneValue("SELECT count(*) FROM accounts");

// NB VISITE
$Cpt = MYSQL::selectOneValue("SELECT COUNT(c_ip) as 'nb' FROM compteur");
// NB VISITE DIFFERENTES
$Cpt_d = MYSQL::selectOneValue("SELECT DISTINCT COUNT(c_ip) as 'nb' FROM compteur");

// CA
$Ca = MYSQL::selectOneValue("SELECT SUM(total_cart) FROM facture WHERE FID = id AND statut = 2");
$Ca = $Ca ? $Ca : '0';

// nb facture
$Facture = MYSQL::selectOneValue("SELECT DISTINCT COUNT(FID) as 'nb' FROM facture GROUP BY FID");
$Facture = $Facture ? $Facture : '0';

$Nb_consult = $Nb_achat = $Nb_avis = 0;
if (UTILS::getModule('STORE', $tplDashboard, 'IF_STORE')) {
    // moy consult store
    $Nb_consult = MYSQL::selectOneValue("SELECT AVG(Nbr_Consultation) as 'nb' FROM store");

    // moy achat store
    $Nb_achat = MYSQL::selectOneValue("SELECT AVG(Nbr_Achat) as 'nb' FROM store");

    // moy avis store
    $Nb_avis = MYSQL::selectOneValue("SELECT AVG(Nbr_Avis) as 'nb' FROM store");
}

$reqFacture = MYSQL::query("SELECT YEAR(date) AS `year`, MONTH(date) AS `month`, COUNT(*) AS `count` FROM facture WHERE statut = 2 GROUP BY YEAR(date), MONTH(date);");
while ($r = mysqli_fetch_object($reqFacture)) {
  $tplDashboard->bloc('FACTURES', array(
    'COUNT' => $r->count,
    'YEAR' => $r->year,
    'MONTH' => $r->month
  ));
}

$banque = "";

function extract_numbers(string $string)
{
   preg_match_all('/([\d]+)/', $string, $match);
   return $match[0];
}

$req = MYSQL::query("SELECT * FROM `historique` WHERE idType_historique = 20 ORDER BY id DESC LIMIT 5");
$i = 0;
while ($r = mysqli_fetch_object($req)) {
    $factureId = $r->isAction && extract_numbers($r->isAction) ? extract_numbers($r->isAction)[0] : '';
    $amount = MYSQL::selectOneValue("SELECT total_order FROM facture WHERE FID = '{$factureId}'");
    $banque .= "<div class='row'>
    <div class='col-3 pt-1 pb-1'>{$r->isDate}</div>
    <div class='col-3 pt-1 pb-1'>{$r->memb___id}</div>
    <div class='col-4 pt-1 pb-1'><a href='/Administration/Facture/{$factureId}'>Facture {$factureId}</a></div>
    <div class='col-2 pt-1 pb-1 color-green'>{$amount}€</div>
    </div>";

    $i++;
    if ($i < 5)
      $banque .= "<hr/>";
}

$tplDashboard->values(array(
    'BANQUE' => $banque,
    'NB_CLIENT' => $Client,
    'NB_PROSPECT' => $Prospet,
    'NB_FOURNISSEUR' => $Fournisseur,
    'NB_CONTACT' => $Contact,
    'NB_MEMBRES' => $Membres,
    'NB_VISITE' => $Cpt,
    'NB_VISITE_DIFF' => $Cpt_d,
    'CA' => $Ca,
    'NB_FACTURE' => $Facture,
    'NB_CONSULT' => $Nb_consult,
    'NB_ACHAT' => $Nb_achat,
    'NB_AVIS' => $Nb_avis,

));

// Number of Members by month
$labelsMembers = "";
$dataMembers = "";
if (ManageRights::verifyRights('Membres', 'Read', false, false)) {
  $tplDashboard->bloc('IF_MEMBERS');
  $req = MYSQL::query("SELECT count(*) as members, YEAR(Date_Inscription) as year, MONTH(Date_Inscription) as month FROM accounts GROUP BY year, month ORDER BY year, month");
  while ($r = mysqli_fetch_object($req)) {
    $labelsMembers .= "'{$r->year}-{$r->month}',";
    $dataMembers .= "'{$r->members}',";
  }
}


$labelsContacts = "";
$dataContacts = "";
$labelsContactsType = "";
$dataContactsType = "";
if (ManageRights::verifyRights('Contacts', 'Read', false, false)) {
    $tplDashboard->bloc('IF_CONTACTS');
    // Number of Contacts by Month
    $req = MYSQL::query("SELECT count(*) as contacts, YEAR(creation_date) as year, MONTH(creation_date) as month FROM contacts GROUP BY year, month ORDER BY year, month");
    while ($r = mysqli_fetch_object($req)) {
        $labelsContacts .= "'{$r->year}-{$r->month}',";
        $dataContacts .= "'{$r->contacts}',";
    }

    // Type of Contacts
    $req = MYSQL::query("SELECT count(*) as cat, categorie FROM contacts GROUP BY categorie");
    while ($r = mysqli_fetch_object($req)) {
        $labelsContactsType .= "'{$r->categorie}',";
        $dataContactsType .= "'{$r->cat}',";
    }
}


// Money Op
$money = 0;
if (ManageRights::verifyRights('Facture', 'Read', false, false)) {
    $tplDashboard->bloc('IF_FACTURE');
    $filter = ['acquitee' => true, 'FID' => '`ID`'];
    $facts = Generique::select('facture', 'graphene_bsm', $filter, 'date_acquitee ASC');

    foreach ($facts as $f) {
      if ($f->getTotal_order() > 0 && $f->getDate_acquitee()) {
        $money += $f->getTotal_order();
        $f->money = $money;
      }
    }
    $facts = array_reverse($facts);

    foreach ($facts as $f) {
      if ($f->getTotal_order() > 0 && $f->getDate_acquitee()) {
          $tplDashboard->bloc('IF_FACTURE.OP', [
          'DATE' => UTILS::date($f->getDate_acquitee()),
          'MEMB' => $f->getMoyenPaiement(),
          'FACTURE' => "<a href='/Administration/Facture/".$f->getFID()."'>Facture ".$f->getNumeroFactureOrFID()."</a>",
          'AMOUNT' => UTILS::price($f->getTotal_order(), true),
          'MONEY' => UTILS::price($f->money, true)
        ]);
      }
    }
}

// User-Member plateform activity (full)
if (ManageRights::verifyRights('Personnel', 'Read', false, false)) {
    $tplDashboard->bloc('IF_ACTIV');
    $req = MYSQL::query("SELECT * FROM historique ORDER BY id DESC LIMIT 10");
    while ($r = mysqli_fetch_object($req)) {
        $tplDashboard->bloc('IF_ACTIV.ACTIV', [
          'DATE' => UTILS::date($r->isDate),
          'MEMBRE' => $r->memb___id,
          'ACTION' => ($r->link == "" ? html_entity_decode($r->isAction) : '<a href="'.$r->link.'">'.html_entity_decode($r->isAction).'</a>')
        ]);
    }
}


// Number of Facture / devis
$req = MYSQL::query("SELECT count(*) as facture, statut, YEAR(date) as year, MONTH(date) as month FROM facture GROUP BY YEAR(date), MONTH(date), statut ORDER BY YEAR(date), MONTH(date), statut");
$labelsFacture = "";
$dataFacture = "";
$dataDevis = "";
$statut = "";
$month = "";
while ($r = mysqli_fetch_object($req)) {
  if ($month != $r->month) {
    $labelsFacture .= "'{$r->year}-{$r->month}' ,";
  }
  if (in_array($r->statut, [DEVIS_DRAFT, DEVIS_SAVED, DEVIS_VALIDATED, DEVIS_LOST, DEVIS_AVENANT])) {
    $dataDevis .= "'{$r->facture}' ,";
    if ($statut === 0) {
      $dataFacture .= "'0' ,";
    }
  } else {
    if ($month != $r->month) {
      if ($statut == 1)
        $dataDevis .= "'0' ,";
    }
    $dataFacture .= "'{$r->facture}' ,";
  }
  $month = $r->month;
  $statut = $r->statut;
}

// Number of Articles
$labelsArticles = "";
$dataArticles = "";
$labelsVideos = "";
$dataVideos = "";
$labelsMessages = "";
$dataMessages = "";
if (ManageRights::verifyRights('Articles & Vidéos', 'Read', false, false)) {
    $tplDashboard->bloc('IF_ARTICLES');
    $req = MYSQL::query("SELECT count(*) as articles, YEAR(date) as year, MONTH(date) as month FROM articles GROUP BY YEAR(date), MONTH(date)");
    while ($r = mysqli_fetch_object($req)) {
        $labelsArticles .= "'{$r->year}-{$r->month}',";
        $dataArticles .= "'{$r->articles}',";
    }

    // Number of Videos
    $req = MYSQL::query("SELECT count(*) as videos, YEAR(date) as year, MONTH(date) as month FROM videos GROUP BY YEAR(date), MONTH(date)");
    while ($r = mysqli_fetch_object($req)) {
        $labelsVideos .= "'{$r->year}-{$r->month}',";
        $dataVideos .= "'{$r->videos}',";
    }

    // Type of Messages
    $req = MYSQL::query("SELECT count(*) as profil_news, YEAR(date) as year, MONTH(date) as month FROM profil_news GROUP BY YEAR(date), MONTH(date)");
    while ($r = mysqli_fetch_object($req)) {
        $labelsMessages .= "'{$r->year}-{$r->month}',";
        $dataMessages .= "'{$r->profil_news}',";
    }
}

// Last Factures
if (ManageRights::verifyRights('Devis', 'Read', false, false) || ManageRights::verifyRights('Facture', 'Read', false, false)) {
    $tplDashboard->bloc('IF_FACTURE_DEVIS');
    $req = MYSQL::query("SELECT * FROM facture WHERE id = FID ORDER BY id DESC LIMIT 5");
    while ($r = mysqli_fetch_object($req)) {
        if ($r->id_tier) {
            $filterTier = ["id" => $r->id_tier];
            $tier = Generique::selectOne('tiers', 'graphene_erp', $filterTier);
            if ($tier) {
                $nom = $tier->getDenomination();
            }
        } else {
            $nom = "{$r->nom} {$r->prenom}";
        }
        $tplDashboard->bloc('IF_FACTURE_DEVIS.LIST', [
          'ID' => $r->FID,
          'DATE' => date('d/m/Y', strtotime($r->date)),
          'STATUT' => $r->statut == 0 || $r->statut == 1 ? '<span style="color:#2DE7ED">Devis</span>' : '<span style="color:#A1066F">Facture</span>',
          'TOTAL' => UTILS::price($r->total_cart, true),
          'CLIENT' => $nom,
          'ACQUITEE' => $r->acquitee == 1 ? '<span class="color-green">Oui</span>' : '<span class="color-red">Non</span>'
      ]);
    }
}

function createNews($title, $link, $media) {
  return array('title' => html_entity_decode($title), 'link' => $link, 'media' => $media);
}

$member = USER::getId();
$req = MYSQL::query("SELECT Nom, Prenom, id_client FROM accounts WHERE id_client='{$member}'");
$account = mysqli_fetch_object($req);

if (FACEBOOK::isEnabled())
    $tplDashboard->values(array('FACEBOOK_CHECKBOX' => FACEBOOK::isEnabled() ? '' : 'title="Merci de connecter votre page Facebook dans les paramètres" disabled'));
if (LINKEDIN::isEnabled())
    $tplDashboard->values(array('LINKEDIN_CHECKBOX' => LINKEDIN::isEnabled() ? '' : 'title="Merci de connecter votre page Linkedin dans les paramètres" disabled'));

$TITRE = "Dashboard";
$DESCRIPTION = "Accueil administration";
$alias = UTILS::getFunction('Alias');
$tplDashboard->values(array(
  'LABELS_MEMBERS' => substr($labelsMembers, 0, -1),
  'DATA_MEMBERS' => substr($dataMembers, 0, -1),
  'LABELS_CONTACTS' => substr($labelsContacts, 0, -1),
  'DATA_CONTACTS' => substr($dataContacts, 0, -1),
  'LABELS_CONTACTS_TYPE' => substr($labelsContactsType, 0, -1),
  'DATA_CONTACTS_TYPE' => substr($dataContactsType, 0, -1),
  'LABELS_FACTURE' => substr($labelsFacture, 0, -1),
  'DATA_FACTURE' => substr($dataFacture, 0, -1),
  'DATA_DEVIS' => substr($dataDevis, 0, -1),
  'LABELS_ARTICLES' => substr($labelsArticles, 0, -1),
  'DATA_ARTICLES' => substr($dataArticles, 0, -1),
  'LABELS_VIDEOS' => substr($labelsVideos, 0, -1),
  'DATA_VIDEOS' => substr($dataVideos, 0, -1),
  'LABELS_MESSAGES' => substr($labelsMessages, 0, -1),
  'DATA_MESSAGES' => substr($dataMessages, 0, -1),
  'SOLDE' => $money,
  'TIME' => UTILS::date('', 'H:i:s'),
  'DATE' => UTILS::date('', 'l d  F Y'),
  'SITENAME' => UTILS::getFunction('SiteName'),
  'ALIAS' => $alias ? $alias : 'Graph',
  'NOM_USER' => $account->Nom . ' ' . $account->Prenom,
  'ID_MEMBRE' => $account->id_client
));
$PAGES = $tplDashboard->construire('adminDashboard');
