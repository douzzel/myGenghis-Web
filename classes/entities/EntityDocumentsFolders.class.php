<?php

    class EntityDocumentsFolders
    {
        private int $id;
        private string $name;
        private ?int $section_id;
        private ?int $parent;
        private ?int $owner;

        public function getId()
        {
            return $this->id;
        }

        public function getName()
        {
            return html_entity_decode($this->name);
        }

        public function getParent()
        {
            return $this->parent;
        }

        public function getSectionId()
        {
                return $this->section_id;
        }

        public function getOwner() {
            return $this->owner;
        }
    }
