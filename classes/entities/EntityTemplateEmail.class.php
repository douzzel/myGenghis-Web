<?php

class EntityTemplateEmail {
    private int $id;
    private string $titre;
    private string $html;
    private ?string $category;
    private ?string $last_modified;

    public function getTitre()
    {
        return $this->titre;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getLastModified()
    {
        return $this->last_modified;
    }

    public function getLastModifiedDate()
    {
        return $this->last_modified ? UTILS::date($this->last_modified, 'd/m/Y') : '';
    }

    public function getCategory()
    {
        return $this->category;
    }
}
