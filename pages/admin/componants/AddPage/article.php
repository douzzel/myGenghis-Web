<?php

ManageRights::verifyRights('Articles & Vidéos', 'Read');
$writePermission = ManageRights::verifyRights('Articles & Vidéos', 'Write', false, false);
if (!$writePermission) {
    $DISABLE_FORM = true;
}
$tplArticle = new Template();
$tplArticle->setFile('PB', './_admin/componants/AddPage/article.html');

if (DATA::isGet('act')) {
    $articleId = DATA::getGet('act');
    $query = MYSQL::query("SELECT * FROM articles WHERE id = '{$articleId}'");
    if ($r = mysqli_fetch_object($query));
    $hidden = $r->hidden ? '' : 'checked';
    $profil_news = $r->profil_news ? 'checked' : '';
    $tplArticle->bloc('ARTICLE', [
        'ID' => $r->id,
        'TITLE' => $r->title,
        'SUMMARY' => $r->summary,
        'IMAGE' => $r->image,
        'CONTENT' => $r->content,
        'DATE' => $r->date,
        'HIDDEN' => $hidden,
        'PROFIL_NEWS' => $profil_news,
        'FACEBOOK_LINK' => $r->facebookId,
        'LINKEDIN_LINK' => $r->linkedinId,
    ]);
    $tplArticle->values([
        'FACEBOOK' => FACEBOOK::isEnabled() ? ($r->facebookId ? 'checked disabled title="Article déjà publié sur Facebook"' : '') : 'title="Merci de connecter votre page Facebook dans les paramètres" disabled',
        'LINKEDIN' => LINKEDIN::isEnabled() ? ($r->linkedinId ? 'checked disabled title="Article déjà publié sur Linkedin"' : '') : 'title="Merci de connecter votre page Linkedin dans les paramètres" disabled',
    ]);

    if (!$r->hidden) {
        $tplArticle->bloc('ARTICLE.IF_BLOG_LINK');
    }
    if ($r->facebookId) {
        $tplArticle->bloc('ARTICLE.IF_FACEBOOK_LINK');
    }
    if ($r->linkedinId) {
        $tplArticle->bloc('ARTICLE.IF_LINKEDIN_LINK');
    }

    if ($writePermission) {
        if (DATA::isPost('duplicate')) {
            $data = ['title' => $r->title, 'summary' => $r->summary, 'image' => $r->image, 'content' => $r->content, 'hidden' => 1, 'profil_news' => '0'];
            Generique::insert('articles', 'graphene_bsm', $data);
            $id = Generique::selectMaxId('articles', 'graphene_bsm');
            UTILS::notification('success', 'Article dupliqué', false, true);
            header("Location: /Administration/Article/{$id}");
            exit;
        }

        if (DATA::isPost('edit')) {
            $hidden = 'on' == DATA::getPost('hidden') ? 'NULL' : 1;
            $profil_news = 'on' == DATA::getPost('profil_news') ? 1 : 0;
            $title = DATA::getPost('title');
            $summary = DATA::getPost('summary');
            $content = DATA::getPost('content');

            if ($image = IMAGE::upload($_FILES['image'], "site/images", $title, 750)) {
                $image = ", image = '{$image}'";
            }
            MYSQL::query("UPDATE articles SET title='{$title}', summary='{$summary}', content='{$content}', `hidden` = {$hidden}, profil_news = {$profil_news} {$image} WHERE id='{$articleId}'");

            $url = UTILS::getFunction('StaticUrl');
            $http = UTILS::getFunction('isHttps');

            $articleUrl = $http.'://'.$url.'/Articles/'.$articleId;
            $articleImg = MYSQL::selectOneValue("SELECT `image` FROM articles WHERE id = '{$articleId}'");
            $msg = 'Article modifié avec succès ';
            $error = '';

            if (!$r->facebookId && 'on' == DATA::getPost('facebook') ? 1 : 0) {
                $res = FACEBOOK::postArticles($articleUrl);
                if ($res) {
                    MYSQL::query("UPDATE articles SET facebookId = '{$res->id}' WHERE id='{$articleId}'");
                    $msg .= ' et publié sur Facebook';
                } else {
                    $error .= 'Erreur lors de la publication sur Facebook. Essayez de reconnecter votre page Facebook.';
                }
            }
            if (!$r->linkedinId && 'on' == DATA::getPost('linkedin') ? 1 : 0) {
                $res = LINKEDIN::postArticles($title, $articleImg, $articleUrl);
                if ($res) {
                    $idPost = isset($res->activity) ? $res->activity : $res->id;
                    MYSQL::query("UPDATE articles SET linkedinId = '{$idPost}' WHERE id='{$articleId}'");
                    $msg .= ' et publié sur LinkedIn';
                } else {
                    $error = 'Erreur lors de la publication sur LinkedIn. Essayez de reconnecter votre page LinkedIn.';
                }
            }

            UTILS::addHistory(USER::getPseudo(), 25, 'Article "'.$title. '" mis à jour');
            if ($error) {
                UTILS::notification('danger', $error);
            } else {
                UTILS::notification('success', $msg);
            }
        }

        if (DATA::isPost('deleteArticle')) {
            UTILS::Alert('danger', 'Suppression d\'un article, Voulez-vous vraiment supprimer cet article ?', 'L\'action sera irréversible', $_SERVER['REQUEST_URI'], 'deleteArticleConfirm', DATA::getPost('deleteArticle'));
        }

        if (DATA::isPost('deleteArticleConfirm')) {
            $articleId = DATA::getPost('deleteArticleConfirm');
            $articleTitle = MYSQL::selectOneValue("SELECT title FROM articles WHERE id='{$articleId}'");
            MYSQL::query("DELETE FROM articles WHERE `id` = '{$articleId}'");
            UTILS::notification('success', 'Article supprimé avec succès', false, true);
            UTILS::addHistory(USER::getPseudo(), 25, "Article « {$articleTitle} » supprimé");
            header('location: /Administration/Site');

            exit;
        }
    }

    // Load Article
} else {
    ManageRights::verifyRights('Articles & Vidéos', 'Write');
    $tplArticle->bloc('NEW');
    $tplArticle->values([
        'FACEBOOK' => FACEBOOK::isEnabled() ? '' : 'title="Merci de connecter votre page Facebook dans les paramètres" disabled',
        'LINKEDIN' => LINKEDIN::isEnabled() ? '' : 'title="Merci de connecter votre page LinkedIn dans les paramètres" disabled',
    ]);

    if (DATA::isPost('new')) {
        $hidden = 'on' == DATA::getPost('hidden') ? 'NULL' : 1;
        $title = DATA::getPost('title');
        $summary = DATA::getPost('summary');
        $content = DATA::getPost('content');
        $image = IMAGE::upload($_FILES['image'], 'site/images', $title, 750);
        $profil_news = 'on' == DATA::getPost('profil_news') ? 1 : 0;
        MYSQL::query("INSERT INTO articles(title, summary, `image`, content, `hidden`, profil_news) VALUES ('{$title}', '{$summary}', '{$image}', '{$content}', {$hidden}, {$profil_news})");
        $articleId = MYSQL::selectOneValue('SELECT max(id) FROM articles');

        $msg = 'Article sauvegardé avec succès ';
        $error = '';

        $url = UTILS::getFunction('StaticUrl');
        $http = UTILS::getFunction('isHttps');
        $articleUrl = $http.'://'.$url.'/Articles/'.$articleId;
        if ('on' == DATA::getPost('newFacebook') ? 1 : 0) {
            $res = FACEBOOK::postArticles($articleUrl);
            if ($res) {
                MYSQL::query("UPDATE articles SET facebookId = '{$res->id}' WHERE id='{$articleId}'");
                $msg .= ' et publié sur Facebook';
            } else {
                $error .= 'Erreur lors de la publication sur Facebook. Essayez de reconnecter votre page Facebook.';
            }
        }
        if ('on' == DATA::getPost('newLinkedin') ? 1 : 0) {
            $articleImg = MYSQL::selectOneValue("SELECT `image` FROM articles WHERE id = '{$articleId}'");
            $res = LINKEDIN::postArticles($title, $articleImg, $articleUrl);
            if ($res) {
                $idPost = isset($res->activity) ? $res->activity : $res->id;
                MYSQL::query("UPDATE articles SET linkedinId = '{$idPost}' WHERE id='{$articleId}'");
                $msg .= ' et publié sur LinkedIn';
            } else {
                $error = 'Erreur lors de la publication sur LinkedIn. Essayez de reconnecter votre page LinkedIn.';
            }
        }

        if ($error) {
            UTILS::notification('danger', $error, false, true);
        } else {
            UTILS::notification('success', $msg, false, true);
        }
        UTILS::addHistory(USER::getPseudo(), 25, "Nouvel article « {$title} »  créé");
        NOTIFICATIONS::add("web", "Nouvel article <b>{$title}</b> par " . NOTIFICATIONS::CreateTag(USER::getId()), "/Administration/Article/{$articleId}", [], "Articles & Vidéos");

        header("Location: /Administration/Article/{$articleId}");

        exit;
    }
}

