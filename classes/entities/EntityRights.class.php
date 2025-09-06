<?php

    class EntityRights{

        private $id;
        private $label;
        private $date_created;

        public function getId(){
            return $this->id;
        }
        public function getLabel(){
            return $this->label;
        }
        public function setId($id){
            $this->id = $id;
        }
        public function setLabel($label){
            $this->label = $label;
        }
    }
