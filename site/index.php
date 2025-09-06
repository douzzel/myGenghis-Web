<?php
spl_autoload_register(
  function ($x) {
    $sources = array('../classes/' . str_replace('_', '/', $x) . '.class.php'); // chargement des classes
    foreach ($sources as $source) {
      if (file_exists($source)) {
        require_once $source;
      }
    }
		$sources = array('../classes/entities/' . str_replace('_', '/', $x) . '.class.php'); // chargement des classes
		foreach ($sources as $source) {
			if (file_exists($source)) {
				require_once $source;
			}
		}
  }
);

$templateId = false;

$_SESSION["storetype"] = "external";

$description = "";
$site = UTILS::getFunction('StaticUrl');
if ($site == 'laou.eu') {
  if (!DATA::isPost('honeypot') && DATA::isPost('name') && DATA::isPost('email') && DATA::isPost('message')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/securimage/securimage.php';
    $securimage = new Securimage();
    if ($securimage->check(DATA::getPost('captcha_code')) == false) {
      $data = ['response' => false, 'json' => ['error_message' => 'Le code de sécurité est incorrect.']];
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
    }
    //creation des variables
    $nom =DATA::getPost("name");
    $emailclient= DATA::getPost("email");
    $messageclient= DATA::getPost("message");
    $url = UTILS::getFunction('StaticUrl');
    if (strpos($emailclient, $url) !== false || !preg_match('/^[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+$/', $emailclient)) {
      $data = ['response' => false, 'json' => ['error_message' => 'Veuillez saisir un email valide.']];
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
    }

    // variables inchangée
    $subject = "[".UTILS::getFunction('SiteName')."] Nouveau message en provenance de {$url}";
    $email = UTILS::getFunction('WebmasterEmail');
    $message = "{$nom} souhaite vous faire parvenir un message :<br/>
    {$messageclient}
    <br/><br/>
    Informations complémentaires :<br/>
    E-Mail : <a href='mailto:{$emailclient}'>{$emailclient}</a>";
    $content = UTILS::tplMail(false, false, $subject, $message, 'column');
    UTILS::MAIL($email, $subject, $content, NULL, NULL, NULL, NULL, NULL, $emailclient, $nom);
    $data = ['response' => true];
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
} else {
  if (!DATA::isPost('honeypot') && DATA::isPost('captcha_code') && DATA::isPost('fname') && DATA::isPost('lname') && DATA::isPost('adr') && DATA::isPost('ville') && DATA::issetPost('telfixe') && DATA::isPost('email') && DATA::isPost('message')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/securimage/securimage.php';
    $securimage = new Securimage();
    if ($securimage->check(DATA::getPost('captcha_code')) == false) {
      $data = ['response' => false, 'json' => ['error_message' => 'Le code de sécurité est incorrect.']];
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
    }

    if (!isset($_SERVER['HTTP_REFERER']) || (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['SERVER_NAME'])) {
      exit;
    }

    //creation des variables
    $denomination = DATA::getPost('denomination');
    $siret = DATA::getPost('siret');
    $tva = DATA::getPost('tva');
    $bp = DATA::getPost('bp');
    $company = "";
    if ($denomination || $siret || $tva) {
      $company = "Socitété {$denomination} <br/> Siret {$siret} <br/> Nº TVA {$tva}<br/>";
    }

    $prenom = DATA::getPost("fname");
    $nom =DATA::getPost("lname");
    $adr = DATA::getPost("adr");
    $ville = DATA::getPost("ville");
    $telfixe = DATA::getPost("telfixe");
    $telportable = DATA::isPost('telportable') ? DATA::getPost("telportable") : '';
    $emailclient = DATA::getPost("email");
    $messageclient= DATA::getPost("message");
    $url = UTILS::getFunction('StaticUrl');
    $test1 = preg_match('/^[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+$/', $emailclient);
    $test2 = strpos($emailclient, $url) !== false;
    if (strpos($emailclient, $url) !== false || !preg_match('/^[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+$/', $emailclient)) {
      // TODO -> add no-reply noreply nepasrepondre ne-pas-repondre
      $data = ['response' => false, 'json' => ['error_message' => 'Veuillez saisir un email valide.']];
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
    }

    if (preg_match('/http|www/i', $messageclient)) {
      $data = ['response' => false, 'json' => ['error_message' => 'Les urls sont interdites dans les messages.']];
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
    }

    if (strlen($messageclient) > 200) {
      $data = ['response' => false, 'json' => ['error_message' => 'Votre message est trop long. Il doit faire moins de 200 caractères.']];
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
    }

    // variables inchangée
    $subject = "[".UTILS::getFunction('SiteName')."] Nouveau message en provenance de {$url}";
    $email = UTILS::getFunction('WebmasterEmail');
    $message = "{$nom} {$prenom} souhaite vous faire parvenir un message :<br/>
    {$company}
    {$messageclient}
    <br/><br/>
    Informations complémentaires :<br/>
    Téléphones : <a href='tel:{$telfixe}'>{$telfixe}</a> <a href='tel:{$telportable}'>{$telportable}</a><br/>
    Adresse : {$adr} {$ville} <br/> {$bp} <br/>
    E-Mail : <a href='mailto:{$emailclient}'>{$emailclient}</a>";
    $content = UTILS::tplMail(false, false, $subject, $message, 'column');
    /*/
    UTILS::MAIL($email, $subject, $content, NULL, NULL, NULL, NULL, NULL, $emailclient, "$nom {$prenom}");
    /*/
    UTILS::MAIL($email, $subject, $content, NULL, NULL, true, NULL, NULL, $emailclient, "$nom {$prenom}"); // also saves copy of contact-form email to SENT
    //*/
    $data = ['response' => true];
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
}

// * Make a reservation
if (!DATA::isPost('honeypot') && DATA::isPost(['captcha_code', 'client_name', 'email', 'res_date', 'res_time', 'res_loc'])) {
  include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/securimage/securimage.php';
  $securimage = new Securimage();
  if ($securimage->check(DATA::getPost('captcha_code')) == false) {
    $data = ['response' => false, 'json' => ['error_message' => 'Le code de sécurité est incorrect.']];
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }

  if (!isset($_SERVER['HTTP_REFERER']) || (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['SERVER_NAME'])) {
    exit;
  }

  $email = DATA::getPost('email');
  $client_name = DATA::getPost('client_name');
  $time = DATA::getPost('res_time');

  $data = ['location_id' => DATA::getPost('res_loc'), 'client_name' => $client_name, 'email' => $email, 'res_date' => UTILS::date(DATA::getPost('res_date'), 'Y-m-d'), 'res_time' => $time];
  if (DATA::isPost('comments'))
    $data['comments'] = DATA::getPost('comments');
  Generique::insert('reservation_list', 'graphene_bsm', $data);

  $loc = Generique::selectOne('reservation_location', 'graphene_bsm', ['id' => DATA::getPost('res_loc')]);

  // Send Mail
  $siteName = UTILS::getFunction('SiteName');
  $date = UTILS::date(DATA::getPost('res_date'), 'd/m/Y');
  $subject = "[{$siteName}] Réservation {$loc->getName()} le {$date} à {$time}";
  $message = "Bonjour,<br/><br/>
  vous avez réservé au nom de {$client_name} pour <b>{$loc->getName()}</b> le <b>{$date}</b> à <b>{$time}</b><br/><br/>
  Adresse : <a href='https://www.openstreetmap.org/search?query={$loc->getAddress()}' target='_blank'>{$loc->getAddress()}</a>";
  if (DATA::isPost('comments') && !DATA::getPost('hide_comments')) {
    $message .= "<br/><br/>Commentaire : ".DATA::getPost('comments');
  }

  $ics = new ICS([
    'summary' => "[{$siteName}] {$loc->getName()}",
    'description' => DATA::getPost('hide_comments') ? '' : DATA::getPost('comments'),
    'dtstart' => DATA::getPost('res_date') . ' ' . DATA::getPost('res_time'),
    'dtend' => date('c', strtotime(DATA::getPost('res_date') . ' ' . DATA::getPost('res_time')) + ($loc->getDuration() * 60)),
    'location' => $loc->getAddress(),
  ]);
  $content = UTILS::tplMail(false, false, $subject, $message, 'column');
  UTILS::MAIL($email, $subject, $content, $ics->to_string());

  $res_id = Generique::selectMaxId('reservation_location', 'graphene_bsm');
  UTILS::addHistory('site', 52, "Nouvel réservation nº{$res_id} « {$loc->getName()} » depuis le site", "/Administration/Reservations#res_{$res_id}");

  $data = ['response' => true, 'data' => ['res_date' => $date, 'res_time' => $time, 'loc_name' => $loc->getName(), 'loc_address' => $loc->getAddress()]];
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

function minutes($time){
  $time = explode(':', $time);
  return $time && $time[0] ? ($time[0]*60) + ($time[1]) + (isset($time[2]) ? ($time[2]/60) : 0) : 0;
}

if (DATA::isPost('reservationLocationSelect')) {
  $filter = ['id' => DATA::getPost('reservationLocationSelect')];
  $loc = Generique::selectOne('reservation_location', 'graphene_bsm', $filter);

  $data = ['days' => $loc->getDays()];
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

if (DATA::isPost(['reservationDate', 'reservationLocation'])) {
  $filter = ['id' => DATA::getPost('reservationLocation')];
  $loc = Generique::selectOne('reservation_location', 'graphene_bsm', $filter);

  $takenSlot = [];
  $date = DATA::getPost('reservationDate');
  $req = MYSQL::query("SELECT count(*) as res_count, res_time FROM reservation_list WHERE location_id = '{$loc->getId()}' AND res_date = '{$date}' GROUP BY res_time");
  $max_res = $loc->getNumber();
  while ($r = mysqli_fetch_object($req)) {
    if ($r->res_count >= $max_res)
      $takenSlot[] = $r->res_time;
  }
  $slot = [];

  if ($loc) {
    $max_minute = minutes($loc->getH_max());
    $min_minute = minutes($loc->getH_min());
    if (!$max_minute)
      $max_minute = 1440;
    if (!$min_minute)
      $min_minute = 0;
    for ($i = $min_minute; $i < $max_minute - $loc->getDuration(); $i += $loc->getDuration()) {
      $hours = floor($i / 60);
      $minutes = $i % 60;
      $minutes = $minutes < 10 ? "0{$minutes}" : $minutes;
      $time = "{$hours}:{$minutes}";
      if (!in_array($time, $takenSlot))
        $slot[] = $time;
    }

    if ($loc->getH_min2() && $loc->getH_max2()) {
      for ($i = minutes($loc->getH_min2()); $i < minutes($loc->getH_max2()) - $loc->getDuration(); $i += $loc->getDuration()) {
        $hours = floor($i / 60);
        $minutes = $i % 60;
        $minutes = $minutes < 10 ? "0{$minutes}" : $minutes;
        $time = "{$hours}:{$minutes}";
        if (!in_array($time, $takenSlot))
          $slot[] = $time;
      }
    }

    if ($loc->getH_min3() && $loc->getH_max3()) {
      for ($i = minutes($loc->getH_min3()); $i < minutes($loc->getH_max3()) - $loc->getDuration(); $i += $loc->getDuration()) {
        $hours = floor($i / 60);
        $minutes = $i % 60;
        $minutes = $minutes < 10 ? "0{$minutes}" : $minutes;
        $time = "{$hours}:{$minutes}";
        if (!in_array($time, $takenSlot))
          $slot[] = $time;
      }
    }
  }
  array_unique($slot);

  $data = ['slot' => $slot, 'duration' => $loc->getDuration()];
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

$tplPage = new Template;
$tpl = $tplPage;

$TTCInStore = UTILS::getFunction('TTCInStore');
require_once('utils.php');
if ($site == 'mpbat.fr') {
  $query = MYSQL::query("SELECT * FROM onepage WHERE id = '310'");
  if (($req = mysqli_fetch_object($query)) && DATA::getGet('page') != 'Accueil') {
    $tplPage->bloc($req->label, array(
      'TITLE' => html_entity_decode($req->title),
      'DESCRIPTION' => html_entity_decode($req->description)
    ));
  }
} else if ($site == 'laou.eu') {
  if (DATA::isGet('page') && DATA::getGet('page') == 'Accueil') {
    $req = MYSQL::query("SELECT * FROM products LEFT JOIN product_images on products.id = product_images.product_id WHERE p_family LIKE '%|1|%' ORDER BY RAND () LIMIT 9");
    while ($r = mysqli_fetch_object($req)) {
      $tplPage->bloc('PRODUCTS', array(
        'IMAGE' => $r->name_img ? '/themes/assets/images/store/' . $r->name_img : $r->image,
        'NAME' => $r->name,
        'ID' => $r->product_id,
        'DESCRIPTION' => $r->description
      ));
    }
  }
} else if (in_array($site, ['mygenghis.com', 'graphene-bsm.com'])) {
  $url = "https://matomo.graphene-bsm.com/?module=API&method=VisitsSummary.get&idSite=1&period=range&date=2019-01-01,today&format=JSON&token_auth=c4c7c305d5bfa1cb234e885de821007d";

  $fetched = file_get_contents($url);
  $content = json_decode($fetched,true);

  if ($content) {
    $tplPage->value('MG_VISITS', $content["nb_visits"] + $content["nb_actions"]);
  }
}

if (DATA::isGet('page') || DATA::isGet('module') || DATA::isGet('doc')) { // inclusion des pages dans le template
  $pageId = NULL;
  if (DATA::isGet('page')) {
    $urlPage = DATA::getGet('page');
    $pageId = MYSQL::selectOneValue("SELECT id FROM user_custom_page WHERE `url` = '{$urlPage}'");
  }
  $moduleId = DATA::getGet('module');
  $docId = DATA::getGet('doc');
  if (!$pageId && !$moduleId && !$docId) {
    UTILS::addHack('Tentative d\'accès à une page inexistante.');
    header("Location: /404");
    exit;
  }
} else {
  header("Status: 301 Moved Permanently", false, 301);
  header("Location: /");
  exit;
}

// Loading of the page and add the data
if ($pageId) {
  $liste = [];
  $sql = MYSQL::query("SELECT * FROM user_custom_page where id = '{$pageId}'");
  $reqView = mysqli_fetch_object($sql);
  $templateId = $reqView->template;
  $description = $reqView->Description;
  $tplPage->setFile("template", "templates/{$templateId}.html");
  if (!in_array($templateId, ['Accueil_1', 'Accueil_2', 'Liste_3', 'Accueil_contact', 5, 6, 16, 17, 'GBSM_Video', 'MP_index', 'ws-events', 'Page_vide', 'FR_Accueil_1', 'FR_Accueil_1', 'GBSM_7']))
    $tplPage->bloc('IF_SHOWHEADER');
  $sql = MYSQL::query("SELECT * FROM onepage WHERE `page` = '{$pageId}'");

  if (in_array($templateId, ['Reservation_1'])) { // If template reservation
    $locations = Generique::select('reservation_location', 'graphene_bsm');
    foreach ($locations as $r) {
      $tplPage->bloc('RESERVATION_LOCATION', [
          'ID' => $r->getId(),
          'NAME' => $r->getName(),
          'ADDRESS' => $r->getAddress(),
      ]);
    }
  }

  $english = false;
  if (UTILS::getFunction('displayWebMenu') && UTILS::isModuleActive('ENGLISH')) {
    $urlPage = DATA::getGet('page');
    if (DATA::isGet('language') && DATA::getGet('language') == 'en') {
      $english = true;
      $tplPage->bloc('DISPLAY_WEB_MENU.IF_FLAG_FRENCH', ['NAMEPAGE' => $urlPage]);
    } else {
      $tplPage->bloc('DISPLAY_WEB_MENU.IF_FLAG_ENGLISH', ['NAMEPAGE' => $urlPage]);
    }
  }

  while ($req = mysqli_fetch_object($sql)) {
    if ($req->label != 'TESTIMONIAL' || $req->description) // Hide bloc testimonial if there is no data
      $tplPage->bloc($req->label, array(
        'TITLE' => $english && $req->title_en ? html_entity_decode($req->title_en) : html_entity_decode($req->title),
        'DESCRIPTION' => $english && $req->description_en ? html_entity_decode($req->description_en) : html_entity_decode($req->description),
        'IMAGE' => $req->image,
        'IMAGE_ALT' => $req->image_alt,
        'VIDEO' => $req->video,
        'BUTTON' => $req->button,
        'TITLE_CLEAN' => preg_replace('/\s+/', '', $req->title)
      ));
    if ($req->label == 'LISTE') {
      if ($req->button)
        $cat = explode(' ', $req->button);
      else
        $cat = explode(' ', $req->title);
      foreach($cat as $c) {
        array_push($liste, $c);
      }
    }
  }

  $liste = array_unique($liste);
  foreach ($liste as $l) {
    $tplPage->bloc('LISTE_CAT', array('TITLE' => ucwords($l), 'CAT' => $l));
  }

  // clean analytics tracking script code for page insertion
  $trackingCode = UTILS::getFunction('analyticsTracking');
  $trackingCode = str_replace("&rsquo;", "'", $trackingCode);
  $trackingCode = html_entity_decode($trackingCode);
  $trackingCode = str_replace(['<script>', '<script type="text/javascript">', '<!-- Matomo -->', '<!-- End Matomo Code -->', '<!-- Google Analytics -->', '<!-- End Google Analytics -->'], '', $trackingCode);

  $tplPage->values(array(
    'PAGENAME' => $reqView->PageName,
    'TITLE' => $reqView->Titre,
    'PAGE' => UTILS::Encode($tplPage->construire('template')),
    'IMAGE' => $reqView->image,
    'OG_IMAGE' => preg_match("/^(http)/", $reqView->image) ? $reqView->image : UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').$reqView->image,
    'OG_TYPE' => 'website',
    'ANALYTICS_TRACKER' => $trackingCode
  ));
} else if ($moduleId == 'CONTACT') {
  if ($site == 'laou.eu') {
    header('Location: /contact');
    exit;
  }
  if (MYSQL::selectOneValue("SELECT ModuleActive FROM modulespages WHERE NameModule = 'CONTACT'") == 'OFF') {
    header('Location: /');
    exit;
  }
  $tplPage->bloc('IF_SHOWHEADER');
  $tplPage->setFile("contact", "contact.html");

  $sql = MYSQL::query("SELECT * FROM modulespages WHERE NameModule = 'CONTACT'");
  $reqView = mysqli_fetch_object($sql);

  if ($reqView->Texte == "1") {
    $tplPage->bloc('IF_CONTACT_PRO');
    $tplPage->bloc('IF_CONTACT_PRO.IF_REQUIRED_CONTACT_PRO');
  } else if ($reqView->Texte == "2") {
    $tplPage->bloc('IF_CONTACT_PRO');
    $tplPage->bloc('IF_CONTACT_PRO.IF_NOT_REQUIRED_CONTACT_PRO');
  } else {
    $tplPage->bloc('IF_NOT_CONTACT_PRO');
  }

  $tplPage->values(array(
    'PAGENAME' => $reqView->NamePage,
    'TITLE' => $reqView->NamePage,
    'PAGE' => $tplPage->construire('contact'),
    'IMAGE' => $reqView->image,
  ));
} else if ($docId) {
  $tplPage->bloc('IF_SHOWHEADER');
  $sql = MYSQL::query("SELECT * FROM documents WHERE ref = '{$docId}'");
  $reqView = mysqli_fetch_object($sql);
  $page = '<div class="section-block clearfix no-padding mt-100 reset-html-doc">
  <div class="row">'.html_entity_decode($reqView->content).'</div></div>';
  $tplPage->values(array(
    'PAGENAME' => $reqView->title,
    'TITLE' => $reqView->title,
    'PAGE' => $page,
    'IMAGE' => $reqView->image
  ));
}

$desc = MYSQL::selectOneValue("SELECT content FROM documents WHERE ref = 'SITE_DESCRIPTION'");
$tplPage->values(array(
  'DESCRIPTION' => $description ? $description : $desc,
  'SITE_DESCRIPTION' => $desc
));

$buffer = "";
if ($templateId != 'Vierge') {
  $buffer .= UTILS::Encode($tplPage->construire('header'));
  $buffer .= UTILS::Encode($tplPage->construire('page'));
  $buffer .= UTILS::Encode($tplPage->construire('footer'));
} else {
  $buffer .= UTILS::Encode($tplPage->construire('empty_page'));
}
echo UTILS::compressHtml($buffer);
