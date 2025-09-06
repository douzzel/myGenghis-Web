<?php

class EntityCategoryTva
{
    private int $id;
    private string $name;
    private float $rate;
    private string $date_created;

    public function getId()
    {
        return $this->id; 
    }
    public function getName()
    {
        return $this->name;
    }
    public function getRate()
    {
        return $this->rate;
    }

    public function getDateCreated()
    {
        return $this->date_created;
    }

    public function toAkaunting() {
        return ['id' => $this->id, 'company_id' => 1, 'name' => $this->name, 'rate' => $this->rate, 'type' => 'normal', 'enabled' => 1, 'created_at' => $this->date_created];
    }
}

?>
