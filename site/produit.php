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

$tplStore = new Template;
$tpl = $tplStore;

$pagename = 'Produit';
require_once('utils.php');

$desc = MYSQL::selectOneValue("SELECT content FROM documents WHERE ref = 'SITE_DESCRIPTION'");

$pid = $_SERVER['REQUEST_URI'];
$pid = explode("?id=", $pid);
$req = MYSQL::query('SELECT *, products.id, products.id as product_id, products.name, products.promo as promo, product_categories.promo as cat_promo FROM products LEFT JOIN category_tva ON products.id_category_tva = category_tva.id LEFT JOIN product_categories ON products.p_category = product_categories.name WHERE products.id = \'' . $pid[1] . '\'');
$r = mysqli_fetch_object($req);
if (!$pid || !$r) {
  header('Location: /Store');
  exit;
}

require_once('../classes/vendor/autoload.php');

$bg_img = MYSQL::selectOneValue("SELECT image FROM modulespages WHERE NameModule = 'STORE'");

$volume = "";
if ($r->steres != 0.00 || $r->ranges != 0.00)
    $volume .= "Volume : ";
if ($r->steres != 0.00)
    $volume .= $r->steres . " stère(s)";
if ($r->steres != 0.00 && $r->ranges != 0.00)
    $volume .= ", soit ";
if ($r->ranges != 0.00)
    $volume .= $r->ranges . " mètre(s) cube rangé(s)";

if ($r->subscription_interval_count && $r->subscription_interval_count > 0 && $r->subscription_interval) {
    if ($r->subscription_interval_count > 1) {
        $interval = ['day' => 'jours', 'week' => 'semaines', 'month' => 'mois', 'year' => 'ans'];
        $subscription = "<br/><b>Abonnement tous les {$r->subscription_interval_count} {$interval[$r->subscription_interval]}<b>";
    } else {
        $interval = ['day' => 'quotidien', 'week' => 'hebdomadaire', 'month' => 'mensuel', 'year' => 'annuel'];
        $subscription = "<br/><b>Abonnement {$interval[$r->subscription_interval]}<b>";
    }
} else {
    $subscription = "";
}

$TTCInStore = UTILS::getFunction('TTCInStore');
$pTVA = $TTCInStore ? UTILS::price($r->price + ($r->price * $r->rate / 100), true) : UTILS::price($r->price, true);
if ($r->promo > 0 || $r->cat_promo > 0) {
  $promo = min(getProductPrice($r->price, $r->promo), getProductPrice($r->price, $r->cat_promo.'%'));
  $promoTVA = $TTCInStore ? UTILS::price($promo + ($promo * $r->rate / 100), true) : UTILS::price($promo, true);
}
$promo = ($r->promo > 0 || $r->cat_promo > 0) ? "<strike class='text-warning mr-3'>{$pTVA}</strike> <b class='text-success'> {$promoTVA}</b>" : "<b class='text-success'>{$pTVA}</b>";

$media = $image = "";
$req = MYSQL::query('SELECT * FROM product_images WHERE product_id = '.$r->id);
if (mysqli_num_rows($req) > 0) {
  while($image_req = mysqli_fetch_object($req)) {
    $image = "";
    if ($image_req->image) {
      $image = $image_req->image;
    } else if ($image_req->thumbnail) {
      $image = $image_req->thumbnail;
    } else if ($image_req->name_img) {
      $image = '/themes/assets/images/store/'.$image_req->name_img;
    }
    $media .= $image ? '<div class="tiled-product mt-30" data-tilt data-tilt-scale="1.1"><a class="overlay-link lightbox-link" data-group="product-lightbox-gallery" href="'.$image.'"> <img src="'.$image.'" alt="" /> <span class="overlay-info"> <span> <span> Zoom </span> </span> </span> </a></div>' : ($image_req->video ? "<div class='thumbnail product-thumbnail img-scale-in' data-hover-easing='easeInOut' data-hover-speed='700' data-hover-bkg-color='#ffffff' data-hover-bkg-opacity='0.9'><video src='$image_req->video' autoplay controls></video></div>" : "");
  }
}