$listMenuArray = array(
    ['Web', '/Administration/Site'],
    ['Article', '', true]
);
$TITRE = 'Article';
$DESCRIPTION = 'Modifier un article';
$tplArticle->values([
    'TITRE' => $TITRE,
    'DESCRIPTION' => $DESCRIPTION,
    'FIL_ARIANNE' => MENU::filArianne($listMenuArray, 'com')
]);
if (DATA::isGet('act')) {
    $articleId = DATA::getGet('act');
    $ACTION_MENU = createActionMenu([
    ['href' => "/Articles/{$articleId}", 'icon' => 'visibility', 'title' => "Voir l'article" ],
    ['href' => 'https://www.pexels.com/popular-searches', 'icon' => 'collections', 'title' => 'Photos', 'target' => '_blank'],
    ['href' => 'https://www.pexels.com/videos', 'icon' => 'movie', 'title' => 'Vidéos', 'target' => '_blank'],
    ['submit' => 'duplicate', 'icon' => 'content_copy', 'title' => 'Dupliquer'],
    ['submit' => 'deleteArticle', 'icon' => 'delete', 'title' => 'Supprimer', 'value' => $articleId]
]);
} else {
    $ACTION_MENU = createActionMenu([
        ['href' => 'https://www.pexels.com/popular-searches', 'icon' => 'collections', 'title' => 'Photos', 'target' => '_blank'],
        ['href' => 'https://www.pexels.com/videos', 'icon' => 'movie', 'title' => 'Vidéos', 'target' => '_blank']
    ]);
}
$PAGES = $tplArticle->construire('PB');
