<?php

class EntityMailReceive extends EntityMail
{
    private ?string $name;
    private ?string $FOLDER;

    public function getType()
    {
        return 'receive';
    }

    public function getDest()
    {
        return iconv('utf-8', 'latin1', $this->name);
    }

    public function getDestLink()
    {
        return "<a href='/Administration/Contacts/{$this->from_email}' title='{$this->from_email}'>".iconv('utf-8', 'latin1', $this->name).'</a>';
    }
}
