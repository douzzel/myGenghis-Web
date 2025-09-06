<?php
ManageRights::verifyRights('Articles & Vidéos', 'Read');
$writePermission = ManageRights::verifyRights('Articles & Vidéos', 'Write', false, false);
if (!$writePermission) {
  $DISABLE_FORM = true;
}
$tplVideo = new Template;
$tplVideo->setFile('PB', './_admin/componants/AddPage/video.html');

if (DATA::isGet('act')) {
  $videoId = DATA::getGet('act');
  $query = MYSQL::query("SELECT * FROM videos WHERE id = '{$videoId}'");
  if ($r = mysqli_fetch_object($query));
    $hidden = $r->hidden ? "" : "checked";
    $profil_news = $r->profil_news ? "checked" : "";
    $video = $r->videoLink ? html_entity_decode($r->videoLink) : "<video src='{$r->video}' preload='metadata' controls width='100%'></video>";
    $tplVideo->bloc('VIDEO', array(
      'ID' => $r->id,
      'TITLE' => $r->title,
      'SUMMARY' => $r->summary,
      'MINIATURE' => $r->image ? "<img src='{$r->image}' class='w-100' id='image_new'>" : "<video class='w-100' src='{$r->video}' preload='metadata' id='image_new'></video>",
      'VIDEOLINK' => $r->videoLink,
      'VIDEO' => $r->video,
      'VIDEO_PLAYER' => $video,
      'DATE' => $r->date,
      'HIDDEN' => $hidden,
      'PROFIL_NEWS' => $profil_news,
      'FACEBOOK_LINK' => $r->facebookId
    ));
    $tplVideo->values(array(
      'FACEBOOK' => FACEBOOK::isEnabled() ? ($r->facebookId ? 'checked disabled title="Vidéo déjà publié sur Facebook"' : ($r->videoLink && !$r->video ? 'title="Impossible de publier un iframe sur Facebook" disabled' :'')) : 'title="Merci de connecter votre page Facebook dans les paramètres" disabled'
    ));
    if (!$r->hidden)
      $tplVideo->bloc('VIDEO.IF_VIDEO_LINK');
    if ($r->facebookId)
      $tplVideo->bloc('VIDEO.IF_FACEBOOK_LINK');

      if ($writePermission) {
          if (DATA::isPost('edit')) {
              $title = DATA::getPost('title');
              $summary = DATA::getPost('summary');

              $data = [
                'title' => $title,
                'summary' => $summary,
                'videoLink' => DATA::getPost('videoLink'),
                'hidden' => DATA::getPost('hidden') == 'on' ? NULL : 1,
                'profil_news' => DATA::getPost('profil_news') == 'on' ? 1 : 0,
              ];

              if ($image = IMAGE::upload($_FILES['image'], "site/images", $title, 750)) {
                $data['image'] = $image;
              }

              $handleVideo = new upload($_FILES['video'], 'fr_FR');
              if ($handleVideo->uploaded) {
                  $handleVideo->allowed = ['video/*'];
                  $handleVideo->file_new_name_ext = 'mp4';
                  $handleVideo->process('site/videos/');
                  $data['video'] = "/site/videos/{$handleVideo->file_dst_name}";
              }

              $filter = ['id' => $videoId];
              Generique::update('videos', 'graphene_bsm', $filter, $data);

              $url = UTILS::getFunction('StaticUrl');
              if (!$r->facebookId && 'on' == DATA::getPost('facebook') ? 1 : 0) {
                  $query = MYSQL::query("SELECT * FROM videos WHERE id = '{$videoId}'");
                  $r = mysqli_fetch_object($query);
                  $file = getcwd().$r->video;
                  $files['attachment'] = ['tmp_name' => $file, 'name' => basename($file), 'type' => 'video/mp4', 'error' => 0, 'size' => filesize($file)];
                  $res = FACEBOOK::postVideo($files, $title, $summary);
                  if (false == $res) {
                      UTILS::notification('danger', 'Erreur lors de la publication de la vidéo sur Facebook. Essayez de reconnecter votre page Facebook.', true, true);
                  } else {
                      MYSQL::query("UPDATE videos SET facebookId = '{$res->id}' WHERE id='{$videoId}'");
                      UTILS::notification('success', 'Vidéo ajoutée avec succès et publié sur Facebook', true, true);
                  }
              } else {
                  UTILS::notification('success', 'Vidéo modifiée avec succès.', true, true);
              }
              UTILS::addHistory(USER::getPseudo(), 27, "Vidéo « {$title} » mise à jour");

              exit;
          }

        if (DATA::isPost('deleteVideo')) {
            UTILS::Alert('danger', 'Suppression d\'une vidéo, Voulez-vous vraiment supprimer cette vidéo ?', 'L\'action sera irréversible', $_SERVER['REQUEST_URI'], 'deleteVideoConfirm', DATA::getPost('deleteVideo'));
        }

        if (DATA::isPost('deleteVideoConfirm')) {
            $videoId = DATA::getPost('deleteVideoConfirm');
            $videoTitle = MYSQL::selectOneValue("SELECT title FROM videos WHERE id='{$videoId}'");
            MYSQL::query("DELETE FROM videos WHERE `id` = '{$videoId}'");
            UTILS::notification('success', 'Vidéo supprimé avec succès', false, true);
            UTILS::addHistory(USER::getPseudo(), 27, "Vidéo « {$videoTitle} » supprimée");
            header('location: /Administration/Site');

            exit;
        }
      }

  // Load Video
} else {
  ManageRights::verifyRights('Articles & Vidéos', 'Write');
  $tplVideo->bloc('NEW');
  $tplVideo->values(array(
    'FACEBOOK' => FACEBOOK::isEnabled() ? '' : 'title="Merci de connecter votre page Facebook dans les paramètres" disabled'
  ));

  if (DATA::isPost('new')) {
    $title = DATA::getPost('title');
    $handle2 = new upload($_FILES['video'], 'fr_FR');
    $video = "";
    if ($handle2->uploaded) {
      $handle2->allowed = array('video/*');
      $handle2->file_new_name_ext = 'mp4';
      $handle2->process('site/videos/');
      $video = '/site/videos/'.$handle2->file_dst_name;
    }

    $summary = DATA::getPost('summary');
    $data = [
      'title' => $title,
      'summary' => $summary,
      'videoLink' => DATA::getPost('videoLink'),
      'video' => $video,
      'hidden' => DATA::getPost('hidden') == 'on' ? NULL : 1,
      'profil_news' => DATA::getPost('profil_news') == 'on' ? 1 : 0,
      'image' => IMAGE::upload($_FILES['image'], "site/images", $title, 750) || ''
    ];
    Generique::insert('videos', 'graphene_bsm', $data);
    $videoId = MYSQL::selectOneValue("SELECT max(id) FROM videos");

    $url = UTILS::getFunction('StaticUrl');
    if ((DATA::getPost('newfacebook') == 'on' ? 1 : 0) && $_FILES['video']) {
      $files['attachment'] = $_FILES['video'];
      $res = FACEBOOK::postVideo($files, $title, $summary);
      if ($res == false)
        UTILS::notification('danger', 'Erreur lors de la publication de la vidéo sur Facebook. Essayez de reconnecter votre page Facebook.', false, true);
      else {
        MYSQL::query("UPDATE videos SET facebookId = '{$res->id}' WHERE id='{$videoId}'");
        UTILS::notification('success', 'Vidéo ajoutée avec succès et publié sur Facebook', false, true);
      }
    } else {
      UTILS::notification('success', 'Vidéo ajoutée avec succès.', false, true);
    }
    UTILS::addHistory(USER::getPseudo(), 27, "Nouvelle vidéo « {$title} » ajoutée");
    NOTIFICATIONS::add("web", "Nouvelle vidéo <b>{$title}</b> par " . NOTIFICATIONS::CreateTag(USER::getId()), "Video/{$videoId}", [], "Articles & Vidéos");

    header("Location: Video/{$videoId}");
    exit;
  }
}

