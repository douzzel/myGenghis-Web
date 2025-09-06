<?php

spl_autoload_register(
	function ($x) {
		$sources = array('../../classes/' . str_replace('_', '/', $x) . '.class.php'); // chargement des classes
		foreach ($sources as $source) {
			if (file_exists($source)) {
				require_once $source;
			}
		}
		$sources = array('../../classes/entities/' . str_replace('_', '/', $x) . '.class.php'); // chargement des classes
		foreach ($sources as $source) {
			if (file_exists($source)) {
				require_once $source;
			}
		}
	}
);

require_once('../../classes/vendor/autoload.php');

USER::init();

if (DATA::isGet('weblink')) {
    $filter = ['weblink' => DATA::getGet('weblink')];
    $document = Generique::selectOne('documents', 'graphene_bsm', $filter);
    if (!$document) {
        header('Location: /');

        exit;
    }
    $loader = new \Twig\Loader\FilesystemLoader('../../themes/html/');
    $twig = new \Twig\Environment($loader);

    $attachments = [];
    if ($document->getAttachments()) {
        $attachments = call_user_func_array('array_merge', unserialize($document->getAttachments()));
    }
    $PAGES = $twig->render('documents/index.twig', [
        'document' => $document,
        'attachments' => $attachments,
        'CONTENT' => html_entity_decode($document->getContent()),
    ]);
    echo  UTILS::compressHtml(UTILS::Encode($PAGES));

    $contenuVariable = array_keys(get_defined_vars());
    foreach ($contenuVariable as $var) {
        unset($var);
    }
} else if (DATA::isGet(['folder', 'file'])) {
    $filename = html_entity_decode(DATA::getGet('file'));
	$folder = DATA::getGet('folder');
    $filePath = $_SERVER['DOCUMENT_ROOT']."/uploads/Drive/{$folder}/{$filename}";
    if (!file_exists($filePath))
        exit;
    $file = Generique::selectOne('documents_files', 'graphene_bsm', ['name' => $filename, 'folder' => $folder]);
    if ($file->getOwner() == USER::getId() || ManageDocuments::verifyRights('folder', $file->getFolder())) {
        header("Content-type: {$file->getType()}");
        header("Content-disposition: attachment; filename={$filename}");
        readfile($filePath);
    } else {
        header('Location: /');
    }
	exit;
} else {
	header('Location: /');

	exit;
}
