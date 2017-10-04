<?php


namespace App;


class ExcelData
{

    public $sku;
    public $price;
    public $manufacturer;
    public $currency_id = null;
    public $stock;

    public function __construct($sku, $price, $manufacturer, $currency, $stock)
    {
        $this->sku = $sku;
        $this->price = $price;
        $this->currency_id = $currency;
        $this->manufacturer = $manufacturer;
        $this->stock = $stock;
    }


}