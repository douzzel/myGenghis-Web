<?php
    class webshop {

        public function creationPanier(){
            if (!isset($_SESSION['panier'])){
               $_SESSION['panier']=array();
               $_SESSION['panier']['libelleProduit'] = array();
               $_SESSION['panier']['qteProduit'] = array();
               $_SESSION['panier']['prixProduit'] = array();
               $_SESSION['panier']['verrou'] = false;
            }
            return true;
        }

        public function ajouterArticle($libelleProduit, $qteProduit, $prixProduit){
            if ($this->creationPanier() && !$this->isVerrouille()) {
               //Si le produit existe déjà on ajoute seulement la quantité
               $positionProduit = array_search($libelleProduit,  $_SESSION['panier']['libelleProduit']);

               if ($positionProduit !== false) {
                  $_SESSION['panier']['qteProduit'][$positionProduit] += $qteProduit ;
               } else {
                  //Sinon on ajoute le produit
                  array_push( $_SESSION['panier']['libelleProduit'],$libelleProduit);
                  array_push( $_SESSION['panier']['qteProduit'],$qteProduit);
                  array_push( $_SESSION['panier']['prixProduit'],$prixProduit);
               }
            } else {
                echo "Un problème est survenu veuillez contacter l'administrateur du site.";
            }
        }

        public function supprimerArticle($libelleProduit){
            //Si le panier existe
            if ($this->creationPanier() && !$this->isVerrouille()){
               //Nous allons passer par un panier temporaire
               $tmp=array();
               $tmp['libelleProduit'] = array();
               $tmp['qteProduit'] = array();
               $tmp['prixProduit'] = array();
               $tmp['verrou'] = $_SESSION['panier']['verrou'];

               for($i = 0; $i < count($_SESSION['panier']['libelleProduit']); $i++){
                    if ($_SESSION['panier']['libelleProduit'][$i] !== $libelleProduit){
                        array_push( $tmp['libelleProduit'],$_SESSION['panier']['libelleProduit'][$i]);
                        array_push( $tmp['qteProduit'],$_SESSION['panier']['qteProduit'][$i]);
                        array_push( $tmp['prixProduit'],$_SESSION['panier']['prixProduit'][$i]);
                    }
                }
               //On remplace le panier en session par notre panier temporaire à jour
               $_SESSION['panier'] =  $tmp;
               //On efface notre panier temporaire
               unset($tmp);
            } else {
               echo "Un problème est survenu veuillez contacter l'administrateur du site.";
            }
        }

        public function modifierQTeArticle($libelleProduit, $qteProduit){
            //Si le panier existe
            if ($this->creationPanier() && !$this->isVerrouille()){
               //Si la quantité est positive on modifie sinon on supprime l'article
               if ($qteProduit > 0) {
                  //Recherche du produit dans le panier
                  $positionProduit = array_search($libelleProduit,  $_SESSION['panier']['libelleProduit']);

                  if ($positionProduit !== false)
                  {
                     $_SESSION['panier']['qteProduit'][$positionProduit] = $qteProduit ;
                  }
               } else {
                $this->supprimerArticle($libelleProduit);
               }
            } else {
                echo "Un problème est survenu veuillez contacter l'administrateur du site.";
            }
        }

        public function MontantGlobal(){
            $total=0;
            for($i = 0; $i < count($_SESSION['panier']['libelleProduit']); $i++) {
               $total += $_SESSION['panier']['qteProduit'][$i] * $_SESSION['panier']['prixProduit'][$i];
            }
            return $total;
        }

        public function isVerrouille(){
            if (isset($_SESSION['panier']) && $_SESSION['panier']['verrou'])
            return true;
            else
            return false;
        }

        public function compterArticles() {
            if (isset($_SESSION['panier']))
            return count($_SESSION['panier']['libelleProduit']);
            else
            return 0;
        }

        public function supprimePanier(){
            unset($_SESSION['panier']);
        }

        public function addArticle($ajax = false){
            $erreur = false;

            $action = (DATA::isPost('addItems') ? DATA::getPost('addItems') :  (DATA::isGet('addItems') ? DATA::getGet('addItems') : null)) ;
            if($action !== null){

                if(!in_array($action,array('ajout', 'suppression', 'refresh'))) $erreur=true;

                //récuperation des variables en POST ou GET
                $libele = (DATA::isPost('libele') ? DATA::getPost('libele') :  (DATA::isGet('libele') ? DATA::getGet('libele') : null ));
                $price = (DATA::isPost('price') ? DATA::getPost('price') :  (DATA::isGet('price') ? DATA::getGet('price') : null ));
                $quantities = (DATA::isPost('quantities') ? DATA::getPost('quantities') :  (DATA::isGet('quantities') ? DATA::getGet('quantities') : null ));


                //Suppression des espaces verticaux
                $libele = preg_replace('#\v#', '',$libele);
                //On vérifie que $p soit un float
                $price = floatval($price);

                //On traite $q qui peut être un entier simple ou un tableau d'entiers

                if (is_array($quantities)){
                    $QteArticle = array();
                    $i=0;
                    foreach ($quantities as $contenu){
                        $QteArticle[$i++] = intval($contenu);
                    }
                } else {
                    $quantities = intval($quantities);
                }
            }

            if (!$erreur){
                switch($action){

                    Case "ajout":
                        $this->ajouterArticle($libele,$quantities,$price);
                        $message = 'Article ajouté au panier';
                        $type = 'success';
                        break;

                    Case "suppression":
                        $this->supprimerArticle($libele);
                        $message = 'Article supprimé du panier';
                        $type = 'danger';
                    break;

                    Case "refresh" :
                        for ($i = 0 ; $i < count($QteArticle) ; $i++)
                        {
                            $this->modifierQTeArticle($_SESSION['panier']['libelleProduit'][$i],round($QteArticle[$i]));
                        }
                        $type = 'success';
                        $message = 'Panier modifier';
                        break;

                    Default:
                        break;
                }
            }

            if($ajax){
                $data = '<div class="notification">';
                $data .= '<div class="alert alert-'.$type.' w-50 d-flex align-items-center justify-content-between" role="alert">';
                $data .= $message;
                $data .= '<button type="button" class="close" onclick="$(\'.notification\').remove();" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>';
                $data .= '</div>';
                $data .= '</div>';
                die(json_encode(array('data' => $data, 'nbr' => $this->compterArticles())));
            }

        }

        public function distance($lat1, $lng1, $lat2, $lng2, $unit = 'k') {
            $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon
            $rlo1 = deg2rad($lng1);
            $rla1 = deg2rad($lat1);
            $rlo2 = deg2rad($lng2);
            $rla2 = deg2rad($lat2);
            $dlo = ($rlo2 - $rlo1) / 2;
            $dla = ($rla2 - $rla1) / 2;
            $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
            $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
            //
            $meter = ($earth_radius * $d);
            if ($unit == 'k') {
                return $meter / 1000;
            }
            return $meter;
        }

        public function arrondi_distance($distance_metre) {
            $resultat = round($distance_metre);
            $len = strlen($resultat) - 2;
            if ($len > 0) {
                $resultat = round($distance_metre / pow(10, $len), 0) * pow(10, $len);
                if ($resultat >= 10000) {
                    return number_format(($resultat / 1000), 0, ',', '');
                } elseif ($resultat >= 1000) {
                    return preg_replace('/,0$/', '', number_format(($resultat / 1000), 1 , ',', ''));
                } else {
                    return $resultat;
                }
            } else {
                return $resultat;
            }
        }

        public function geocodeDistance($origin, $destination){
            $data = array('distance' => '', 'duration' => '', 'end_address' => '');
            $origin = str_replace(" ", "+", $origin);
            if (str_replace(" ", "", $destination) != "" && str_replace(" ", "", $destination) != "0") {
                $destination = str_replace(" ", "+", html_entity_decode($destination));
                $json = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/directions/json?origin=".$origin."&destination=".$destination. "&avoid=highways&key=AIzaSyDm9D4gBb6eV99I1IkXrXXKohOm6YYc1n8"), true);
                if ($json['geocoded_waypoints'][0]['geocoder_status'] == 'OK' && count($json['routes']) > 0) {
                    //var_dump($json);
                    $data['distance'] = $this->arrondi_distance($json['routes'][0]['legs'][0]['distance']['value']);
                    $data['duration'] = date('H:i', $json['routes'][0]['legs'][0]['duration']['value']);
                    $data['end_address'] = $json['routes'][0]['legs'][0]['end_address'];
                }
            }

            return $data;
        }

        public function zone($kilometer) {
            $req = MYSQL::query('SELECT * FROM delivery');
            if (mysqli_num_rows($req) > 0) {
                while ($result = mysqli_fetch_object($req)) {
                    // make sure that checked distance is above 'min' if value is equal to 'min', otherwise leave value (mainly for cases where 'min' = "0")
                    if ($result->min) {
                      $kilometer = (($kilometer == $result->min) ? $kilometer + 1 : $kilometer);
                    }
                    // make sure that checked distance is below 'max' if value is equal to 'max', otherwise leave value
                    if ($result->max) {
                      $kilometer = (($kilometer == $result->max) ? $kilometer - 1 : $kilometer);
                    }
                    // check which 'zone' distance is in for delivery and return corresponding 'zone'
                    if (($kilometer >= $result->min || $result->min) && ($kilometer <= $result->max || $result->max)) {
                        return $result->zone;
                    }
                }
            }
        }

        public function geocodeAddress($address) {
            //valeurs vide par défaut
            $data = array('address' => '', 'lat' => '', 'lng' => '', 'city' => '', 'department' => '', 'region' => '', 'country' => '', 'postal_code' => '');
            //on formate l'adresse
            $address = str_replace(" ", "+", $address);

            //on fait l'appel à l'API google map pour géocoder cette adresse
            $json = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=AIzaSyDm9D4gBb6eV99I1IkXrXXKohOm6YYc1n8"));

            //on enregistre les résultats recherchés
            if ($json->status == 'OK' && count($json->results) > 0) {

                $res = $json->results[0];
                //var_dump($res);
                //adresse complète et latitude/longitude
                $data['address'] = $res->formatted_address;
                $data['lat'] = $res->geometry->location->lat;
                $data['lng'] = $res->geometry->location->lng;
                foreach ($res->address_components as $component) {
                    //ville
                    if ($component->types[0] == 'locality') {
                        $data['city'] = $component->long_name;
                    }
                    //départment
                    if ($component->types[0] == 'administrative_area_level_2') {
                        $data['department'] = $component->long_name;
                    }
                    //région
                    if ($component->types[0] == 'administrative_area_level_1') {
                        $data['region'] = $component->long_name;
                    }
                    //pays
                    if ($component->types[0] == 'country') {
                        $data['country'] = $component->long_name;
                    }
                    //code postal
                    if ($component->types[0] == 'postal_code') {
                        $data['postal_code'] = $component->long_name;
                    }
                }
            }
            return $data;
        }
    }
?>
