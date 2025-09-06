<?php
ManageRights::verifyRights('Web', 'Read');
$writePermission = ManageRights::verifyRights('Web', 'Write', false, false);
if (!$writePermission) {
  $DISABLE_FORM = true;
}
$tplPage = new Template;
$tplPage->setFile('page', './_admin/componants/AddPage/page.html');

$pageUrl = DATA::getGet('act');
$query = MYSQL::query("SELECT * FROM user_custom_page WHERE `url` = '{$pageUrl}'");
$result = mysqli_fetch_object($query);

$site = UTILS::getFunction('StaticUrl');
if ($writePermission) {
    if (DATA::isPost('add-liste')) {
        if ('laou.eu' == $site && 4 == $result->id) {
            MYSQL::query("INSERT INTO onepage (`page`, label, title, `description`, `image`, `button`) VALUES ('{$result->id}', 'LISTE', ".'\''.UTILS::randomTitle().'\', \''.UTILS::randomText().'\', \''.UTILS::randomImage().'\', "dessin")');
        } else {
            MYSQL::query("INSERT INTO onepage (`page`, label, title, `description`, `image`) VALUES ('{$result->id}', 'LISTE', ".'\''.UTILS::randomTitle().'\', \''.UTILS::randomText().'\', \''.UTILS::randomImage().'\')');
        }
        UTILS::notification('success', 'Un élément a été ajouté avec succès.');

    }

    if (DATA::isPost('deleteElem')) {
        $nb = MYSQL::selectOneValue("SELECT count(*) FROM onepage WHERE label = 'LISTE' AND `page` = {$result->id}");
        if (1 == $nb) {
            UTILS::notification('danger', 'Une liste doit toujours possédé au moins un élément. La suppression ne sera pas effectué');
        } else {
            UTILS::Alert('danger', 'Suppression d\'un élément, Voulez-vous vraiment supprimer cet élément ?', 'L\'action sera irréversible', $_SERVER['REQUEST_URI'], 'deleteOKElem', DATA::getPost('deleteElem'));
        }
    }

    if (DATA::isPost('deleteOKElem')) {
        MYSQL::query('DELETE FROM onepage WHERE id = \''.DATA::getPost('deleteOKElem').'\'');
        UTILS::notification('success', 'Élément supprimé avec succès');
    }

    if (DATA::isPost('deleteBanner')) {
      MYSQL::query("UPDATE user_custom_page SET `image` = NULL WHERE id = '{$result->id}'");
      exit;
    }

    // Add edit image and remove editor
    if (DATA::isPost('editInformations')) {
        $page_group = DATA::getPost('page_group');
        $page_group = $page_group ? "'{$page_group}'" : 'NULL';
        $sort_order = DATA::getPost('sort_order');
        $url = DATA::getPost('url');
        $title = DATA::getPost('titre');
        $description = DATA::getPost('description');
        if ($image = IMAGE::upload($_FILES['logo'], "site/images", $pageUrl, 1366)) {
            $image = ", `image` = '{$image}'";
        }
        $pageName = DATA::getPost('PageName');
        $visibility = DATA::getPost('visibility') == 'visible' ? 'true' : 'false';
        MYSQL::query("UPDATE user_custom_page SET `Titre` = '{$title}', `Description` = '{$description}', `PageName` = '{$pageName}', `visibility` = {$visibility} {$image}, page_group = {$page_group}, sort_order = '{$sort_order}', `url` = '{$url}' WHERE id = '{$result->id}'");
        UTILS::addHistory(USER::getPseudo(), 23, "Page « {$title} » modifiée", "/site/{$url}");
        UTILS::notification('success', 'Page modifiée avec succès.');
    }

    if (DATA::isPost('id') && DATA::isPost('edit')) {
        $id = DATA::getPost('id');
        $title = DATA::getPost('title');
        $description = DATA::getPost('description');
        $image = $video = $button = $image_alt = $english = "";

        if (isset($_FILES['image']) && $image = IMAGE::upload($_FILES['image'], "site/images", $pageUrl, 1366)) {
            $image = ", `image` = '{$image}'";
        }
        if (isset($_FILES['video'])) {
          $handle = new upload($_FILES['video'], 'fr_FR');
          if ($handle->uploaded) {
            $handle->allowed = ['video/*'];
            $handle->file_new_name_ext = 'mp4';
            $handle->process('site/videos/');
            $vid = '/site/videos/'.$handle->file_dst_name;
            $video = ", `video` = '".$vid."'";
          }
        }

        if (DATA::issetPost('button')) {
            $button = ", `button` = '".DATA::getPost('button')."'";
        }

        if (DATA::issetPost('image_alt')) {
            $image_alt = ", `image_alt` = '".DATA::getPost('image_alt')."'";
        }

        if (DATA::isPost('title-EN') && DATA::isPost('description-EN')) {
          $english = ", `title_en` = '".DATA::getPost('title-EN')."', `description_en` = '".DATA::getPost('description-EN')."' ";
        }
        MYSQL::query("UPDATE `onepage` SET `title` = '{$title}', `description` = '{$description}' {$image} {$video} {$button} {$image_alt} {$english} WHERE id = '{$id}' AND page = '{$result->id}'");
        UTILS::notification('success', 'Page modifiée avec succès.');
    }
}

