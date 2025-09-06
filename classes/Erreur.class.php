<?php


class Erreur {
	
	
	private static $instance = null;
	
	
	//référence vers l'historique des erreurs
	private static $history = null;
	
	//texte des erreurs
	private static $errors = '';

	/* singleton */
	private function __construct() {
		if(self::$history == null)
			self::$history = new Historique('./erreurs/php');
	}
	
	public static function getInstance(){
		if (self::$instance == null){
			self::$instance = new Erreur();
		}
		return self::$instance;
	}
		
	/*
	 * Traitement des erreurs
	 */
	public static function setError($errno, $errstr, $errfile, $errline) {
		new Erreur();

		// On détermine le type d'erreur et on affecte les variables et le cas échéant
		switch ($errno) {
			case E_USER_NOTICE : case E_NOTICE :
				$type = 'Notification';
				break;
			
			case E_COMPILE_WARNING : case E_CORE_WARNING : case E_USER_WARNING : case E_WARNING :
				$type = 'Avertissement';
				break;

			case E_PARSE :
				$type = 'Erreur de syntaxe';

			case E_COMPILE_ERROR : case E_CORE_ERROR : case E_USER_ERROR : case E_ERROR :
				$type = 'Erreur';
				break;

			default :
				$type = 'Erreur inconnue';
				break;
		}

		$uri = $_SERVER['REQUEST_URI'];
		// Construction du message d'erreur
		$text = $type.' le '.date('d/m/Y à H:i:s').' dans le fichier '.$errfile.' à la ligne '.$errline.' '.UTILS::getIp() . " sur {$uri}";
		$message = '<b>'.$text."</b>\r\n<br />" . $errstr . "<br /><br />\r\n\r\n";

		// On enregistre le texte de l'erreur dans la variable
		self::$errors .= $message;

		// On enregistre l'erreur dans le fichier
		self::$history->write($message);
	}
	
	
	
	public function getErrors() {
		return self::$errors;
	}
}


error_reporting(E_ALL);

?>
