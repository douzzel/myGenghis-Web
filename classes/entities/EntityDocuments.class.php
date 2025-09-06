<?php

    class EntityDocuments {
        private int $id;
        private ?string $ref;
        private ?string $title;
        private ?int $owner;
        private ?string $content;
        private ?string $image;
        private ?string $folder;
        private ?int $section_id;
        private ?string $weblink;
        private ?string $attachments;
        private string $created_at;
        private ?string $updated_at;

        public function __toString() {
                return $this->title ? html_entity_decode($this->title) : '';
        }

        public function getId()
        {
                return $this->id;
        }

        public function getRef()
        {
                return $this->ref;
        }

        public function getTitle()
        {
                return $this->title ? html_entity_decode($this->title) : '';
        }

        public function getOwner()
        {
                return $this->owner;
        }

        public function getOwnerName() {
                $filter = ['id_client' => $this->owner];
                $owner = Generique::selectOne('accounts', 'graphene_bsm', $filter);
                return $owner ? $owner->getNameOrPseudo() : '';
        }

        public function getContent()
        {
                return $this->content;
        }

        public function getImage()
        {
                return $this->image;
        }

        public function getFolder()
        {
                return $this->folder;
        }

        public function getWeblink()
        {
                return $this->weblink;
        }

        public function getAttachments()
        {
                return $this->attachments;
        }

        public function getSectionId()
        {
                return $this->section_id;
        }

        public function getCreatedAt()
        {
                return $this->created_at;
        }

        public function getUpdatedAt()
        {
                return $this->updated_at;
        }

        public function getCreatedAtText() {
                return UTILS::date($this->created_at);
        }

        public function getUpdatedAtText() {
                return UTILS::date($this->updated_at);
        }
    }
?>
