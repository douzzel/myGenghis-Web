<?php

class EntityPhone
{
    private int $id;
    private ?int $id_client;
    private ?int $contacts_id;
    private ?int $tier_id;
    private ?string $Home;
    private ?string $Cellular;
    private ?string $Job;

    public function __toString() {
        return "{$this->id}";
    }

    public function getHome()
    {
        return $this->Home;
    }

    public function getCellular()
    {
        return $this->Cellular;
    }

    public function getJob()
    {
        return $this->Job;
    }

    public function setFromContact($Home, $Cellular, $Job) {
        $this->Home = $Home;
        $this->Cellular = $Cellular;
        $this->Job = $Job;
    }

    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getContactsId()
    {
        return $this->contacts_id;
    }

    public function getTierId()
    {
        return $this->tier_id;
    }

    public function getExistingPhone() {
        return $this->Cellular ? $this->Cellular : ($this->Home ? $this->Home : ($this->Job ? $this->Job : ''));
    }
}
