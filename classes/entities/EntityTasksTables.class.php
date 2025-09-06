<?php
    class EntityTasksTables {
        private int $id;
        private string $title;
        private ?string $icon;
        private int $creator_id;
        private string $date_created;
        private string $last_modified;
        private int $deleted;

        public function getId()
        {
                return $this->id;
        }

        public function getTitle()
        {
                return $this->title;
        }

        public function getIcon()
        {
                return $this->icon;
        }

        public function getCreatorId()
        {
                return $this->creator_id;
        }
    }
