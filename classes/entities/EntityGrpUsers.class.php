<?php

    class EntityGrpUsers{
        private $id;
        private $group_id;
        private $user_id;
        private $gbsm_fixed_perm;
        private $date_created;

        public function getGroup_id()
        {
                return $this->group_id;
        }

        public function getUserId()
        {
                return $this->user_id;
        }
    }

