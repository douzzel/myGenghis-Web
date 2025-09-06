<?php

    class EntityAffectations{

        private $id;
        private $nom;
        private $montant;
        private $date_created;
        private $categories_id;
        private $contrat_id;

        public function getId(){

            return $this->id;
        }
        public function getNom(){

            return $this->nom;
        }
        public function getMontant(){
            return $this->montant;
        }
    }

?>