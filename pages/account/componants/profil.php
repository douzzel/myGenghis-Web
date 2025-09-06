<?php

$tplProfil = new Template();
$tplProfil->setFile('profil', './account/componants/profil.html');

$alias = UTILS::getFunction('Alias');
$alias = $alias ? $alias : 'Graph';

$siteName = UTILS::getFunction('SiteName');

if (DATA::isPost('getArticle')) {
    $articleId = DATA::getPost('getArticle');
    $req = MYSQL::query("SELECT * FROM articles WHERE id = '{$articleId}'");
    $content = '';
    if ($r = mysqli_fetch_object($req)) {
        if ($r->image) {
            $content = "<div class='d-flex justify-content-center'><img src='{$r->image}'></div>";
        }
        $content .= $r->content;
    }
    echo html_entity_decode($content);

    exit;
}

if (DATA::isPost('shareFB')) {
    $type = DATA::getPost('typeNews');
    if ('messages' == $type) {
        if (!empty($_POST) && ManageRights::verifyRights('Messages', 'Write', false)) {
            $postId = DATA::getPost('shareFB');
            $msg = MYSQL::selectOneValue("SELECT `content` FROM profil_news WHERE id = '{$postId}'");
            $res = FACEBOOK::postMessage($msg);
            if ($res) {
                MYSQL::query("UPDATE profil_news SET facebookId = '{$res->id}' WHERE id = '{$postId}'");
                UTILS::notification('success', 'Le message a été partagé sur Facebook', false, true);
            } else {
                UTILS::notification('danger', 'Erreur lors du partage sur Facebook. Essayez de reconnecter votre page Facebook.', false, true);
            }
        }
    } elseif ('articles' == $type) {
        if (!empty($_POST) && ManageRights::verifyRights('Articles & Vidéos', 'Write', false)) {
            $articleId = DATA::getPost('shareFB');
            $url = UTILS::getFunction('StaticUrl');
            $http = UTILS::getFunction('isHttps');
            $articleUrl = $http.'://'.$url.'/Articles/'.$articleId;
            $res = FACEBOOK::postArticles($articleUrl);
            if ($res) {
                MYSQL::query("UPDATE articles SET facebookId = '{$res->id}' WHERE id='{$articleId}'");
                UTILS::notification('success', 'L\'article a été partagé sur Facebook', false, true);
            } else {
                UTILS::notification('danger', 'Erreur lors du partage sur Facebook. Essayez de reconnecter votre page Facebook.', false, true);
            }
        }
    } elseif ('videos' == $type) {
        if (!empty($_POST) && ManageRights::verifyRights('Articles & Vidéos', 'Write', false)) {
            $videoId = DATA::getPost('shareFB');
            $query = MYSQL::query("SELECT * FROM videos WHERE id = '{$videoId}'");
            $r = mysqli_fetch_object($query);
            $file = getcwd().$r->video;
            $files['attachment'] = ['tmp_name' => $file, 'name' => basename($file), 'type' => 'video/mp4', 'error' => 0, 'size' => filesize($file)];
            $res = FACEBOOK::postVideo($files, $r->title, $r->summary);
            if ($res) {
                MYSQL::query("UPDATE videos SET facebookId = '{$res->id}' WHERE id='{$videoId}'");
                UTILS::notification('success', 'La vidéo a été partagé sur Facebook', true, true);
            } else {
                UTILS::notification('danger', 'Erreur lors du partage sur Facebook', true, true);
            }
        }
    }
}

