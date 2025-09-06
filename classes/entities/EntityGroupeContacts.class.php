<?php
class EntityGroupeContacts
{
    private $id;
    private $name;
    private $groupe_parent;
    private $level;
    private $date_created;

    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLevel()
    {
        return $this->level;
    }
    public function getGroupeParent()
    {
        return $this->groupe_parent;
    }
}