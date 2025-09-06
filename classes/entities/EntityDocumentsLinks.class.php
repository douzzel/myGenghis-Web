<?php

    class EntityDocumentsLinks
    {
        private int $id;
        private string $name;
        private ?string $icon;
        private string $href;
        private ?string $add_link;
        private ?int $section_id;
        private ?int $folder;
        private ?string $created_at;

        public function __toString() {
            return html_entity_decode($this->name);
    }

        public function getId()
        {
            return $this->id;
        }

        public function getName()
        {
            return html_entity_decode($this->name);
        }

        public function getFolder()
        {
            return $this->folder;
        }

        public function getSectionId()
        {
                return $this->section_id;
        }
        public function getIcon()
        {
                return isset($this->icon) && $this->icon ? $this->icon : 'link';
        }

        public function getHref()
        {
                return $this->href;
        }

        public function getAddLink()
        {
                return $this->add_link;
        }
    }
