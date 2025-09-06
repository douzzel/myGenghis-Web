<?php
UTILS::addHistory(USER::getPseudo(), 10, 'Déconnexion du site '.UTILS::getFunction('SiteName'));
foreach($_SESSION as $key => $value) {
    if ($key != 'panier' && $key != 'last_login') {
        unset($_SESSION[$key]);
    }
}

// HumHub logout
$hhFile = "./classes/humhub";
if(is_dir($hhFile)) {
      hupa::sessionDel(USER::getPseudo());
}

UTILS::notification('warning', 'Vous êtes déconnecté', false, true);
header("Location: /Connexion");
exit;
