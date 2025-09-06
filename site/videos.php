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

$query = MYSQL::query("SELECT ModuleActive, `image`, Titre FROM modulespages WHERE NameModule = 'VIDEOS'");
$req = mysqli_fetch_object($query);

if ($req->ModuleActive == 'ON') {
  $tplArticles->bloc('IF_VIDEOS');
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
  $tplArticles->setFile("page", "videos_rr.html");
} else {
  $tplArticles->setFile("page", "videos.html");
}

$description = MYSQL::selectOneValue("SELECT content FROM documents WHERE ref = 'SITE_DESCRIPTION'");

$date = "";
if (DATA::isGet('video')) {
  $videoId = DATA::getGet('video');
  $reqArticle = MYSQL::query("SELECT * FROM videos WHERE ID = '{$videoId}'");
  if ($r = mysqli_fetch_object($reqArticle)) {
    $video = $r->videoLink ? html_entity_decode($r->videoLink) : "<video src='{$r->video}' preload='metadata' controls autoplay width='100%'></video>";
    $tplArticles->bloc('VIDEO', array(
      'ID' => $r->id,
      'TITLE' => $r->title,
      'DATE' => UTILS::date($r->date, 'l d F Y'),
      'IMAGE' => $r->image,
      'SUMMARY' => html_entity_decode($r->summary),
      'VIDEO' => $video
    ));
    $date = $r->date;
    $description = $r->summary;
    $image = $r->image;
    $pagename = $r->title;
  } else {
    header("Location: /Videos");
  }

} else {
  $tplArticles->bloc('IF_VID_LIST');
  $reqArticles = MYSQL::query("SELECT id, title, `image`, video, summary, `date` FROM videos WHERE `hidden` IS NULL ORDER BY id DESC");
  while ($r = mysqli_fetch_object($reqArticles)) {

    $tplArticles->bloc('IF_VID_LIST.VIDEO', array(
      'ID' => $r->id,
      'TITLE' => $r->title,
      'DATE' => UTILS::date($r->date, 'd F Y'),
      'THUMBNAIL' => $r->image ? "<img src='{$r->image}' alt='{$r->title}'/>" : ($r->video ? "<video src='{$r->video}' preload='image'></video>" : ''),
      'SUMMARY' => strip_tags(str_replace(["<br>", "</p>"], "\n", html_entity_decode($r->summary))),
    ));
  }
}

$tplArticles->values(array(
  'DESCRIPTION' => $description,
  'IMAGE' => $image,
  'OG_IMAGE' => preg_match("/^(http)/", $image) ? $image : UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').$image,
  'PAGENAME' => $pagename,
  'OG_TYPE' => 'video',
  'OG_CONTENT' => "<meta property='video:release_date' content='{$date}' />"
));

$buffer = "";
$buffer .= UTILS::Encode($tplArticles->construire('header'));
$buffer .= UTILS::Encode($tplArticles->construire('page'));
$buffer .= UTILS::Encode($tplArticles->construire('footer'));
echo UTILS::compressHtml($buffer);
