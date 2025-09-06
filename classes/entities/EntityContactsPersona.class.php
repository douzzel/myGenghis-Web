<?php

class EntityContactsPersona
{
    private int $id;
    private int $persona_id;
    private ?int $contacts_id;
    private ?int $tier_id;
    private ?int $accounts_id;

    public function __toString() {
        return "{$this->persona_id}";
    }

    public function getPersona_id()
    {
        return $this->persona_id;
    }

    public function getName() {
        return MYSQL::selectOneValue("SELECT name FROM persona WHERE id = '{$this->persona_id}'");
    }
}
