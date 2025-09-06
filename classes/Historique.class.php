<?php

class Historique {

	private $file;
	private $fileName;

	/*
	 * Ouverture du fichier de logs
	 */
	public function __construct($fileName) {
		$this->fileName = $fileName.'_'.date('d\-m\-Y\_H-\0\0').'.log';
		$this->file = fopen($this->fileName, "a");
	}
	
	/*
	 * Ecriture d'une ligne
	 */
	public function write($texte) {
		fputs($this->file, $texte."\n");
	}
	
	/*
	 * Fermeture du fichier
	 */
	public function destruct() {
		fclose($this->file);
	}
}

?>