<?php

    class EntityDocumentsFiles
    {
        private int $id;
        private string $name;
        private ?string $type;
        private ?int $owner;
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

        public function getType()
        {
                return $this->type;
        }

        public function getHref()
        {
            if ($this->section_id)
                return "/uploads/Drive/sect-{$this->section_id}/{$this->name}";
            else
                return "/uploads/Drive/{$this->folder}/{$this->name}";
        }

        public function getIcon()
        {
            if (strstr($this->type, 'image')) {
                return "image";
            } else if (strstr($this->type, 'audio')) {
                return "music_note";
            } else if (strstr($this->type, 'video')) {
                return "movie";
            } else if (strstr($this->type, 'pdf')) {
                return "picture_as_pdf";
            }
            return "insert_drive_file";
        }

        public function getOwner()
        {
                return $this->owner;
        }
    }
