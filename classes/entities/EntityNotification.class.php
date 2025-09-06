<?php

    class EntityNotification
    {
        private int $id;
        private ?string $icon;
        private ?string $message;
        private ?string $date;
        private ?string $lien;
        private ?bool $view;

        public function getId()
        {
            return $this->id;
        }

        public function getIdNotif()
        {
            return $this->id;
        }

        public function getIcon()
        {            
            return $this->icon;
        }

        public function getMessage()
        {
            return $this->message;
        }

        public function getDate()
        {
            return UTILS::date($this->date) ;
        }

        public function getLien()
        {            
            return $this->lien;
        }

        public function getView()
        {
            return $this->view;
        }
    }
