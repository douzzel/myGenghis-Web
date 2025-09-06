<?php

    class EntityDocumentsSections
    {
        private int $id;
        private string $name;
        private int $sort_order;

        public function getId()
        {
            return $this->id;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getSortOrder()
        {
            return $this->sort_order;
        }
    }
