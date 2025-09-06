<?php
USER::init();
require_once('../pages/accueil/compteur.php');
$site = UTILS::getFunction('StaticUrl');

$basket = new webshop();
if(DATA::isPost('addItems')) {
  $basket->addArticle(true);
}
$nbrArticle = $basket->compterArticles();

if (DATA::isSession('notification')) { // on charge les notifications si y'en as
  $tpl->value('NOTIFICATIONS', DATA::getSession('notification'));
  unset($_SESSION['notification']); // on supprime la variable après affichage
}

$displayTextSocialNetwork = false;
if (UTILS::getFunction('urlFacebook')) {
  $tpl->bloc('IF_FACEBOOK');
  $displayTextSocialNetwork = true;
}
if (UTILS::getFunction('urlYoutube')) {
  $tpl->bloc('IF_YOUTUBE');
  $displayTextSocialNetwork = true;
}
if (UTILS::getFunction('urlLinkedin')) {
  $tpl->bloc('IF_LINKEDIN');
  $displayTextSocialNetwork = true;
}
if (UTILS::getFunction('urlInstagram')) {
  $tpl->bloc('IF_INSTAGRAM');
  $displayTextSocialNetwork = true;
}
if (UTILS::getFunction('urlTwitter')) {
  $tpl->bloc('IF_TWITTER');
  $displayTextSocialNetwork = true;
}

if ($displayTextSocialNetwork) {
  $tpl->bloc('IF_DISPLAY_TEXT_SOCIAL_NETWORK');
}

$displayTextContact = false;
if (UTILS::getFunction('phone')) {
  $tpl->bloc('IF_PHONE');
  $displayTextContact = true;
}

if (UTILS::getFunction('displayAddress') && UTILS::getFunction('address')) {
  $tpl->bloc('IF_DISPLAY_ADDRESS');
  $displayTextContact = true;
}

if (UTILS::getFunction('displayEmail') && UTILS::getFunction('WebmasterEmail')) {
  $tpl->bloc('IF_DISPLAY_EMAIL');
  $displayTextContact = true;
}

if (UTILS::isModuleActive('CONTACT')) {
  $tpl->bloc('IF_CONTACT_FORM');
  $displayTextContact = true;
}

if ($displayTextContact) {
  $tpl->bloc('IF_DISPLAY_TEXT_CONTACT');
}

$displayWebMenu = UTILS::getFunction('displayWebMenu');
if ($displayWebMenu)
  $tpl->bloc('DISPLAY_WEB_MENU');

// * Load default and custom fonts
$fontDirectory = __DIR__."/../uploads/fonts";
$defaultFonts = '--font-text: "'.(UTILS::getFunction('fontText') ?? 'Roboto').'";
                --font-title: "'.(UTILS::getFunction('fontTitle') ?? 'Lato').'";';
$outputFonts = "";
if (is_dir($fontDirectory)) {
    $direc = opendir($fontDirectory);
    while ($file = readdir($direc)) {
      if ($file != '.gitkeep') {
        if (is_file("$fontDirectory/$file")) {
                $name = pathinfo("$fontDirectory/$file", PATHINFO_FILENAME);
                $ext = pathinfo("$fontDirectory/$file", PATHINFO_EXTENSION);
                $outputFonts .= "@font-face { font-family: '{$name}'; src:url('/uploads/fonts/{$file}'); }";
            }
        }
    }
    closedir($direc);
}

$tpl->values(array(
  'FONTS' => $outputFonts,
	'DEFAULT_FONTS' => '--font-my-genghis: "'.(UTILS::getFunction('fontMyGenghis') ?? 'Roboto').'";
  '.'--font-text: "'.(UTILS::getFunction('fontText') ?? 'Roboto').'";
    --font-title: "'.(UTILS::getFunction('fontTitle') ?? 'Lato').'";',
  'EMAIL' => UTILS::getFunction('WebmasterEmail'),
  'HTTP' => UTILS::getFunction('isHttps'),
  'PHONE' => UTILS::getFunction('phone'),
  'ADDRESS' => UTILS::getFunction('address'),
  'SITENAME' => UTILS::getFunction('SiteName'),
  'FACEBOOK' => UTILS::getFunction('urlFacebook'),
  'YOUTUBE' => UTILS::getFunction('urlYoutube'),
  'LINKEDIN' => UTILS::getFunction('urlLinkedin'),
  'INSTAGRAM' => UTILS::getFunction('urlInstagram'),
  'TWITTER' => UTILS::getFunction('urlTwitter'),
  'EMAIL' => UTILS::getFunction('WebmasterEmail'),
	'STATIC_URL' => UTILS::getFunction('StaticUrl'),
  'URL' => $_SERVER['REQUEST_URI'],
  'CACHE_LOGO' => filemtime('../themes/assets/images/logo.png'),
  'WEB_LOGO_LINK' => UTILS::getFunction('webLogoLink') ?? '/',
  'YEAR' => date('Y')
));

