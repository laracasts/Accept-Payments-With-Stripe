<?php

namespace App\Billing;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	/**
	 * Fields to guard.
	 * 
	 * @var array
	 */
    protected $guarded = [];

    /**
     * Fetch the payment amount in dollars.
     * 
     * @return string
     */
    public function inDollars()
    {
    	return number_format($this->amount / 100, 2);
    }
}
