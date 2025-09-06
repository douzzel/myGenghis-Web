<?php

class EntityContactsExchange {
    private int $id;
	  private ?int $contacts_id;
	  private ?int $tier_id;
	  private ?int $accounts_id;
	  private ?string $contact_method;
	  private ?string $subject;
	  private ?string $message;
	  private ?string $action;
	  private ?int $FID;
	  private string $date; 

    public function __toString() {
        return "{$this->id}";
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContactMethod()
    {
        return $this->contact_method;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getFID()
    {
        return $this->FID;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getFormatDate()
    {
        if (UTILS::date(null, 'Y-m-d') == UTILS::date($this->date, 'Y-m-d')) {
            return UTILS::date($this->date, 'H:i');
        }
        if (UTILS::date(null, 'Y-m') == UTILS::date($this->date, 'Y-m')) {
            return UTILS::date($this->date, 'D d');
        }
        if (UTILS::date(null, 'Y') == UTILS::date($this->date, 'Y')) {
            return UTILS::date($this->date, 'D d F');
        }

        return UTILS::date($this->date, 'd/m/Y');
    }
}
