<?php
	class Pagination {

		/* singleton */
		private function __construct() {}


        /**
         * Fonction qui retourne une div de Pagination en fonction de plusieurs paramètres
         * @return $html une chaine contenant une div.
         * @param object $chemin
         * @param object $nomget
         * @param object $total
         * @param object $courante[optional]
         * @param object $affichage[optional]
         */

		public static function getPage($req, $nbr=false, $afterView = false){
			if(!$nbr):
				$nbr = 12;
			endif;
			/* On compte le nombre de reponses */
			$req_nbre_reponses = MYSQL::query($req);
			$totalDesMessages = MYSQL::numRows($req_nbre_reponses);
			$nombreDeMessagesParPage = $nbr; // 12 par default
			$nombreDePages = ceil($totalDesMessages / $nombreDeMessagesParPage);
			if($afterView) {
                $nombreDePages = $nombreDePages + 1;
            }
			if(isset($_GET['numpage']) && $_GET['numpage'] == 0): // on vérifie que l'id ne soit pas sur 0 sinon en redirige
                $urlPagin = explode('-page-', $_SERVER['REQUEST_URI']);
                header("location: {$urlPagin[0]}");
            elseif(isset($_GET['numpage']) && $_GET['numpage'] > $nombreDePages):
                $urlPagin = explode('-page-', $_SERVER['REQUEST_URI']);
                header("location: {$urlPagin[0]}"); // si le nombre de page est plus petit que la page courante
            endif;


			//Numéro de Page courante
			if(!isset($_GET['numpage'])) {
                $page = 1;
                $urlPagin = $_SERVER['REQUEST_URI'];
            }elseif(is_numeric($_GET['numpage']) && $_GET['numpage']<=$nombreDePages){
                $page = $_GET['numpage'];
                $urlPagin = explode('-page-', $_SERVER['REQUEST_URI']);
                unset($urlPagin[sizeof($urlPagin)-1]);
                $urlPagin = implode('-page-', $urlPagin);
            }else{
                $page = $nombreDePages;
                $urlPagin = explode('-page-', $_SERVER['REQUEST_URI']);
                unset($urlPagin[sizeof($urlPagin)-1]);
                $urlPagin = implode('-page-', $urlPagin);

            }

            //Calcul de la clause LIMIT

			$PageStart = $page*$nombreDeMessagesParPage-$nombreDeMessagesParPage;

			return array($urlPagin, $nombreDePages, $page, $PageStart, $nombreDeMessagesParPage); // on retourne le tout sous forme d'un tableau
		}

		public static function viewPagination($chemin,$nomget,$total,$courante=1,$affichage=2){

            //variable contenant le code HTML a retourner
            $html = '';
            //Si il n'y a pas plus d'une page on renvoit rien...
            if($total<=1)
                return $html;

            $precedent = $courante-1;
            $suivant = $courante+1;
            $textePrecedent = '&laquo;';
            $texteSuivant = '&raquo;';

            $html .= '<ul class="pagination m-0 mb-3">';

            /*Boutons précédent*/
            if ($courante == 2) // si on est sur la page 2, Nous retournons sur la page initiale (permet d'éviter les doublons index.php et index.php?page=1)
                $html.= Pagination::Url($chemin,$textePrecedent);
            elseif($courante > 2) // si la page courante est supérieure à 2 le bouton précédent renvoit sur la page dont le numéro est immédiatement inférieur
                $html.= Pagination::Url($chemin,$textePrecedent,$nomget,$precedent);
            else // sinon on désactive le bouton précédent
                $html.= '<li class="page-item active"><span class="page-link pagination-padding">'.$textePrecedent.'</a></li>';

            /*Affichage des numéros des pages*/

            if($total < 7 + $affichage*2){
                //affiche tous les numéros
                $html.= ($courante == 1) ? '<li class="page-item active"><a class="page-link" href="#">1</a></li>' : Pagination::Url($chemin,'1',$nomget,1);

                // On boucle toutes les pages restantes boucle for
                for ($i = 2; $i <= $total; $i++){
                    if ($i == $courante) // La page courante est affichée différemment
                        $html.= '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
                    else
                        $html.= Pagination::Url($chemin,$i,$nomget,$i);
                }
            } elseif($total > 2 + ($affichage * 2)){
                /*Il y'en a trop donc il va falloir des "..." */
                if($courante < 1+($affichage * 2)){
                    $html.= ($courante == 1) ? '<li class="page-item active"><a class="page-link" href="#">1</a></li>' : Pagination::Url($chemin,'1',$nomget,1);

                     // On boucle toutes les pages restantes boucle for
                   for($i = 2; $i < 4 + ($affichage * 2); $i++){
                        if ($i == $courante)// La page courante est affichée différemment
                            $html.= '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
                        else
                            $html.= Pagination::Url($chemin,$i,$nomget,$i);
                    }
                      // les ... pour marquer la troncature
                    $html.= '<li class="page-item"><a class="page-link" href="#">...</a></li>';

                    // et enfin les deux dernières pages
                    $html.= Pagination::Url($chemin,$total-1,$nomget,$total-1);
                    $html.= Pagination::Url($chemin,$total,$nomget,$total);
                }elseif($total - ($affichage * 2) > $courante && $courante > ($affichage * 2)){
                    // on affiche les deux premières pages
                    $html.= Pagination::Url($chemin,'1',$nomget,1);
                    $html.= Pagination::Url($chemin,'2',$nomget,2);

                    // les ... pour marquer la troncature
                    $html.= '<li class="page-item"><a class="page-link" href="#">...</a></li>';

                    // puis sept pages : les trois précédent la page courante, la page courante, puis les trois lui succédant
                    for ($i= $courante - $affichage; $i<= $courante + $affichage; $i++){
                        if ($i== $courante)
                            $html.= '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
                        else
                            $html.= Pagination::Url($chemin,$i,$nomget,$i);
                    }

                    // les ... pour marquer la troncature
                    $html.= '<li class="page-item"><a class="page-link" href="#">...</a></li>';

                    // et enfin les deux dernière spages
                    $html.= Pagination::Url($chemin,$total-1,$nomget,$total-1);
                    $html.= Pagination::Url($chemin,$total,$nomget,$total);
                }
                 else{
                    // on affiche les deux premières pages
                    $html.= Pagination::Url($chemin,'1',$nomget,1);
                    $html.= Pagination::Url($chemin,'2',$nomget,2);

                    // les ... pour marquer la troncature
                    $html.= '<li class="page-item "><a class="page-link" href="#">...</a></li>';

                    // et enfin les neuf dernières pages
                    for ($i = $total - (2 + ($affichage * 2)); $i <= $total; $i++){
                        if ($i == $courante)
                            $html.= '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
                        else
                            $html.= Pagination::Url($chemin,$i,$nomget,$i);
                    }
               }
            }

            /*Bouton suivant*/

            if ($courante != $total)
                $html .= Pagination::Url($chemin,$texteSuivant,$nomget,$suivant);
            else
                $html.= '<li class="page-item active"><span class="page-link pagination-padding">'.$texteSuivant.'</span></li>';

            $html .= '</ul>';

            return $html;
        }

        /**
         * Méthode qui renvoit un lien en fonction de plusieurs paramètres
         * @return $lien un lien
         * @param object $chemin notre fichier
         * @param object $texte texte du lien
         * @param object $parametre[optional] parametre GET
         * @param object $valeur[optional] valeur du parametre GET
         */
        public static function Url($chemin,$texte,$parametre=false,$valeur=false){
            $lien = '<li class="page-item"><a class="page-link" href="'.$chemin;

            if(!empty($parametre))
                $lien .= '-'.$parametre.'-'.$valeur;

            $lien .= '">'.$texte.'</a></li>';
            return $lien;
        }
   }
?>
