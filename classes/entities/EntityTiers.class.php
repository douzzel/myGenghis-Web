<?php

class EntityTiers{
    private int $id;
    private string $denomination;
    private ?string $number_siret;
    private string $adresse;
    private ?string $address_complement;
    private ?string $boite_postale;
    private string $code_postal;
    private string $ville;
    private ?string $numero_tva;
    private ?string $pays;

    // Type 0 = Personne Physique
    // Type 1 = Personne Morale
    private int $type;
    private ?int $representant_legal_id;
    private ?int $representant_legal_id_client;
    private ?string $date_created;
    private ?int $forme_id;

    private ?string $email;
    private ?string $category;

    private bool $deleted = false;

    public function __toString() {
        return "{$this->id}";
    }

    public function getId(){

        return $this->id;
    }
    public function getDenomination(){

        return $this->denomination;
    }
    public function  getNumberSiret(){

        return $this->number_siret;
    }
    public function getAdresse(){

        return $this->adresse;
    }
    public function getBoitePostale(){
        return $this->boite_postale;
    }
    public function getCodePostal(){

        return $this->code_postal;
    }
    public function getFullAdresse() {
        return "{$this->adresse} {$this->code_postal} {$this->ville} {$this->pays}";
    }
    public function getNumeroTva(){

        return $this->numero_tva;
    }
    public function getVille(){

        return $this->ville;
    }
    public function getPays(){

        return $this->pays;
    }
    public function getRepresentantLegalId() {
        return $this->representant_legal_id;
    }

    public function getRepresentantLegalIdClient() {
        return $this->representant_legal_id_client;
    }

    public function getRepresentantLegalName() {
        if ($this->representant_legal_id) {
            $rl = Generique::selectOne('contacts', 'graphene_bsm', ['id' => $this->representant_legal_id]);
            if ($rl)
                return $rl->getFullName();
        }
        if ((!isset($rl) || !$rl) && $this->representant_legal_id_client) {
            $rl = Generique::selectOne('accounts', 'graphene_bsm', ['id_client' => $this->representant_legal_id_client]);
            if ($rl)
                return $rl->getCompleteName();
        }
        return '';
    }

    public function getRepresentantLegalMail() {
        if ($this->representant_legal_id) {
            $rl = Generique::selectOne('contacts', 'graphene_bsm', ['id' => $this->representant_legal_id]);
            if ($rl)
                return $rl->getCouriel();
        }
        if ((!isset($rl) || !$rl) && $this->representant_legal_id_client) {
            $rl = Generique::selectOne('accounts', 'graphene_bsm', ['id_client' => $this->representant_legal_id_client]);
            if ($rl)
                return $rl->getEmail();
        }
        return '';
    }

    public function getType(){
        return $this->type;
    }
    public function getFormeId(){
        return $this->forme_id;
    }

    public function getFormeName() {
        $filter = ['id' => $this->forme_id];
        $forme = Generique::selectOne('forme', 'graphene_erp', $filter);
        if ($forme) {
            return $forme->getName();
        }
        return '';
    }

    public function getPre() {
        return $this->getType() == 0 ? $this->getFormeName() : '';
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCategory()
    {
        return $this->category ? html_entity_decode($this->category) : '';
    }

    public function getCompleteName() {
        $pre = $this->getPre();
        if ($pre) {
            return $pre . ' ' . $this->getDenomination();
        }
        return $this->getDenomination();
    }

    public function getAddressComplement()
    {
        return $this->address_complement;
    }

    public function getDateCreated()
    {
        return $this->date_created;
    }
}
