<?php

class EntityClickAndCollectSettings
{
    private int $id;
    private int $acccounts_ids;
    private ?string $message;
    private string $name;

    public function getMessage()
    {
        return $this->message;
    }

    public function getName()
    {
        return $this->name;
    }
}
