<?php

class Item{
    public $product;
    public $quantity;

    public function __construct($product, $quantity = 1){
        $this->product = $product;
        $this->quantity = $quantity;
    }
}