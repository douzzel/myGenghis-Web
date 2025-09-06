<?php

    class EntityNotificationUser
    {
        private int $id;
        private ?int $id_notification;
        private ?int $view;
        private ?int $user_id;

        public function getId()
        {
            return $this->id;
        }

        public function getIdNotification()
        {
            return $this->id_notification;
        }

        public function getView()
        {
            return $this->view;
        }

        public function getUserID()
        {
            return $this->user_id;
        }
    }
