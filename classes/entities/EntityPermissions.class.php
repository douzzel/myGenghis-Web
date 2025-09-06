<?php

    class EntityPermissions{
        private $id;
        private $group_id;
        private $user_id;
        private $right_id;
        private $gbsm_fixed_perm;
        private $module_id;
        private $date_created;

        public function getRightId(){
            return $this->right_id;
        }

        public function getUserId()
        {
                return $this->user_id;
        }

        public function getGroupId()
        {
                return $this->group_id;
        }
    }