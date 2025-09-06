<?php

    class EntityProductCategories{

        private int $id;
        private string $name;
        private ?string $img;
        private ?int $promo;
        private ?int $billsPromo;
        private string $created;
        private string $modified;
        private int $sort_order;
        private int $active;
        private int $deleted;

        public function getName()
        {
                return $this->name;
        }

        public function getSortOrder()
        {
                return $this->sort_order;
        }

        public function getImg()
        {
                return $this->img;
        }

        public function getBillsPromo()
        {
                return $this->billsPromo;
        }

        public function getPromo()
        {
                return $this->promo;
        }

        public function getActive()
        {
                return $this->active;
        }

        public function getId()
        {
                return $this->id;
        }
    }
