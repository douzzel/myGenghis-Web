<?php

    class EntityDocumentsAccess
    {
        private int $id;
        private ?int $document_id;
        private ?int $folder_id;
        private ?int $file_id;
        private ?int $group_id;
        private ?int $user_id;
        private ?int $persona_id;

        public function getId()
        {
            return $this->id;
        }

        public function getDocument_id()
        {
            return $this->document_id;
        }

        public function getGroup_id()
        {
            return isset($this->group_id) ? $this->group_id : null;
        }

        public function getUser_id()
        {
                return isset($this->user_id) ? $this->user_id : null;
        }

        public function getPersona_id()
        {
                return isset($this->persona_id) ? $this->persona_id : null;
        }
    }
