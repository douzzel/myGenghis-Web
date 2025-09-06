<?php

    class EntityContratEmprunt{

        private $id;
        private $objet_du_contrat;
        private $numero_contrat;
        private $montant;
        private $id_mode_calcul;
        private $id_periodicite;
        private $duree;
        private $date_deblocage_initial;
        private $montant_deblocage_initial;
        private $date_premier_versement;
        private $jour_echeance;
        private $terme_echoir;
        private $taux_interet;
        private $taux_assurance;
        private $assurance_fixe;
        private $taux_commission;
        private $commission_fixe;
        private $frais_dossier;
        private $type_garantie_id;
        private $tva;
        private $tiers_id;
        private $date_created;

        public function getId(){

            return $this->id;
        }
        public function getObjetDuContrat(){

            return $this->objet_du_contrat;
        }

        
    }
?>