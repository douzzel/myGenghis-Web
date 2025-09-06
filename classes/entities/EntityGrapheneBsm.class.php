<?php

    class EntityGrapheneBsm {
        private int $id;
        private ?int $id_client;
        private ?string $mail;
        private ?string $offer_gbsm;
        private ?string $start_gbsm;
        private ?string $expiration_gbsm;
        private ?string $expiration_server;
        private ?string $website;

        public function getExpiration_server()
        {
                return $this->expiration_server;
        }

        public function getExpiration_gbsm()
        {
                return $this->expiration_gbsm;
        }

        public function getStart_gbsm()
        {
                return $this->start_gbsm;
        }

        public function getGbsm_offer()
        {
                return $this->offer_gbsm;
        }

        public function getWebsite()
        {
                return $this->website;
        }
    }
?>
