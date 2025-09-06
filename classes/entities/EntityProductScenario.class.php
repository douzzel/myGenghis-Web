<?php

class EntityProductScenario
{
    private int $id;
    private ?string $product_name;
    private ?int $use_quantity;
    private ?int $product_quantity;
    private ?int $use_km;
    private ?int $km_min;
    private ?int $km_max;
    private ?string $scenario_message;
    private ?int $active;
    private string $last_modified;


    public function getId()
    {
        return $this->id;
    }

    public function getProductName()
    {
        return $this->product_name;
    }

    public function getUseQuantity()
    {
        return $this->use_quantity;
    }

    public function getProductQuantity()
    {
        return $this->product_quantity;
    }

    public function getUseKm()
    {
        return $this->use_km;
    }

    public function getKmMin()
    {
        return $this->km_min;
    }

    public function getKmMax()
    {
        return $this->km_max;
    }

    public function getScenarioMessage()
    {
        return $this->scenario_message;
    }

    public function getDecodeScenarioMessage()
    {
        return html_entity_decode($this->scenario_message);
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getActiveSate()
    {
        return $this->active ? "inactif" : "actif";
    }

    public function getAltActive()
    {
        return $this->active == 0 ? 1 : 0;
    }

    public function getAltState()
    {
        return $this->active == 0 ? "actif" : "inactif";
    }

    public function getLastModified()
    {
        return $this->last_modified;
    }
}