if (DATA::isPost('shareLinkedIn')) {
    $type = DATA::getPost('typeNews');
    if ('messages' == $type) {
        if (!empty($_POST) && ManageRights::verifyRights('Messages', 'Write', false)) {
            $postId = DATA::getPost('shareLinkedIn');
            $content = MYSQL::selectOneValue("SELECT `content` FROM profil_news WHERE id = '{$postId}'");
            $res = LINKEDIN::postMessage($content);
            if ($res) {
                $idPost = isset($res->activity) ? $res->activity : $res->id;
                MYSQL::query("UPDATE profil_news SET linkedinId = '{$idPost}' WHERE id = '{$postId}'");
                UTILS::notification('success', 'Le message a été partagé sur Linkedin', false, true);
            } else {
                UTILS::notification('danger', 'Erreur lors du partage sur Linkedin. Essayez de reconnecter votre page Linkedin.', false, true);
            }
        }
    } elseif ('articles' == $type) {
        if (!empty($_POST) && ManageRights::verifyRights('Articles & Vidéos', 'Write', false)) {
            $articleId = DATA::getPost('shareLinkedIn');
            $title = MYSQL::selectOneValue("SELECT `title` FROM articles WHERE id = '{$articleId}'");
            $articleImg = MYSQL::selectOneValue("SELECT `image` FROM articles WHERE id = '{$articleId}'");
            $url = UTILS::getFunction('StaticUrl');
            $http = UTILS::getFunction('isHttps');
            $articleUrl = $http.'://'.$url.'/Articles/'.$articleId.' ';
            $res = LINKEDIN::postArticles($title, $articleImg, $articleUrl);
            if ($res) {
                $idPost = isset($res->activity) ? $res->activity : $res->id;
                MYSQL::query("UPDATE articles SET linkedinId = '{$idPost}' WHERE id='{$articleId}'");
                UTILS::notification('success', 'L\'article a été partagé sur Linkedin', false, true);
            } else {
                UTILS::notification('danger', 'Erreur lors du partage sur Linkedin. Essayez de reconnecter votre page Linkedin.', false, true);
            }
        }
    } elseif ('videos' == $type) {
        if (!empty($_POST) && ManageRights::verifyRights('Articles & Vidéos', 'Write', false)) {
            UTILS::notification('warning', "Le partage de vidéo sur LinkedIn n'est pas encore disponible", true, true);
        }
    }
}

if (!empty($_POST) && (DATA::isPost('send') || DATA::isPost('deleteNews') || DATA::isPost('deleteOKmessages')) && ManageRights::verifyRights('Messages', 'Write', false)) {
    if (DATA::isPost('send')) {
        $msg = 'Votre message a été publié sur la plateforme';
        $error = '';
        $id_client = DATA::getPost('publish_as');
        $content = DATA::getPost('message');
        $facebook = '';
        $linkedin = '';
        if (DATA::isPost('facebook') && 'on' == DATA::getPost('facebook')) {
            $res = FACEBOOK::postMessage($content);
            if ($res) {
                $msg .= ' et sur Facebook';
                $facebook = ", facebookId = '{$res->id}'";
            } else {
                $error .= 'Erreur lors de la publication sur Facebook. Essayez de reconnecter votre page Facebook.';
            }
        }
        if (DATA::isPost('linkedin') && 'on' == DATA::getPost('linkedin')) {
            $res = LINKEDIN::postMessage($content);
            if ($res) {
                $msg .= ' et sur LinkedIn';
                if (isset($res->activity)) {
                    $linkedin = ", linkedinId = '{$res->activity}'";
                } else {
                    $linkedin = ", linkedinId = '{$res->id}'";
                }
            } else {
                $error = 'Erreur lors de la publication sur LinkedIn. Essayez de reconnecter votre page LinkedIn.';
            }
        }
        MYSQL::query("INSERT INTO profil_news SET id_client = '{$id_client}', content = '{$content}' {$facebook} {$linkedin}");
        if ($error) {
            UTILS::notification('danger', $error, false, true);
        } else {
            UTILS::notification('success', $msg, false, true);
        }
    }

    if (DATA::isPost('deleteNews')) {
        UTILS::Alert('danger', 'Voulez-vous vraiment effectuer cette suppression ?', '', $_SERVER['REQUEST_URI'], 'deleteOK'.DATA::getPost('typeNews'), DATA::getPost('deleteNews'));
    }

    if (DATA::isPost('deleteOKmessages')) {
        $delId = DATA::getPost('deleteOKmessages');
        MYSQL::query("DELETE FROM profil_news WHERE id = '{$delId}'");
        UTILS::notification('success', 'Votre message a été supprimé avec succès.', false, true);
    }
}

if (DATA::isPost('editMessage') && DATA::isPost('editIdMessage') && ManageRights::verifyRights('Messages', 'Write', false)) {
    $filter = ['id' => DATA::getPost('editIdMessage')];
    $data = ['content' => DATA::getPost('editContent')];
    Generique::update('profil_news', 'graphene_bsm', $filter, $data);
    UTILS::notification('success', 'Votre message a été modifié avec succès.');
}

