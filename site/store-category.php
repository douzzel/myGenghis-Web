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

$pagename = 'Categories';
require_once('utils.php');

$desc = MYSQL::selectOneValue("SELECT content FROM documents WHERE ref = 'SITE_DESCRIPTION'");

$urlid = $_SERVER['REQUEST_URI'];
$cid = explode("?cat=", $urlid);
$fid = explode("?fam=", $urlid);
$catName = '';
$promo = 0;
// for Store type/entry point verifications below
if (!isset($_SESSION["storetype"]))
  $_SESSION["storetype"] = "external";

if (sizeof($cid) > 1) {
  $catName = htmlentities(urldecode($cid[1]));
  if ($catName == 'Tout') {
    $reqFamily = MYSQL::query("SELECT * FROM product_family");
    $catName = '%';
    $filterCat = '';
  } else {
    $reqFamily = MYSQL::query("SELECT * FROM product_family WHERE fam_cat = '{$catName}'");
    $filterCat = " WHERE `name` = '{$catName}'";
  }
  // Store type query changer
  if ($_SESSION["storetype"] == 'internal') {
      $req = MYSQL::query('SELECT *, products.id, products.id as product_id, products.name, stock > 0 as is_stock, promo FROM products LEFT JOIN category_tva ON products.id_category_tva = category_tva.id LEFT JOIN product_images ON product_images.id = (SELECT id FROM product_images WHERE products.id = product_images.product_id LIMIT 1) WHERE products.p_category LIKE \'' . $catName . '\' AND mask = 0 AND intmask = 0 ORDER BY is_stock DESC, products.name ASC');
  } else {
    $req = MYSQL::query('SELECT *, products.id, products.id as product_id, products.name, stock > 0 as is_stock, promo FROM products LEFT JOIN category_tva ON products.id_category_tva = category_tva.id LEFT JOIN product_images ON product_images.id = (SELECT id FROM product_images WHERE products.id = product_images.product_id LIMIT 1) WHERE products.p_category LIKE \'' . $catName . '\' AND mask = 0 AND extmask = 0 ORDER BY is_stock DESC, products.name ASC');
  }
  $reqCat = MYSQL::query("SELECT * FROM product_categories {$filterCat}");
  $cat = mysqli_fetch_object($reqCat);
}

if (sizeof($fid) > 1) {
  $famId = htmlentities(urldecode($fid[1]));
  // Store type query changer
  if ($_SESSION["storetype"] == 'internal') {
      $req = MYSQL::query('SELECT *, products.id, products.id as product_id, products.name, stock > 0 as is_stock, promo FROM products LEFT JOIN category_tva ON products.id_category_tva = category_tva.id LEFT JOIN product_images ON product_images.id = (SELECT id FROM product_images WHERE products.id = product_images.product_id LIMIT 1) WHERE products.p_family LIKE "%|' . $famId . '|%" AND mask = 0 AND intmask = 0 ORDER BY is_stock DESC, products.name ASC');
  } else {
    $req = MYSQL::query('SELECT *, products.id, products.id as product_id, products.name, stock > 0 as is_stock, promo FROM products LEFT JOIN category_tva ON products.id_category_tva = category_tva.id LEFT JOIN product_images ON product_images.id = (SELECT id FROM product_images WHERE products.id = product_images.product_id LIMIT 1) WHERE products.p_family LIKE "%|' . $famId . '|%" AND mask = 0 AND extmask = 0 ORDER BY is_stock DESC, products.name ASC');
  }
  $reqCat = MYSQL::query("SELECT * FROM product_family WHERE `id` = '{$famId}'");
  $cat = mysqli_fetch_object($reqCat);
  $reqFamily = MYSQL::query("SELECT * FROM product_family WHERE parent LIKE '%|{$famId}|%'");
}

$bg_img = MYSQL::selectOneValue("SELECT image FROM modulespages WHERE NameModule = 'STORE'");

