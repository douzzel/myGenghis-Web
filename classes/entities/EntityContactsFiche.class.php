<?php

class EntityContactsFiche
{
    private int $id;
    private ?int $contacts_id;
    private ?int $tier_id;
    private ?int $accounts_id;
    private ?string $discussion_link;
    private ?string $deck_link;
    private ?string $drive_link;
    private ?string $website;
    private ?string $monitor_id;
    private ?string $note;
    private ?string $reference = '';
    private ?string $picture;
    private ?string $status;

    public function __toString() {
        return "{$this->id}";
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDiscussionLink()
    {
        return $this->discussion_link;
    }

    public function getDeckLink()
    {
        return $this->deck_link;
    }

    public function getDriveLink()
    {
        return $this->drive_link;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function getMonitorId()
    {
        return $this->monitor_id;
    }

    public function getNote()
    {
        return html_entity_decode($this->note);
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getAkauntingId()
    {
        if (isset($this->reference)) {
            $filter = ['reference' => $this->reference];
            $contact = Generique::selectOne('0so_contacts', 'graphene_akaunting', $filter);
            return $contact ? $contact->getId() : false;
        } else {
            return false;
        }
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
