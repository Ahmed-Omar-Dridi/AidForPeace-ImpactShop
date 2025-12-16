<?php
// controllers/HomeController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../config/DATABASE.PHP';

class HomeController {
    public function index() {
        $products = Product::findAll();
        // Limit to 6 products for homepage
        $products = array_slice($products, 0, 6);
        
        require __DIR__ . '/../views/home/home.php';
    }

    public function role() {
        require __DIR__ . '/../views/home/role_selection.php';
    }

    public function donation() {
        require __DIR__ . '/../views/home/DONATION.PHP';
    }
}