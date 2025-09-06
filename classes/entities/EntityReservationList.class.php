<?php

class EntityReservationList
{

    private int $id;
    private ?int $billing_id;
    private int $location_id;
    private string $res_date;
    private ?string $res_time;
    private string $created;
    private string $updated;
    private ?string $client_name;
    private ?string $email;
    private ?string $comments;


    public function getId()
    {

        return $this->id;
    }

    public function getBillingId()
    {
        return $this->billing_id;
    }

    public function getResDate()
    {
        return $this->res_date;
    }

    public function getLocationId()
    {
        return $this->location_id;
    }

    public function getLocationName()
    {
        $filter = ['id' => $this->location_id];
        $location = Generique::selectOne('reservation_location', 'graphene_bsm', $filter);
        return "{$location->getName()} - {$location->getAddress()}";
    }

    public function getNumeroFacture()
    {
        $filter = ['FID' => $this->billing_id];
        return Generique::selectOne('facture', 'graphene_bsm', $filter)->getNumeroFactureOrFID();
    }

    public function getResTime()
    {
        return $this->res_time;
    }

    public function getResDateText()
    {
        return UTILS::date($this->res_date, 'd/m/Y') . ' à ' . UTILS::date($this->res_time, 'H\hi');
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getClientName()
    {
        return $this->client_name;
    }

}