$pageUrl = DATA::getGet('act');
$query = MYSQL::query("SELECT * FROM user_custom_page WHERE `url` = '{$pageUrl}'");
$result = mysqli_fetch_object($query);
if (!$result) {
  header('Location: /Administration/Site');
  exit;
}

$pages = MYSQL::query("SELECT id, PageName FROM user_custom_page WHERE page_group IS NULL AND id != '{$result->id}'");
$page_group = "<option></option>";
while ($p = mysqli_fetch_object($pages)) {
  $selected = $p->id == $result->page_group ? "selected" : "";
  $page_group .= "<option value='{$p->id}' {$selected}>{$p->PageName}</option>";
}

$tplPage->values(array(
  'URL' => $_SERVER['REQUEST_URI'],
  'PAGENAME' => $result->PageName,
  'VISIBILITY_TRUE' => $result->visibility ? 'checked' : '',
  'VISIBILITY_FALSE' => !$result->visibility ? 'checked' : '',
  'TITRE' => $result->Titre,
  'DESCRIPTION' => $result->Description,
  'IMAGE' => $result->image,
  'PAGE_URL' => $pageUrl,
  'SORT_ORDER' => $result->sort_order,
  'PAGE_GROUP' => $page_group,
  'FULLPAGE' => $result->template == 'Vierge' ? 'Fullpage' : ''
));



$tplPage->bloc('EDIT');
$query = MYSQL::query("SELECT * FROM onepage WHERE `page` = $result->id");
$tmp = "";
while ($req = mysqli_fetch_object($query)) {
  if (!$req->title)
    $req->title = " ";
  if (!$req->description)
    $req->description = " ";
  $tplPage->bloc('EDIT.MODULE', [
    'LABEL' => $req->label,
    'TITLE' => $req->title,
    'DESCRIPTION' => strip_tags(html_entity_decode($req->description)),
    'DESCRIPTION_HTML' => preg_replace('/<br>+/', "\n", $req->description),
    'IMAGE' => $req->image,
    'IMAGE_ALT' => $req->image_alt,
    'VIDEO' => $req->video,
    'BUTTON' => $req->button,
    'ID' => $req->id,
  ]);

  if (UTILS::isModuleActive('ENGLISH')) {
    $tplPage->bloc('EDIT.MODULE.ENGLISH', [
      'TITLE' => $req->title_en ?? $req->title,
      'DESCRIPTION' => $req->description_en ? strip_tags(html_entity_decode($req->description_en)) : strip_tags(html_entity_decode($req->description)),
      'DESCRIPTION_HTML' => $req->description_en ? preg_replace('/<br>+/', "\n", $req->description_en) : preg_replace('/<br>+/', "\n", $req->description),
    ]);
  }
  $tmp = $req->label;
  if ($tmp == 'LISTE')
    $tplPage->bloc('EDIT.MODULE.DELETE');
}
if ($tmp == 'LISTE')
  $tplPage->bloc('EDIT.MODULE.ADD');

$TITRE = 'Page '.DATA::getGet('act');
$DESCRIPTION = "Gestion de page du site";
$listMenuArray = [
  ['Web', '/Administration/Site'],
  [$TITRE, '', true]
];
$tplPage->values([
  'FIL_ARIANNE' => MENU::filArianne($listMenuArray, 'com')
]);
if (DATA::isGet('act')) {
  $pageUrl = DATA::getGet('act');
  $ACTION_MENU = createActionMenu([['href' => "/{$pageUrl}", 'icon' => 'visibility', 'title' => "Voir la vidéo" ], ['href' => 'https://www.pexels.com/popular-searches', 'icon' => 'collections', 'title' => 'Photos', 'target' => '_blank'], ['href' => 'https://www.pexels.com/videos', 'icon' => 'movie', 'title' => 'Vidéos', 'target' => '_blank']]);
} else {
  $ACTION_MENU = createActionMenu([['href' => 'https://www.pexels.com/popular-searches', 'icon' => 'collections', 'title' => 'Photos', 'target' => '_blank'], ['href' => 'https://www.pexels.com/videos', 'icon' => 'movie', 'title' => 'Vidéos', 'target' => '_blank']]);
}
$PAGES = $tplPage->construire('page');