if (!empty($_POST) && (DATA::isPost('deleteOKarticles') || DATA::isPost('deleteOKvideos')) && ManageRights::verifyRights('Articles & Vidéos', 'Write', false)) {
    if (DATA::isPost('deleteOKarticles')) {
        $delId = DATA::getPost('deleteOKarticles');
        MYSQL::query("UPDATE articles SET profil_news = 0 WHERE id = {$delId}");
        UTILS::notification('success', 'Votre article a été supprimé du '.$alias.' Univers avec succès.', false, true);
    }

    if (DATA::isPost('deleteOKvideos')) {
        $delId = DATA::getPost('deleteOKvideos');
        MYSQL::query("UPDATE videos SET profil_news = 0 WHERE id = {$delId}");
        UTILS::notification('success', 'Votre vidéo a été supprimé du '.$alias.' Univers avec succès.', false, true);
    }
}

switch ($AdminMemberID) {
  case true:
    $request = 'id_client = \''.DATA::getGet('Member').'\'';

    break;

  default:
    $request = 'pseudo = \''.USER::getPseudo().'\'';

    break;
}

$req = MYSQL::query('SELECT * FROM accounts WHERE '.$request);
$resultAccount = mysqli_fetch_object($req);

$dateInscription = date('d M Y', strtotime($resultAccount->Date_Inscription));