if ($displayWebMenu) {
    // Construct the menu bar
    $sql = "SELECT url, pageName, id FROM user_custom_page WHERE page_group IS NULL AND visibility IS true ORDER BY sort_order";
    $reqMenu = MYSQL::query($sql);
    if (mysqli_num_rows($reqMenu) > 0) {
        while ($r = mysqli_fetch_object($reqMenu)) {
            $subHtml = "";
            $subOnlyItems = "";
            $sqlSub = MYSQL::query("SELECT url, pageName FROM user_custom_page WHERE page_group = '{$r->id}' AND visibility IS true ORDER BY sort_order");
            if (mysqli_num_rows($sqlSub) > 0) {
                $subHtml .= "<ul class='sub-menu'>";
                while ($sub = mysqli_fetch_object($sqlSub)) {
                    $active = "";
                    if ($_SERVER['REQUEST_URI'] == '/'.$sub->url) {
                        $active = "class='active'";
                    }
                    $subOnlyItems .= "<li {$active}><a href='{$sub->url}'>{$sub->pageName}</a></li>";
                }
                $subHtml .= $subOnlyItems;
                $subHtml .= "</ul>";
            }

            $active = "";
            if ($_SERVER['REQUEST_URI'] == '/'.$r->url) {
                $active = "class='active'";
            }
            $tpl->bloc('DISPLAY_WEB_MENU.IF_IS_MENU', [
              'TITLE' => $r->pageName,
              'URL' => $r->url,
              'SUBITEMS' => $subHtml,
              'SUB_ONLY_ITEMS' => $subOnlyItems,
              'ACTIVE' => $active
            ]);
        }
    }

    $contact = 0;
    // Construct the menu bar
    $sql = "SELECT url, pageName, template FROM user_custom_page WHERE visibility IS true ORDER BY sort_order";
    $reqMenu = MYSQL::query($sql);
    if (mysqli_num_rows($reqMenu) > 0) {
        while ($r = mysqli_fetch_object($reqMenu)) {
            $tpl->bloc('DISPLAY_WEB_MENU.IF_IS_MENU_MOBILE', [
              'TITLE' => $r->pageName,
              'URL' => $r->url
            ]);
            if (in_array($r->template, [17]) && $contact == 0 && UTILS::isModuleActive('CONTACT')) {
                $contact = 1;
                $tpl->bloc('DISPLAY_WEB_MENU.IF_CONTACT', [
                  'URL' => $r->url
                ]);
            }
        }
    }

    if (UTILS::isModuleActive('STORE'))
        $tpl->bloc('DISPLAY_WEB_MENU.IF_STORE', ['STORE_NAME' => MYSQL::selectOneValue("SELECT ModuleTitle FROM modulespages WHERE NameModule = 'STORE'")]);

    if (UTILS::isModuleActive('ARTICLES'))
      $tpl->bloc('DISPLAY_WEB_MENU.IF_ARTICLES');

    if (UTILS::isModuleActive('VIDEOS'))
      $tpl->bloc('DISPLAY_WEB_MENU.IF_VIDEOS');

    if (USER::isConnecte()) {
      $tpl->bloc('DISPLAY_WEB_MENU.IF_ACCOUNT');
    } else if (UTILS::isModuleActive('CONNEXION')) {
      $tpl->bloc('DISPLAY_WEB_MENU.IF_CONNEXION');
    }
}

if (UTILS::isModuleActive('LIVECHAT'))
  $tpl->bloc('IF_LIVECHAT');

if ($site == 'mpbat.fr') {
  $tpl->setFile("header", "MP_BAT/header.html");
  $tpl->setFile("page", "MP_BAT/page.html");
  $tpl->setFile("produit", "produit.html");
  $tpl->setFile("store-category", "store-category.html");
  $tpl->setFile("footer", "MP_BAT/footer.html");
} else if ($site == 'laou.eu' && (!isset($pagename) || !in_array($pagename, ['Articles', 'Vidéos', 'Categories', 'Produit', 'Store', 'Boutique']))) {
  $tpl->setFile("header", "Laou/header.html");
  $tpl->setFile("page", "Laou/page.html");
  $tpl->setFile("footer", "Laou/footer.html");
} else if ($site == 'fromagesrerolle.fr') {
  $tpl->setFile("header", "header.html");
  $tpl->setFile("page", "page_rr.html");
  $tpl->setFile("produit", "produit.html");
  $tpl->setFile("store-category", "store-category.html");
  $tpl->setFile("footer", "footer_rr.html");
} else {
  $tpl->setFile("header", "header.html");
  $tpl->setFile("page", "page.html");
  $tpl->setFile("produit", "produit.html");
  $tpl->setFile("store-category", "store-category.html");
  $tpl->setFile("footer", "footer.html");
}
$tpl->setFile("empty_page", "empty_page.html");

if ($site == 'damien-rebourg.fr')
  $tpl->values(array('STYLE' => '.header {background: white !important} .navigation > ul > li > a {color: rgb(70, 70, 70) !important}'));

function getProductPrice($price, $promo) {

    if (strpos($promo, '%') === false) {
      $promo = number_format(round((float)$promo, 2), 2, '.', '');
      return round($price - $promo, 2);
    }
    $promo = number_format(round((float)$promo, 2), 2, '.', '');
    return round($price - ($price * $promo / 100), 2);
}
?>
