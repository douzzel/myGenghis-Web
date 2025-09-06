<?php

    class EntityProductColumnsSettings {
        private int $id;
        private ?string $name;
        private ?string $type;
        private ?int $sort_order;
        private int $hidden_store;
        private string $created_at;
        private ?string $last_modified;

        public function getId()
        {
                return $this->id;
        }

        public function getName()
        {
                return $this->name;
        }

        public function getType()
        {
                return $this->type;
        }

        public function getFrenchType()
        {
                switch ($this->type) {
                        case 'text':
                                return 'Texte';
                        case 'date':
                                return 'Date';
                        case 'int':
                                return 'Number';
                        case 'select':
                                return 'Select';
                }
        }

        public function getSortOrder()
        {
                return $this->sort_order;
        }

        public function getHiddenStore()
        {
                return $this->hidden_store;
        }
    }
?>
