<?php

    class EntityPersona
    {
        private int $id;
        private string $name;
        private ?string $job;
        private ?string $town;
        private ?string $situation;
        private ?int $age;
        private ?string $quote;
        private ?string $fear1;
        private ?string $fear2;
        private ?string $fear3;
        private ?string $expeciation1;
        private ?string $expeciation2;
        private ?string $expeciation3;
        private ?string $hobbie1;
        private ?string $hobbie2;
        private ?string $hobbie3;
        private ?string $bio;
        private ?string $technology1;
        private ?int $technology1lvl;
        private ?string $technology2;
        private ?int $technology2lvl;
        private ?string $technology3;
        private ?int $technology3lvl;
        private ?string $sexe;
        private ?string $image;
        private ?string $soncas1;
        private ?string $soncas2;
        private ?string $soncas3;
        private ?string $profil;
        private ?string $budget;
        private ?string $products;
        private ?string $search;
        private ?string $contacts;
        private ?string $other;

        public function getId()
        {
            return $this->id;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getImage()
        {
                return $this->image;
        }
    }
