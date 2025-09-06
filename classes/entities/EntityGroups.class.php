<?php 
class EntityGroups{
    private $id;
    private $name_group;
    private $gbsm_fixed_group;
    private $date_created;

    public function getId(){
        return $this->id;
    }
    public function getNameGroup(){
        return $this->name_group;
    }
}