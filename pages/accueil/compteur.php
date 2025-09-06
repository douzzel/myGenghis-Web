<?php

    $saved_date = null;
    // Paramètres du compteur
    $keep = 48;	// durée de conservation des IPs en heures (défaut : 48h)
    $interval = 1;	// intervalle de temps en minutes pour compter le nombre de connectés des X dernières minutes (défaut : 5 minutes)
    $unique = 24;	// durée en heures pendant laquelle une IP est comptée comme unique (défaut : 24h)
    $initial = 0;	// nombre initial de visiteurs au compteur
    $exclude = array();	// liste des IPs (partielles ou complètes) à ajouter sous la forme array('127.0.0.1', '168.254.')

    // Récupération des données témoins
	$req = MYSQL::QUERY("SELECT	DATE_FORMAT(c_lastvisit, '%Y-%m-%d') AS c_lastvisit, c_total FROM compteur WHERE c_iphost = 'temoin'");
	if(mysqli_num_rows($req)){
        $data = mysqli_fetch_object($req);
        // Nombre de visites total
	    $total = $data->c_total;

	    // Dernier jour traité
	    $saved_date = $data->c_lastvisit;
    }


	// Aujourd'hui et maintenant
	$today = date('Y-m-d');
	$now = time();

	// Si changement de jour
	if(($today != $saved_date)){
		// Le nombre de visites de chaque visiteur de la base est remis à 0
		MYSQL::QUERY("UPDATE compteur SET c_total = 0 WHERE c_iphost != 'temoin'");

        // La date stockée est mise à jour à la date d'aujourd'hui
		MYSQL::QUERY("UPDATE compteur SET c_lastvisit = CURDATE() WHERE c_iphost = 'temoin'");

        // On vide les lignes obsolètes
		$exceed = $now - ($keep*60*60);
		MYSQL::QUERY('DELETE FROM compteur WHERE c_iphost != "temoin" AND UNIX_TIMESTAMP(c_lastvisit) < '.$exceed.'');
	}


	/**********************************
	* Fonction de vérification des IP *
	**********************************/

	// Fonction qui vérifie si l'IP est exclue du comptage ou pas
	function ipcheck($ip_to_match, $ip_array)
	{
		if (is_array($ip_array))
		{
			foreach ($ip_array as $ip)
			{
				if (strpos($ip_to_match, $ip)===0)
				return true;

			}
		}

		return false;
	}

    /*************************
	* Traitement des visites *
	*************************/

	$ip = $_SERVER['REMOTE_ADDR'];

	// Si l'IP n'est pas dans la liste de celles à exclure
	if(ipcheck($ip, $exclude) != true){
		// On compte le nombre d'entrées correspondant à l'IP de notre visiteur
		$res = MYSQL::QUERY("SELECT COUNT(*) FROM compteur WHERE c_ip = '$ip'");
		$row = mysqli_fetch_row($res);

		// Si aucune IP ne correspond, le visiteur est nouveau dans la base de données
		if($row[0] == 0)
		{
			$iphost = gethostbyaddr($ip);
			// Alors on ajoute son heure de connexion, son IP, et on initialise son nombre de visites à 1
			MYSQL::QUERY("INSERT INTO compteur (c_firstvisit, c_lastvisit, c_total, c_ip, c_iphost) VALUES (NOW(), NOW(), 1, '$ip', '$iphost')");

			// Et on incrémente le nombre de visiteurs
			MYSQL::QUERY("UPDATE compteur SET c_total = c_total+1 WHERE c_iphost = 'temoin'");
		}
		/*
		Si il est déjà dans la base, alors :
		- soit la période est dépassée, alors le visiteur est considéré comme nouveau
		- soit il est déjà venu dans la même période d'unicité d'un visiteur
		*/
		else
		{
			// On récupère toutes les données qui lui correspondent
			$visite = MYSQL::QUERY("SELECT UNIX_TIMESTAMP(c_firstvisit) AS c_firstvisit FROM compteur WHERE c_ip = '$ip'");
			$data = mysqli_fetch_array($visite);

			// On récupère la date de ses première et dernière visites
			$firstvisit = $data['c_firstvisit'];

			// Si la période est dépassée
			if(($now - $firstvisit) > ($unique*60*60))
			{
				// Incrémentation du compteur total
				MYSQL::QUERY("UPDATE compteur SET c_total = c_total+1 WHERE c_iphost = 'temoin'");

				// On compte le visiteur comme nouveau, même si c'est dans la même journée
				MYSQL::QUERY("UPDATE compteur SET c_firstvisit = NOW(), c_lastvisit = NOW(), c_total = c_total+1 WHERE c_ip = '$ip'");

			}
			// Sinon on est dans la même période d'unicité
			else
			{
				// On met uniquement à jour l'heure de son dernier passage
				MYSQL::QUERY("UPDATE compteur SET c_lastvisit = NOW() WHERE c_ip = '$ip'");

			}
		}
	}


	/***********************
	* Stockage des données *
	***********************/

	// // Nombre de visites total
	// $qry = MYSQL::QUERY("SELECT c_total FROM compteur WHERE c_iphost = 'temoin'");
  // $alltime = mysqli_fetch_array($qry);
  // var_dump($alltime);
	// $c_alltime = $alltime['c_total'];

	// // Nombres de visiteurs quotidiens
	// $qry = MYSQL::QUERY("SELECT SUM(c_total) AS c_total FROM compteur WHERE c_iphost != 'temoin'");
	// $today = mysqli_fetch_array($qry);
	// $c_today = $today['c_total'];

	// // Nombre de visiteurs en ligne
	// $lastmin = $now - ($interval*60);
	// $res = MYSQL::QUERY("SELECT COUNT(*) FROM compteur WHERE (c_iphost != 'temoin') AND (UNIX_TIMESTAMP(c_lastvisit) >= $lastmin)");
	// $row = mysqli_fetch_row($res);
	// $c_online = $row[0];
?>
