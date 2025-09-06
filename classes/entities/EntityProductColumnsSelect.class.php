<?php

    class EntityProductColumnsSelect {
        private int $id;
        private int $id_product_columns_settings;
        private string $value;

        public function getValue()
        {
                return $this->value;
        }

        public function getId()
        {
                return $this->id;
        }

        public function getId_product_columns_settings()
        {
                return $this->id_product_columns_settings;
        }
    }
