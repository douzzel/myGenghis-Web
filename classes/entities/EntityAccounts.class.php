<?php

class EntityAccounts
{
    private int $id_client;
    private ?string $Nom;
    private ?string $Prenom;
    private ?string $civilite;
    private string $Email = "";
    private string $Pseudo = "";
    private string $Password;
    private string $Date_Inscription = "";
    private ?string $DateNaissance;
    private ?string $Adresse;
    private ?string $address_complement;
    private ?string $CodePostal;
    private ?string $Ville;
    private ?string $Pays;
    private int $Newsletter = 1;
    private int $clickCollect= 0;
    private int $FM_ReadOnly = 1;
    private int $OpenCode = 0;
    private int $RestorePassword = 0;
    private int $isBanni = 0;
    private ?string $signature;
    private ?string $about;
    private ?string $category;
    private ?string $tier_id = null;

    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getPseudo()
    {
        return $this->Pseudo;
    }

    public function getDate_Inscription()
    {
        return $this->Date_Inscription;
    }

    public function getDate_InscriptionText()
    {
        return UTILS::date($this->Date_Inscription, "j F Y");
    }

    public function getAdresse()
    {
        return $this->Adresse;
    }

    public function getFullAdresse() {
        return "{$this->Adresse} {$this->CodePostal} {$this->Ville} {$this->Pays}";
    }

    public function getNom()
    {
        return $this->Nom;
    }

    public function getPrenom()
    {
        return $this->Prenom;
    }

    public function getCompleteName()
    {
        return $this->Nom && $this->Prenom ? "{$this->Nom} {$this->Prenom}" : ($this->Nom ? $this->Nom : $this->Prenom);
    }

    public function getEmail()
    {
        return $this->Email;
    }

    public function getDateNaissance()
    {
        return $this->DateNaissance;
    }

    public function getPays()
    {
        return $this->Pays;
    }

    public function getCodePostal()
    {
        return $this->CodePostal;
    }

    public function getVille()
    {
        return $this->Ville;
    }

    public function getAvatar() {
        return UTILS::GetAvatar($this->Pseudo);
    }

    public function setFromContacts(EntityContacts $c) {
        $this->id_client = 0;
        $this->Nom = $c->getNom();
        $this->Prenom = $c->getPrenom();
        $this->Email = $c->getCouriel() ?? "";
        $this->Pays = $c->getPays();
        $this->Adresse = $c->getAdresse();
        $this->CodePostal = $c->getCp();
        $this->Ville = $c->getVille();
        $this->category = $c->getCategory();
        $this->civilite = $c->getCivilite();
        $this->address_complement = $c->getAddressComplement();
    }

    public function getNameOrPseudo() {
        if ($this->Prenom || $this->Nom) {
            return "{$this->Nom} {$this->Prenom}";
        }
        return $this->Pseudo;
    }

    public function getName() {
        return "{$this->Nom} {$this->Prenom}";
    }

    public function getCategory()
    {
        return $this->category ? html_entity_decode($this->category) : '';
    }

    public function getTierId()
    {
        return $this->tier_id;
    }

    public function getCivilite()
    {
        return $this->civilite;
    }

    public function getAddressComplement()
    {
        return $this->address_complement;
    }

    public function getFM_ReadOnly()
    {
        return $this->FM_ReadOnly;
    }

    public function getOpenCode()
    {
        return $this->OpenCode;
    }

    public function getClickCollect()
    {
        return $this->clickCollect;
    }
}
