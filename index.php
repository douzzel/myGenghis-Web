<?php

$devMode = getenv('DEV') ? true : false;

if ($devMode) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

ini_set('session.cache_limiter','public');
session_cache_limiter(false);
date_default_timezone_set('Europe/London');
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

// * Load class and entities
spl_autoload_register(
	function ($x) {
		$sources = array('./classes/' . str_replace('_', '/', $x) . '.class.php');
		foreach ($sources as $source) {
			if (file_exists($source)) {
				require_once $source;
			}
		}
		$sources = array('./classes/entities/' . str_replace('_', '/', $x) . '.class.php');
		foreach ($sources as $source) {
			if (file_exists($source)) {
				require_once $source;
			}
		}
		$sources = array('./classes/nupa/' . str_replace('_', '/', $x) . '.class.php');
		foreach ($sources as $source) {
			if (file_exists($source)) {
				require_once $source;
			}
		}
	}
);

require_once('./classes/defines.php');
require_once('./classes/vendor/autoload.php');

// On charge les paniers
$basket = new webshop();
$site = UTILS::getFunction('StaticUrl');

// demarre les redirections
UTILS::initOutputFilter();

// on bloque l'accès fichier.php
if (preg_match('#index.php#i', $_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], '/?') === 0) {
	UTILS::addHack('Tentative d\'accès direct à index.php (sans url rewriting).');
	header("Location: /404");
	exit;
}

// * Create twig renderer
$loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) ."/themes/html/");
$twig = new \Twig\Environment($loader);

$twig->addFilter(new \Twig\TwigFilter('verifyRights', function ($e) {
	return ManageRights::verifyRights($e, 'Read', false, false);
}));

$twig->addFilter(new \Twig\TwigFilter('isModuleActive', function ($e) {
	return UTILS::isModuleActive($e);
}));

$twig->addFilter(new \Twig\TwigFilter('getFunction', function ($e) {
	return UTILS::getFunction($e);
}));

$twig->addFilter(new \Twig\TwigFilter('unescape', function ($e) {
	return html_entity_decode($e);
}));

$twig->addFilter(new \Twig\TwigFilter('get_class', function ($e) {
	return get_class($e);
}));

// * In production, log in erreurs/.log file
// * In development, log on web page
if (!$devMode) {
    set_error_handler(['Erreur', 'setError']);
}

// * start user sessions
USER::init();

// * Load fonts
$fontDirectory = __DIR__."/uploads/fonts";
$outputFonts = "";
$listFonts = $nameFonts = [];
if (is_dir($fontDirectory)) {
    $direc = opendir($fontDirectory);
    while ($file = readdir($direc)) {
      if ($file != '.gitkeep') {
        if (is_file("$fontDirectory/$file")) {
                $name = pathinfo("$fontDirectory/$file", PATHINFO_FILENAME);
                $ext = pathinfo("$fontDirectory/$file", PATHINFO_EXTENSION);
                $outputFonts .= "@font-face { font-family: '{$name}'; src:url('/uploads/fonts/{$file}'); }";
				$listFonts[] = "'{$name}={$name}'";
				$nameFonts[] = $name;
            }
        }
    }
    closedir($direc);
}

// * Load page
$PAGE = "";
if (DATA::isGet('page')) {
	$page = DATA::getGet('page');
	if (!preg_match('#\.\.#', $page) && !preg_match('#://#', $page) && file_exists('./pages/' . $page . '.php')) {
		include('./pages/' . $page . '.php');
	} else {
		UTILS::addHack('Tentative d\'accès à une page inexistante.');
		header("Location: /404");
		exit;
	}
} else {
	$page = UTILS::getFunction('defaultPage');
	$page = $page ? $page : '/Connexion';
	header("Status: 301 Moved Permanently", false, 301);
	header("Location: {$page}");
	exit;
}


if (isset($_COOKIE['dragDrag'])) {
	$TPL['DRAGGABLE'] = true;
}

$TPL = [
	'DEFAULT_FONTS' => '--font-my-genghis: "'.(UTILS::getFunction('fontMyGenghis') ?? 'Roboto').'";
					 '.'--font-text: "'.(UTILS::getFunction('fontText') ?? 'Roboto').'";
					 	--font-title: "'.(UTILS::getFunction('fontTitle') ?? 'Lato').'";',
	'FONTS_LIST' => implode(",", $listFonts),
	'FONTS' => $outputFonts,
	'STATIC_URL' => $site,
	'SITE_NAME' => UTILS::getFunction('SiteName'),
	'URL' => UTILS::getFunction('isHttps')."://".UTILS::getFunction('SiteName'),
	'CUSTOM_MENU' => UTILS::getInitialesSiteName(),
	'URL_DYNAMIQUE' => $_SERVER['REQUEST_URI'],
	'CACHE_MAINCSS' => filemtime('./themes/assets/css/_main.css'),
	'CACHE_MAINJS' => filemtime('./themes/assets/js/_main.js'),
	'action' => DATA::getGet('Action'),
	'page' => DATA::getGet('Page'),
	'TITRE' => !isset($TITRE) ? 'Aucun titre associé à la page' : $TITRE,
	'DESCRIPTION' => isset($DESCRIPTION) ? $DESCRIPTION : '',
	'NOTIFICATIONS' => DATA::getSession('notification'),
	'PAGE' => $PAGE,
	'YEAR' => date('Y'),
];
unset($_SESSION['notification']);

if (ManageRights::verifyRights('Menu', 'Read', false, false)) {
	// // * System usage
	// function usage($usage) {
	// 	if ($usage < 75)
	// 		return 'color-success';
	// 	else if ($usage < 90)
	// 		return 'color-warning';
	// 	else
	// 		return 'color-danger';
	// }
	// $memory = round(UTILS::getServerMemoryUsage(true));
	// $diskfree = round(disk_free_space(".") / 1000000000);
	// $disktotal = round(disk_total_space(".") / 1000000000);
	// $diskused = round(($disktotal - $diskfree) / $disktotal * 100);
	// if (!stristr(PHP_OS, 'win')) {
	// 	$load = sys_getloadavg();
	// 	$cpuload = $load[0];
	// } else {
	// 	$cpuload = "";
	// }

	// $TPL = array_merge($TPL, [
	// 	'MEMORY' => $memory,
	// 	'MEMORY_USAGE' => usage($memory),
	// 	'DISK' => $diskused,
	// 	'DISK_USAGE' => usage($diskused),
	// 	'CPU' => $cpuload,
	// 	'CPU_USAGE' => usage($cpuload),
	// 	'TIME' => UTILS::date('', 'H:i:s'),
	// 	'DATE' => UTILS::date('', 'l d  F Y'),
	// ]);


}

function createActionMenu($options) {
	return $GLOBALS['twig']->render('Templates/menu-action.twig', ['options' => $options]);
}

if ($devMode) {
	$TPL['THEMES'] = '/themes'; // Load JS and CSS directly instead of minify them
	echo $twig->render('_main.twig', $TPL); // Send uncompressed HTML
} else {
	echo UTILS::compressHtml(UTILS::Encode($twig->render('_main.twig', $TPL))); // Compress HTML and send it
}

// The bandwidth is freed up after the page is loaded.
$contenuVariable = array_keys(get_defined_vars());
foreach ($contenuVariable as $var) {
	unset($var);
}
