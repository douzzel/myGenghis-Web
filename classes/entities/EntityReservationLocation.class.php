<?php

class EntityReservationLocation
{

        private int $id;
        private string $name;
        private ?string $address;
        private ?string $h_min;
        private ?string $h_max;
        private ?string $h_min2;
        private ?string $h_max2;
        private ?string $h_min3;
        private ?string $h_max3;
        private ?string $days;
        private ?string $duration;
        private ?string $number;
        private string $created;
        private string $updated;


        public function getId()
        {
                return $this->id;
        }

        public function getName()
        {
                return $this->name;
        }

        public function getAddress()
        {
                return $this->address;
        }

        public function getH_min()
        {
                return $this->h_min;
        }

        public function getH_max()
        {
                return $this->h_max;
        }

        public function getDuration()
        {
                return $this->duration;
        }

        public function getDays()
        {
                return $this->days;
        }

        public function getNumber()
        {
                return $this->number;
        }

        public function getH_min2()
        {
                return $this->h_min2;
        }

        public function getH_max2()
        {
                return $this->h_max2;
        }

        public function getH_max3()
        {
                return $this->h_max3;
        }

        public function getH_min3()
        {
                return $this->h_min3;
        }
}
