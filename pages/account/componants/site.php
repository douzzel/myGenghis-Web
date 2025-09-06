<?php
$tplSite = new Template;
$tplSite->setFile('site', './account/componants/site.html');

$tplSite->values(array(
  'URL' => UTILS::getFunction('defaultPage')
));

$PAGES = $tplSite->construire('site');