$itnum = mysqli_num_rows($req);
$tplStore->values(array(
  'BG_IMG' => $bg_img,
  'DESCRIPTION' => $desc,
  'PAGENAME' => sizeof($cid) > 1 ? urldecode($cid[1]) : urldecode($fid[2]),
  'OG_IMAGE' => preg_match("/^(http)/", $bg_img) ? $bg_img : UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').$bg_img,
  'OG_TYPE' => 'website',
  'PRODUIT_S' => $itnum . ($itnum > 1 ? ' Produits' : ' Produit'),
  'PANIER_ACTIVE' => $nbrArticle > 0 ? 'active' : '',
  'PANIER_BUTTON_ACTIVE' => $nbrArticle > 0 ? 'button' : '',
  'NBR_ARTICLE_CART' => $nbrArticle,
  'BG_CATEGORY' => $cat && $cat->img ? "/site/$cat->img" : $bg_img,
  'NBR_ARTICLE' => MYSQL::selectOneValue("SELECT count(*) FROM products WHERE mask = 0"),
  'NBR_ARTICLE_CAT' => ($catName != "" ? MYSQL::selectOneValue("SELECT count(*) FROM products WHERE mask = 0 AND p_category = '$catName'") : MYSQL::selectOneValue("SELECT count(*) FROM products WHERE mask = 0 AND p_family LIKE '%|$famId|%'"))
));

$message_stock_0 = MYSQL::selectOneValue("SELECT content FROM documents WHERE REF = 'STOCK_0_MESSAGE'");

$TTCInStore = UTILS::getFunction('TTCInStore');
while ($r = mysqli_fetch_object($req)) {
  $pTVA = UTILS::price($TTCInStore ? round($r->price + ($r->price * $r->rate / 100), 2) : $r->price, true);

  // Take higher promotion from category of product
  if ($r->promo > 0 || ($cat && isset($cat->promo) && $cat->promo > 0)) {
    $promo = min(getProductPrice($r->price, $r->promo), getProductPrice($r->price, ($cat && isset($cat->promo) ? $cat->promo : '0').'%'));
    $promoTVA = UTILS::price($TTCInStore ? round($promo + ($promo * $r->rate / 100), 2) : $promo, true);
  }
  $image = $r->name_img ? '/themes/assets/images/store/' . $r->name_img : $r->image;
  $tplStore->bloc('PRODUCTS', array(
    'PROMO' => $r->promo > 0 || ($cat && isset($cat->promo) && $cat->promo > 0) ? "<strike class='text-warning'>{$pTVA}</strike> <b class='text-success'> {$promoTVA}</b>" : "<b class='text-success'>{$pTVA}</b>",
    'PRODUCT_ID' => $r->product_id,
    'NAME' => $r->name,
    'STOCK' => $r->stock > 0 && $r->quantite_minimum <= $r->stock ? '<span class="stock">En stock</span>' : ($message_stock_0 ? "<span class='stock' style='background-color:darkred;'>$message_stock_0</span>" : ""),
    'MEDIA' => $image ? '<img src="'.$image.'" style="height: 260px; width: 340px; object-fit: cover;" loading="lazy"/>' : '<video src="'.$r->video.'" autoplay loop muted style="height: 260px; width: 340px; object-fit: cover;"></video>',
    'DESCRIPTION' => substr($r->description, 0, 200),
    'PRICE' => getProductPrice($r->price, $r->promo),
    'IF_NO_STOCK' => $r->stock > 0 ? '' : 'hidden',
    'PROMOTION' => $r->promo > 0 || ($cat && isset($cat->promo) && $cat->promo > 0) ? '<span class="onsale"><span class="sale-text">Promotion</span></span>' : '',
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
  $filter = ['fam_cat' => $r->name];
  $products = Generique::select('product_family', 'graphene_bsm', $filter);
  if ($products) {
    $tplStore->bloc('CATEGORIE.IF_FAM');
  }
  $req2 = MYSQL::query("SELECT * FROM product_family WHERE fam_cat = '{$r->name}' AND (parent IS NULL OR parent like '||')");
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

while ($r = mysqli_fetch_object($reqFamily)) {
  $tplStore->bloc('FAMILIES', [
    'NAME' => $r->name,
    'ID' => $r->id,
  ]);
}

$buffer = "";
$buffer .= UTILS::Encode($tplStore->construire('header'));
$buffer .= UTILS::Encode($tplStore->construire('store-category'));
$buffer .= UTILS::Encode($tplStore->construire('footer'));
echo UTILS::compressHtml($buffer);