$promo .= $TTCInStore ? '<small class="ml-2"> T.T.C.</small>' : '<small class="ml-2"> H.T.</small>';

$site = UTILS::getFunction('StaticUrl');
if ($site == 'laou.eu')
  $r->description = strip_tags($r->description);

$message_stock_0 = MYSQL::selectOneValue("SELECT content FROM documents WHERE REF = 'STOCK_0_MESSAGE'");

$tplStore->values(array(
  'PAGENAME' => $r->name,
  'OG_IMAGE' => $image ? UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').$image : '',
  'OG_TYPE' => 'website',
  'BG_IMG' => $bg_img,
  'NAME' => $r->name,
  'P_CATEGORY' => $r->p_category,
  'STOCK' => $r->stock > 0 && $r->quantite_minimum <= $r->stock ? '<span class="stock">En stock</span>' : ($message_stock_0 ? "<span class='stock' style='background-color:darkred;'>$message_stock_0</span>" : ""),
  'MEDIA' => $media,
  'CATEGORIE' => $r->categorie ? "<b>Unité :</b> {$r->categorie}" : '',
  'PROMO' => $promo,
  'PRODUCT_ID' => $r->product_id,
  'PRICE' => min(getProductPrice($r->price, $r->promo), getProductPrice($r->price, $r->cat_promo.'%')),
  'QUANTITE_MINIMUM' => $r->quantite_minimum,
  'QUANTITE_MAXIMUM' => $r->quantite_maximum ? ($r->quantite_maximum < $r->stock ? $r->quantite_maximum : $r->stock) : $r->stock,
  'PANIER_ACTIVE' => $nbrArticle > 0 ? 'active' : '',
  'PANIER_BUTTON_ACTIVE' => $nbrArticle > 0 ? 'button' : '',
  'NBR_ARTICLE' => $nbrArticle,
  'POIDS' => $r->poids != 0.00 ? "Poids : " . (int) $r->poids . "kg" : '',
  'LONGUEUR_BUCHE' => $r->longueur_buche != 0.00 ? "Longueur de bûche : " . (int) $r->longueur_buche . "cm" : '',
  'VOLUME' => $volume,
  'PRODUCT_DESCRIPTION' => $r->description,
  'PRODUCT_INFORMATIONS' => $r->informations,
  'REFERENCE' => UTILS::getFunction('referenceInStore') && $r->reference ? "<span class='tagged-as'><b>Référence :</b> {$r->reference}</span><br/>" : '',
  'SUBSCRIPTION' => $subscription,
  'TEXT_BEFORE' => $r->textBefore ? $r->textBefore : (UTILS::getFunction('textTarifStore') ?: 'Tarif :'),
  'TEXT_AFTER' => $r->textAfter
));
$catName = $r->p_category;

if ($r->description)
  $tplStore->bloc('IF_DESCRIPTION');

if ($r->stock > 0 && $r->quantite_minimum <= $r->stock)
  $tplStore->bloc('IF_STOCK');
else if ($site != 'laou.eu')
  $tplStore->bloc('IF_NO_STOCK');

// for Store type/entry point verifications below
if (!isset($_SESSION["storetype"]))
  $_SESSION["storetype"] = "external";

// * Other Products
$maskStoreType = $_SESSION["storetype"] == 'internal' ? " AND intmask = 0" : "AND extmask = 0";
$reqOthers = MYSQL::query("SELECT *, products.name, products.id as product_id, products.promo as promo, product_categories.promo as cat_promo, products.textBefore, products.textAfter FROM products LEFT JOIN product_images ON products.id = product_images.product_id LEFT JOIN category_tva ON products.id_category_tva = category_tva.id LEFT JOIN product_categories ON products.p_category = product_categories.name WHERE products.id != '{$pid[1]}' AND mask = 0 {$maskStoreType} AND products.p_category = '{$r->p_category}' ORDER BY RAND () LIMIT 3");
if (mysqli_num_rows($reqOthers))
  $tplStore->bloc('IF_OTHER_PRODUCTS');
