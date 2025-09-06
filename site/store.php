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

$_SESSION["storetype"] = "external";

$tplStore = new Template;
$tpl = $tplStore;
$site = UTILS::getFunction('StaticUrl');
$pagename = $site == ('fromagerie-rerolle.graphene-bsm.com' || 'fromagesrerolle.fr' || 'www.fromagesrerolle.fr') ? 'Boutique' : 'Store';
require_once('utils.php');

$desc = MYSQL::selectOneValue("SELECT content FROM documents WHERE ref = 'SITE_DESCRIPTION'");

$bg_img = MYSQL::selectOneValue("SELECT image FROM modulespages WHERE NameModule = 'STORE'");

$tplStore->values(array(
  'DESCRIPTION' => $desc,
  'PAGENAME' => MYSQL::selectOneValue("SELECT ModuleTitle FROM modulespages WHERE NameModule = 'STORE'"),
  'OG_IMAGE' => preg_match("/^(http)/", $bg_img) ? $bg_img : UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').$bg_img,
  'OG_TYPE' => 'website'
));

echo UTILS::Encode($tplStore->construire('header'));

$req = MYSQL::query('SELECT * FROM product_categories WHERE active = true');
if (mysqli_num_rows($req) == 1) { // If there's only one categorie, redirect to it
  $r = mysqli_fetch_object($req);
  $name = html_entity_decode($r->name);
  header("Location: /Categories?cat={$name}");
  exit;
}
$basket = new webshop();
$nbrArticle = $basket->compterArticles();
?>
<div class="content clearfix">
    <div class="section-block intro-title-2" style="background-image: url('<?php echo $bg_img ?>')" id="titleImage">
        <div class="row">
            <div class="column width-12">
                <div class="title-container">
                    <div class="title-container-inner">
                        <h1 class="inline no-margin-bottom"><?php echo $pagename ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="nav-store" id="nav-categorie">
      <ul>
        <?php
        $reqCategories = MYSQL::query('SELECT * FROM product_categories WHERE active = true');
        while ($r = mysqli_fetch_object($reqCategories)) {
            ?>
          <li>
              <a href="/Categories?cat=<?php echo $r->name ?>"><?php echo $r->name ?></a>
              <?php
                $reqFam = MYSQL::query("SELECT * FROM product_family WHERE fam_cat = '{$r->name}' AND (parent IS NULL OR parent like '||')");
                if (mysqli_num_rows($reqFam) > 0) {
              ?>
              <ul>
                <?php while ($r = mysqli_fetch_object($reqFam)) { ?>

                  <li title="<?php echo $r->name ?>">
                  <a href="/Categories?fam=<?php echo $r->id ?>?fam=<?php echo $r->name ?>">
                  <?php echo $r->img ? "<img src='$r->img'>" : '' ?>
                  <p><?php echo $r->name ?></p></a>

                  <?php
                  $req3 = MYSQL::query("SELECT * FROM product_family WHERE parent LIKE '%|{$r->id}|%'");
                  if (mysqli_num_rows($req3) > 0) {
                  ?>
                  <ul>
                    <?php while ($r = mysqli_fetch_object($req3)) { ?>
                    <li title="<?php echo $r->name ?>"><a href="/Categories?fam=<?php echo $r->id ?>?fam=<?php echo $r->name ?>"><p><?php echo $r->name ?></p></a></li>
                    <?php } ?>
                  </ul>
                  <?php } ?>
                  </li>

              <?php } ?>
              </ul>
              <?php } ?>
          </li>
        <?php } ?>
      </ul>
  </nav>

    <div class="section-block pt-40 no-padding-bottom">
        <div class="row">
            <div class="column width-8">
                <div class="product-result-count">
                    <p>
                        <?php
                        // count categories
                        $pcatnum = mysqli_num_rows($req);
                        if ($pcatnum == 1) {
                            echo $pcatnum . " Catégorie";
                        } else {
                            echo $pcatnum . " Catégories";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="column width-4">
              <div class="row">
                <a class="button add-to-cart-button pull-right seeCart <?php if ($nbrArticle > 0) echo "active" ?>" href="/Store/Cart" target="_top">Voir mon panier (<?php echo $nbrArticle ?>)</a>
              </div>
                <!-- <form class="search-form" action="php/search.php" method="post" novalidate>
                    <div class="row">
                        <div class="column width-9">
                            <div class="field-wrapper">
                                <input type="text" name="fname" class="form-fname form-element large" placeholder="Recherher" tabindex="1" required>
                            </div>
                        </div>
                        <div class="column width-3">
                            <a type="submit" class="form-submit button large bkg-theme bkg-hover-theme color-white color-hover-white"><span class="icon-magnifying-glass"></span></a>
                        </div>
                    </div>
                </form> -->
            </div>
            <div class="column width-12">
                <hr>
            </div>
        </div>
    </div>

    <div class="section-block portfolio-carousel products no-padding-top bkg-grey-ultralight">
        <div class="row">
            <div class="column width-12 slider-column no-padding">
                <div id="portfolio-recent-slider-1" class="tm-slider-container recent-slider" data-nav-arrows="true" data-nav-pagination="false" data-carousel-visible-slides="4">
                    <ul class="tms-slides">
                        <!-- load categories -->
                        <?php while ($r = mysqli_fetch_object($req)) { ?>
                            <li class="tms-slide product">
                                <div class="grid-item product portrait grid-sizer design">
                                    <div class="thumbnail product-thumbnail img-scale-in mb-0" data-hover-easing="easeInOut" data-hover-speed="700" data-hover-bkg-color="#ffffff" data-hover-bkg-opacity="0.9">
                                        <a class="overlay-link" href="/Categories?cat=<?php echo $r->name; ?>">
                                            <img src="/site/<?php echo $r->img; ?>" style="height: 360px; width: 255px; object-fit: cover;"/>
                                            <span class="overlay-info">
                                                <span>
                                                    <span>
                                                        <?php echo $r->name; ?>
                                                    </span>
                                                </span>
                                            </span>
                                        </a>
                                        <div class="product-actions">
                                            <a href="/Categories?cat=<?php echo $r->name; ?>" class="button add-to-cart-button small">VOIR</a>
                                        </div>
                                    </div>
                                    <div class="product-details center">
                                        <h3 class="product-title">
                                            <a href="/Categories?cat=<?php echo $r->name; ?>">
                                                <?php echo $r->name; ?>
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
echo UTILS::Encode($tplStore->construire('footer'));
?>
