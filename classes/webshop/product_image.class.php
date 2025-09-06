<?php
    // 'product image' object
    class ProductImage{
    
        // database connection and table name
        private $conn;
        private $table_name = "product_images";
    
        // object properties
        public $id;
        public $product_id;
        public $name;
        public $timestamp;
    
        // constructor
        public function __construct($db){
            $this->conn = $db;
        }
    }
?>