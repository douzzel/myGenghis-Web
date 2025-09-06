<?php

class EntityContacts
{
    private int $id;
    private ?int $id_client;
    private ?int $ref_win;
    private ?string $ref_dos;
    private ?string $code_abr;
    private ?string $nom = null;
    private ?string $adresse = null;
    private ?string $address_complement = null;
    private ?string $adr1;
    private ?string $adr2;
    private ?string $cp = null;
    private ?string $ville = null;
    private ?string $pays = null;
    private ?string $telephone = null;
    private ?string $fax;
    private ?string $couriel = null;
    private ?string $portable = null;
    private ?string $ref_cat;
    private ?string $categorie = null;
    private ?string $comment;
    private ?string $ref_win_2;
    private ?string $ref_ste;
    private ?string $nom_2;
    private ?string $prenom = null;
    private ?string $civilite = null;
    private ?string $fonction;
    private ?string $tel_2 = null;
    private ?string $fax_2;
    private ?string $couriel_2;
    private ?string $portable_2;
    private ?string $adr_perso;
    private ?string $cp_perso;
    private ?string $pays_perso;
    private ?string $tel_perso;
    private ?string $fax_perso;
    private ?string $couriel_perso;
    private ?string $comment_ctc;
    private ?string $creation_date;
    private ?int $id_groupe_contacts;
    private ?string $tier_id = null;

    public function getId()
    {
        return $this->id;
    }

    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getCouriel()
    {
        return $this->couriel;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getPays()
    {
        return $this->pays;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function getCp()
    {
        return $this->cp;
    }

    public function getFullAdresse() {
        return "{$this->adresse} {$this->cp} {$this->ville} {$this->pays}";
    }

    public function createSimpleContact($nom, $prenom) {
        $this->id = 0;
        $this->nom = $nom;
        $this->prenom = $prenom;
    }

    public function createMailContact($mail) {
        $this->id = 0;
        $this->couriel = $mail;
    }

    public function getPortable()
    {
        return $this->portable;
    }

    public function getJob()
    {
        return $this->tel_2;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getPhone() {
        return $this->getPortable() ?? $this->getTelephone() ?? $this->getJob();
    }

    public function getCategory()
    {
        return $this->categorie ? html_entity_decode($this->categorie) : '';
    }

    public function getTierId()
    {
        return $this->tier_id;
    }

    public function getCivilite()
    {
        return $this->civilite;
    }

    public function getFullName()
    {
        return $this->civilite ? "{$this->civilite} {$this->nom} {$this->prenom}" : ($this->nom && $this->prenom ? "{$this->nom} {$this->prenom}" : ($this->nom ? $this->nom : $this->prenom));
    }

    public function getAddressComplement()
    {
        return $this->address_complement;
    }

    public function getCreationDate()
    {
        return $this->creation_date;
    }
}