$listMenuArray = [
  ['Web', '/Administration/Site'],
  ['Vidéo', '', true]
];
$TITRE = "Vidéo";
$DESCRIPTION = "Modifier une vidéo";
$tplVideo->values(array(
  'TITRE' => $TITRE,
  'DESCRIPTION' => $DESCRIPTION,
  'FIL_ARIANNE' => MENU::filArianne($listMenuArray, 'com')
));
if (DATA::isGet('act')) {
  $videoId = DATA::getGet('act');
  $ACTION_MENU = createActionMenu([
    ['href' => "/Videos/{$videoId}", 'icon' => 'visibility', 'title' => "Voir la vidéo" ],
    ['href' => 'https://www.pexels.com/popular-searches', 'icon' => 'collections', 'title' => 'Photos', 'target' => '_blank'],
    ['href' => 'https://www.pexels.com/videos', 'icon' => 'movie', 'title' => 'Vidéos', 'target' => '_blank'],
    ['submit' => 'deleteVideo', 'icon' => 'delete', 'title' => 'Supprimer', 'value' => $videoId]
  ]);
} else {
  $ACTION_MENU = createActionMenu([
    ['href' => 'https://www.pexels.com/popular-searches', 'icon' => 'collections', 'title' => 'Photos', 'target' => '_blank'],
    ['href' => 'https://www.pexels.com/videos', 'icon' => 'movie', 'title' => 'Vidéos', 'target' => '_blank']
  ]);
}
$PAGES = $tplVideo->construire('PB');