$req = MYSQL::query('SELECT X.*
FROM (
          SELECT Nom, Prenom, Pseudo, content, id, id AS link_article, profil_news.id_client, NULL AS title, NULL AS summary, NULL AS video, NULL AS videoLink, NULL as `image`, `date`, facebookId, linkedinId
          FROM profil_news LEFT JOIN accounts ON profil_news.id_client=accounts.id_client
        UNION
          SELECT NULL AS Nom, NULL AS Prenom, NULL AS Pseudo, NULL AS id_client, NULL AS content, id, id as link_article, title, SUBSTRING(summary, 1, 700), NULL AS video, NULL AS videoLink, `image`, `date`, facebookId, linkedinId
          FROM articles WHERE profil_news = 1
        UNION
          SELECT NULL AS Nom, NULL AS Prenom, NULL AS Pseudo, NULL AS id_client, NULL AS content, id, id AS link_article, title, SUBSTRING(summary, 1, 700), video, videoLink, `image`, `date`, facebookId, linkedinId
          FROM videos WHERE profil_news = 1
      ) X
ORDER BY X.`date` DESC
');

$i = 0;
$messagePermission = ManageRights::verifyRights('Messages', 'Write', false, false);
while ($r = mysqli_fetch_object($req)) {
    $link = '';
    $media = '';
    $linkContent = '';
    if (0 == $r->id_client) {
        $avatar = UTILS::getAvatar($r->Pseudo);
        $content = $r->content ? html_entity_decode($r->content) : '';
        $r->title = "<img src='/themes/assets/images/logo.png' class='img-avatar m-1'> {$siteName}";
        $type = 'messages';
        $contentClean = str_replace(array("\r", "\n"), '', $r->content);
        $editLink = "<li><a class='btn text-hover-theme' href='#' data-toggle='modal' data-target='#editMessageModal' onclick=\"editMessage('{$r->link_article}', '{$contentClean}')\"><i class='material-icons mr-2 vertical-align'>edit</i> Modifier</a></li>";
    } elseif ($r->Pseudo) {
        $avatar = UTILS::getAvatar($r->Pseudo);
        $content = $r->content ? html_entity_decode($r->content) : '';
        $r->title = "<a href='/Administration/Membres/{$r->id_client}'><img src='{$avatar}' class='img-avatar m-1'></a> {$r->Nom} {$r->Prenom}";
        $type = 'messages';
        $contentClean = str_replace(array("\r", "\n"), '', $r->content);
        $editLink = "<li><a class='btn text-hover-theme' href='#' data-toggle='modal' data-target='#editMessageModal' onclick=\"editMessage('{$r->link_article}', '{$contentClean}')\"><i class='material-icons mr-2 vertical-align'>edit</i> Modifier</a></li>";
    } elseif ($r->video || $r->videoLink) {
        $poster = $r->image ? "poster='{$r->image}'" : '';
        $media = $r->videoLink ? html_entity_decode($r->videoLink) : "<video src='{$r->video}' preload='metadata' controls width='100%' style='height: 200px;' {$poster}></video>";
        $content = strip_tags(str_replace(["<br>", "</p>"], "\n", html_entity_decode($r->summary)));
        $type = 'videos';
        $linkContent = "<li><a class='btn text-hover-theme' href='/Videos/{$r->link_article}' target='_blank'><i class='material-icons mr-2 vertical-align'>visibility</i>Voir sur le site</a>";
        $editLink =  "<li><a class='btn text-hover-theme' href='/Administration/Video/{$r->link_article}'><i class='material-icons mr-2 vertical-align'>edit</i> Modifier</a></li>";
    } else {
        $content = $r->summary.'<br/><b>… Afficher la suite</b>';
        $link = "href='#' data-toggle='modal' data-target='#articleModal' onclick='getArticle({$r->link_article}, \"{$r->title}\")'";
        $type = 'articles';
        $media = "<img src='{$r->image}'>";
        $linkContent = "<li><a class='btn text-hover-theme' href='/Articles/{$r->link_article}' target='_blank'><i class='material-icons mr-2 vertical-align'>visibility</i>Voir sur le site</a>";
        $editLink =  "<li><a class='btn text-hover-theme' href='/Administration/Article/{$r->link_article}'><i class='material-icons mr-2 vertical-align'>edit</i> Modifier</a></li>";
    }

    $fb = $r->facebookId ? '<div class="btn btn-link facebookNews cursor-pointer" onClick="parent.open(\'https://www.facebook.com/'.$r->facebookId.'\'); return false" title="Voir sur Facebook"><i class="ya ya-facebook mr-2"></i>Voir sur Facebook</div>' : '<button type="submit" name="shareFB" value="'.$r->link_article.'" class="btn text-hover-theme" title="Partager sur Facebook"><i class="material-icons color-dark mr-2 vertical-align">reply</i>Partager sur Facebook</button>';
    $in = $r->linkedinId ? '<div class="btn btn-link facebookNews cursor-pointer" onClick="parent.open(\'https://www.linkedin.com/feed/update/'.$r->linkedinId.'\'); return false" title="Voir sur Linkedin"><b class="mx-2">In</b></i>Voir sur Linkedin</div>' : '<button type="submit" name="shareLinkedIn" value="'.$r->link_article.'" class="btn text-hover-theme" title="Partager sur Linkedin"><i class="material-icons color-dark mr-2 vertical-align">reply</i>Partager sur Linkedin</button>';
    $admin = $messagePermission ? '<span class="moreNews" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="material-icons color-dark">more_vert</i> </span>' : '';

    if ($content) {
        $tplProfil->bloc('ARTICLES', [
            'TITLE' => $r->title,
            'DATE' => UTILS::date($r->date),
            'CONTENT' => $content,
            'BALISE' => $link ? 'a' : 'div',
            'LINK' => $link,
            'MEDIA' => $media,
            'TYPE' => $type,
            'FACEBOOK' => $fb,
            'LINKEDIN' => $in,
            'LINK_ARTICLES' => $r->link_article,
            'ADMIN' => $admin,
            'LINK_CONTENT' => $linkContent,
            'EDIT' => $editLink
        ]);
    }
}

if ($messagePermission) {
    $tplProfil->bloc('IF_IS_ADMIN');

    if (FACEBOOK::isEnabled()) {
        $tplProfil->values(['FACEBOOK_CHECKBOX' => FACEBOOK::isEnabled() == true ? '' : 'title="Merci de connecter votre page Facebook dans les paramètres" disabled']);
    }
    if (LINKEDIN::isEnabled()) {
        $tplProfil->values(['LINKEDIN_CHECKBOX' => LINKEDIN::isEnabled() == true ? '' : 'title="Merci de connecter votre page Linkedin dans les paramètres" disabled']);
    }
}

$tplProfil->values([
    'ID_MEMBRE' => $resultAccount->id_client,
    'AVATAR' => UTILS::getAvatar($resultAccount->Pseudo),
    'PSEUDO' => $resultAccount->Pseudo,
    'NOM' => $resultAccount->Nom,
    'PRENOM' => $resultAccount->Prenom,
    'EMAIL' => $resultAccount->Email,
    'DATE_INSCRIPTION' => $dateInscription,
    'PAYS' => $resultAccount->Pays,
    'URL' => UTILS::getFunction('StaticUrl'),
    'ABOUT' => $resultAccount->about,
    'VILLE' => $resultAccount->Ville,
    'SIGNATURE' => $resultAccount->signature,
    'SITENAME' => $siteName,
]);

$PAGES = $tplProfil->construire('profil');