while ($r = mysqli_fetch_object($reqOthers)) {
  $image = $r->name_img ? '/themes/assets/images/store/' . $r->name_img : $r->image;
  $pTVA = UTILS::price($TTCInStore ? round($r->price + ($r->price * $r->rate / 100), 2) : $r->price, true);
  if ($r->promo > 0 || $r->cat_promo > 0) {
    $promo = min(getProductPrice($r->price, $r->promo), getProductPrice($r->price, $r->cat_promo.'%'));
    $promoTVA = UTILS::price($TTCInStore ? round($promo + ($promo * $r->rate / 100), 2) : $promo, true);
  }
  $tplStore->bloc('IF_OTHER_PRODUCTS.OTHER_PRODUCTS', array(
  'STOCK' => $r->stock > 0 && $r->quantite_minimum <= $r->stock ? '<span class="stock">En stock</span>' : ($message_stock_0 ? "<span class='stock' style='background-color:darkred;'>$message_stock_0</span>" : ""),
  'MEDIA' => $image ? "<img src='$image' style='min-height: 260px; max-height: 260px; margin: auto; display: block;' />" : '<video src="'.$r->video.'" autoplay loop muted style="height: 260px; width: 340px; object-fit: cover;"></video>',
  'PROMO' => ($r->promo > 0 || $r->cat_promo > 0) ? "<strike class='text-warning'>{$pTVA}</strike> <b class='text-success'> {$promoTVA}</b>" : "<b class='text-success'>{$pTVA}</b>",
  'NAME' => $r->name,
  'ID' => $r->product_id,
  'PRICE' => ($r->promo > 0 || $r->cat_promo > 0) ? $promo : $r->price,
  'TEXT_BEFORE' => $r->textBefore,
  'TEXT_AFTER' => $r->textAfter
  ));
}

$req = MYSQL::query('SELECT * FROM product_categories WHERE active = true');
while ($r = mysqli_fetch_object($req)) {
  $tplStore->bloc('CATEGORIE', array(
    'IMAGE' => $r->img,
    'NAME' => $r->name,
    'ACTIVE' => $catName == $r->name ? 'active' : ''
  ));
  $req2 = MYSQL::query("SELECT * FROM product_family WHERE fam_cat = '{$r->name}' AND (parent IS NULL OR parent like '||')");
  if (mysqli_num_rows($req2) > 0) {
    $tplStore->bloc('CATEGORIE.IF_FAM');
  }
  while ($r = mysqli_fetch_object($req2)) {
    $tplStore->bloc('CATEGORIE.IF_FAM.FAM', [
      'NAME' => $r->name,
      'ID' => $r->id,
      'IMAGE' => $r->img ? "<img src='$r->img'>" : '',
    ]);
    $req3 = MYSQL::query("SELECT * FROM product_family WHERE parent LIKE '%|{$r->id}|%'");
    if (mysqli_num_rows($req3) > 0) {
      $tplStore->bloc('CATEGORIE.IF_FAM.FAM.IF_SUB_FAM');
    }
    while ($r = mysqli_fetch_object($req3)) {
      $tplStore->bloc('CATEGORIE.IF_FAM.FAM.IF_SUB_FAM.SUB_FAM', [
        'NAME' => $r->name,
        'ID' => $r->id,
        'IMAGE' => $r->img ? "<img src='$r->img'>" : '',
      ]);
    }
  }
}

$reqFamily = MYSQL::query("SELECT * FROM product_family WHERE fam_cat = '{$catName}'");
while ($r = mysqli_fetch_object($reqFamily)) {
  $tplStore->bloc('FAMILIES', [
    'NAME' => $r->name,
    'ID' => $r->id,
  ]);
}

$buffer = "";
$buffer .= UTILS::Encode($tplStore->construire('header'));
$buffer .= UTILS::Encode($tplStore->construire('produit'));
$buffer .= UTILS::Encode($tplStore->construire('footer'));
echo UTILS::compressHtml($buffer);
