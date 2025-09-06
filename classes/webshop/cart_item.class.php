<?php
    // 'cart item' object
    class CartItem{
    
        // database connection and table name
        private $conn;
        private $table_name = "cart_items";
    
        // object properties
        public $id;
        public $product_id;
        public $quantity;
        public $user_id;
        public $created;
        public $modified;
    
        // constructor
        public function __construct($db){
            $this->conn = $db;
        }
    }
?>