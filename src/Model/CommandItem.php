<?php

namespace App\Model;

class CommandItem{

    public function __construct(

        private int $productId,
        private string $name,
        private int $quantity,
        private float $price,
        private int $userId
    ){}

    public function getProductId(){ return $this->productId; }
    public function getName(){ return $this->name; }
    public function getQuantity(){ return $this->quantity; }
    public function getPrice(){ return $this->price; }
    public function getUserId(){ return $this->userId; }

    public function setProductId(int $productId){ $this->productId = $productId; }
    public function setName(string $name){ $this->name = $name; }
    public function setQuantity(int $quantity){ $this->quantity = $quantity; }
    public function setPrice(float $price){ $this->price = $price; }
    public function setUserId(int $userId){ $this->userId = $userId; }

    public function getTotal(){
        return $this->price * $this->quantity;
    }

    public function toArray(){
        return [
            'productId' => $this->productId,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->getTotal()
        ];
    }

    public function toJson(){
        return json_encode($this->toArray());
    }

}
