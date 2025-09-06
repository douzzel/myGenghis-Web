<?php

    class EntityForme{
        private int $id;
        private string $name;
        private ?string $date_created;

        public function getId(){
            return $this->id;
        }
        public function getName(){
            return $this->name;
        }
    }
?>
