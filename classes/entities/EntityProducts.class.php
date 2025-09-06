<?php

    class EntityProducts {
        private int $id;
        private ?string $reference;
        private ?string $ref_sell_accounting;
        private ?string $ref_buy_accounting;
        private int $type = 0;
        private string $name;
        private string $description;
        private ?string $informations;
        private float $price;
        private ?float $purchase_price = 0;
        private int $id_category_purchase_tva;
        private string $created;
        private string $modified;
        private int $stock;
        private ?string $stock_link;
        private int $deliveryOffer;
        private string $categorie;
        private string $p_category;
        private ?string $p_family;
        private ?string $promo;
        private ?string $billsPromo;
        private int $price_delivery;
        private int $id_category_tva;
        private int $mask = 0;
        private int $intmask = 0;
        private int $extmask = 0;
        private ?int $poids;
        private ?int $ranges;
        private ?int $steres;
        private ?int $quantite_minimum;
        private ?int $quantite_maximum;
        private ?int $quantite_par_palette;
        private ?int $longueur_buche;
        private int $deleted = 0;
        private ?int $subscription_interval_count;
        private ?string $subscription_interval;
        private ?string $textBefore;
        private ?string $textAfter;

        public function getId() {
            return $this->id;
        }

        public function getName() {
            return $this->name;
        }

        public function getDescription() {
            return $this->description;
        }

        public function getPrice() {
            return $this->price;
        }

        public function getPurchasePrice() {
            return $this->purchase_price;
        }

        public function getCreated() {
            return $this->created;
        }

        public function getStock() {
            return $this->stock;
        }

        public function getIdCategoryTva() {
            return $this->id_category_tva;
        }
    }
?>
