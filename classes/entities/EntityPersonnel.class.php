<?php

    class EntityPersonnel
    {
        private int $id;
        private ?int $id_client;
        private string $nom;
        private string $prenom;
        private string $sexe;
        private string $datedenaissance;
        private string $adresse;
        private string $poste;
        private int $codePostal;
        private string $ville;
        private int $idcontrat;
        private string $DPAE;
        private string $entrer;
        private ?string $sortie;
        private int $ficheDePoste;
        private int $ficheDePaie;

        public function getId()
        {
                return $this->id;
        }

        public function getNom()
        {
                return $this->nom;
        }

        public function getPrenom()
        {
                return $this->prenom;
        }

        public function getCompleteName()
        {
                return "{$this->nom} {$this->prenom}";
        }

        public function getIdClient()
        {
                return $this->id_client;
        }

        public function getSortie()
        {
                return $this->sortie;
        }

        public function getPoste() {
                return ($this->ficheDePoste ? MYSQL::selectOneValue("SELECT service FROM fiche_poste WHERE id = '{$this->ficheDePoste}'") : '');
        }

        public function getFicheDePoste()
        {
                return $this->ficheDePoste;
        }

        public function getAvatar() {
                $pseudo = MYSQL::selectOneValue("SELECT Pseudo FROM accounts WHERE id_client = '{$this->id_client}'");
                return UTILS::GetAvatar($pseudo);
        }
    }
