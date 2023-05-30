<?php

namespace App\Http\Traits;

use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Hash;

trait HelperTrait
{
    public $projectName = 'Library Management System.';
    protected function invoiceGenerator($model)
    {
        $lastorderId = $model::orderBy('id', 'desc')->first()->id;

        // Get last 3 digits of last order id
        $lastIncreament = substr($lastorderId, -3);

        // Make a new order id with appending last increment + 1
        $newOrderId = 'BB' . date('Ymd') . str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);

        return $newOrderId;
    }



}
