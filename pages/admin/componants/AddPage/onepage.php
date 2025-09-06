<?php
$tplOnePage = new Template;
$tplOnePage->setFile('OnePage', './_admin/componants/AddPage/onepage.html');

$query = MYSQL::query("SELECT Html, Titre, Description, image FROM modulespages WHERE NameModule = 'OnePage'");
$result = mysqli_fetch_object($query);

$tplOnePage->values(array(
  'URL' => $_SERVER['REQUEST_URI'],
  'PAGE' => $result->Html,
  'MODULE' => $module,
  'TITRE' => $result->Titre,
  'DESCRIPTION' => $result->Description,
  'LOGO' => $result->image
));

// Add edit image and remove editor
if (DATA::isPost('titre') && DATA::isPost('description')) {
  $titre = DATA::getPost('titre');
  if ($image = IMAGE::upload($_FILES['logo'], "site/landingPage/images", $titre)) {
      $image = ", `image` = '{$image}'";
  }
  MYSQL::query('UPDATE modulespages SET Titre = \'' . DATA::getPost('titre') . '\', Description = \'' . DATA::getPost('description') . '\''.$image.' WHERE NameModule = "OnePage"');
  UTILS::notification('success', 'Page modifiée avec succès.', false, true);
  header("Refresh:0");
  exit;
}
if (DATA::isGet('act')) {
  $tplOnePage->bloc('EDIT');
  $query = MYSQL::query("SELECT * FROM onepage WHERE page = 0");
  while ($req = mysqli_fetch_object($query)) {
    $tplOnePage->bloc('EDIT.MODULE', array(
      'LABEL' => $req->label,
      'TITLE' => $req->title,
      'DESCRIPTION' => preg_replace('/<br>+/', "\n", $req->description),
      'IMAGE' => $req->image,
      'VIDEO' => $req->video,
      'BUTTON' => $req->button
    ));
  }
} else {
  $tplOnePage->bloc('VIEW');
}

if (DATA::isPost('label') && DATA::isPost('title') && DATA::isPost('description')) {
  $label = DATA::getPost('label');
  $title = DATA::getPost('title');
  $description = preg_replace('/\s\s+/', '<br>', DATA::getPost('description'));
  if ($image = IMAGE::upload($_FILES['image'], "site/landingPage/images", $title)) {
      $image = ", `image` = '{$image}'";
  }
  $handle = new upload($_FILES['video'], 'fr_FR');
  if ($handle->uploaded) {
      $handle->allowed = array('video/*');
      $handle->file_new_name_ext = 'mp4';
      $handle->process('site/landingPage/videos/');
      $vid = '/site/landingPage/videos/'.$handle->file_dst_name;
      $video = ", `video` = '".$vid."'";
  }
  if (DATA::getPost('button'))
    $button = ", `button` = '".DATA::getPost('button')."'";
  MYSQL::query("UPDATE `onepage` SET `title` = '{$title}', `description` = '{$description}' {$image} {$video} {$button} WHERE label = '{$label}'");
  UTILS::notification('success', 'Page modifiée avec succès.', false, true);
  header("Refresh:0");
  exit;
}

$PAGES = $tplOnePage->construire('OnePage');
$TITRE = "OnePage";
$DESCRIPTION = "Gestion de la nouvelle page";
