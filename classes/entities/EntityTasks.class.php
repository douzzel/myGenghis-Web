<?php
    class EntityTasks {
        private int $id;
        private string $title;
        private ?string $description;
        private ?string $users;
        private ?string $priority;
        private int $creator_id;
        private int $validated;
        private int $table_id;
        private string $date_created;
        private string $last_modified;
        private ?string $date_validated;
        private ?string $date_reminder;
        private ?string $date_deadline;
        private int $deleted;

        public function getId()
        {
                return $this->id;
        }

        public function getTitle()
        {
                return $this->title;
        }

        public function getDescription()
        {
                return $this->description ? html_entity_decode($this->description) : $this->description;
        }

        public function getCreatorId()
        {
                return $this->creator_id;
        }

        public function getValidated()
        {
                return $this->validated;
        }

        public function getTableId()
        {
                return $this->table_id;
        }

        public function getTableIcon() {
                $table = Generique::selectOne('tasks_tables', 'graphene_bsm', ['id' => $this->table_id]);
                return "<i class='material-icons align-middle p-0' title='{$table->getTitle()}'>{$table->getIcon()}</i>";
        }

        public function getDateReminder()
        {
                return $this->date_reminder;
        }

        public function getFormatDateReminder() {
                return $this->date_reminder ? UTILS::date($this->date_reminder) : '';
        }

        public function getDateDeadline()
        {
                return $this->date_deadline;
        }

        public function getFormatDateDeadline() {
                return $this->date_deadline ? UTILS::date($this->date_deadline) : '';
        }

        public function isReminder() {
                if (!$this->date_reminder || !$this->date_deadline) {
                        return false;
                }
                $now = new DateTime();
                $dateReminder = new DateTime($this->date_reminder);
                $dateDeadline = new DateTime($this->date_deadline);
                if ($dateReminder->getTimestamp() < $now->getTimestamp() || $dateDeadline->getTimestamp() < $now->getTimestamp()) {
                        return true;
                }
                return false;
        }

        public function isOutdated() {
                if (!$this->date_deadline) {
                        return false;
                }
                $now = new DateTime();
                $dateDeadline = new DateTime($this->date_deadline);
                return $dateDeadline->getTimestamp() < $now->getTimestamp();
        }

        public function getUsers()
        {
                if (!$this->users)
                        return false;
                $userList = explode(',', $this->users);
                $accountsList = [];
                foreach ($userList as $user) {
                        $accountsList[] = Generique::select('personnel', 'graphene_bsm', ['id_client' => $user]);
                }
                return $accountsList;
        }

        public function getAvatar($idClient) {
                if ($idClient) {
                        $clientName = MYSQL::selectOneValue("SELECT Pseudo FROM accounts WHERE id_client = '{$idClient}'");
                        return UTILS::GetAvatar($clientName);
                } else {
                        return '/themes/assets/images/avatars/no-avatar.png';
                }
        }

        public function getPriority()
        {
                return $this->priority;
        }
    }
