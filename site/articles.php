<?php
spl_autoload_register(
  function ($x) {
    $sources = array('../classes/' . str_replace('_', '/', $x) . '.class.php'); // chargement des classes
    foreach ($sources as $source) {
      if (file_exists($source)) {
        require_once $source;
      }
    }
  }
);

$tplArticles = new Template;

$query = MYSQL::query("SELECT ModuleActive, `image`, Titre FROM modulespages WHERE NameModule = 'ARTICLES'");
$req = mysqli_fetch_object($query);

if ($req->ModuleActive == 'ON') {
  $tplArticles->bloc('IF_ARTICLES');
  $pagename = $req->Titre;
  $image = $req->image;
} else {
  header("Location: /");
  exit;
}

$tpl = $tplArticles;
require_once('utils.php');
$site = UTILS::getFunction('StaticUrl');

if ($site == 'fromagesrerolle.fr') {
  $tplArticles->setFile("page", "articles_rr.html");
} else {
  $tplArticles->setFile("page", "articles.html");
}

if (DATA::isGet('data')) {
  $articleId = DATA::getGet('article');
  $content = MYSQL::selectOneValue("SELECT content FROM articles WHERE ID = '{$articleId}'");
  echo "<style>
    {$outputFonts}
    :root { {$defaultFonts} }
    body { font-family: var(--font-text), sans-serif; line-height: 26px; }
    h1, h2, h3, h4, h5, h6 { font-family: var(--font-title); }
    .text-light, .text-white { color: white; }
    .post > div { background-repeat: revert; padding: 1rem; font-weight: bold; }
    p { margin-bottom: 25px; }
  </style>
  <script>document.addEventListener('DOMContentLoaded', function(){
    var a = document.getElementsByTagName('a');
    [...a].forEach((e) => e.target = '_parent');
  })</script>";
  echo html_entity_decode($content);
  exit;
}

$description = MYSQL::selectOneValue("SELECT content FROM documents WHERE ref = 'SITE_DESCRIPTION'");

$date = "";
if (DATA::isGet('article')) {
  $articleId = DATA::getGet('article');
  $reqArticle = MYSQL::query("SELECT * FROM articles WHERE ID = '{$articleId}'");
  if ($r = mysqli_fetch_object($reqArticle)) {
    $tplArticles->bloc('ARTICLE', array(
      'ID' => $r->id,
      'TITLE' => $r->title,
      'DATE' => UTILS::date($r->date, 'l d F Y'),
      'IMAGE' => $r->image,
      'CONTENT' => html_entity_decode($r->content)
    ));
    $date = $r->date;
    $description = $r->summary;
    $image = $r->image;
    $pagename = $r->title;
  } else {
    header("Location: /Articles");
  }

} else {
  $tplArticles->bloc('IF_ART_LIST');
  $reqArticles = MYSQL::query("SELECT id, title, `image`, summary, `date` FROM articles WHERE `hidden` IS NULL ORDER BY id DESC");
  while ($r = mysqli_fetch_object($reqArticles)) {
    $tplArticles->bloc('IF_ART_LIST.ARTICLE', array(
      'ID' => $r->id,
      'TITLE' => $r->title,
      'DATE' => UTILS::date($r->date, 'd F Y'),
      'IMAGE' => $r->image,
      'SUMMARY' => $r->summary,
    ));
  }
}

$tplArticles->values(array(
  'DESCRIPTION' => $description,
  'IMAGE' => $image,
  'OG_IMAGE' => preg_match("/^(http)/", $image) ? $image : UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').$image,
  'PAGENAME' => $pagename,
  'OG_TYPE' => 'article',
  'OG_CONTENT' => "<meta property='article:published_time' content='{$date}' />"
));

$buffer = "";
$buffer .= UTILS::Encode($tplArticles->construire('header'));
$buffer .= UTILS::Encode($tplArticles->construire('page'));
$buffer .= UTILS::Encode($tplArticles->construire('footer'));
echo UTILS::compressHtml($buffer);
