<?php

namespace App\Billing;

use App\Subscription;
use App\Billing\Payment;

trait Billable
{
    /**
     * Fetch a user by their Stripe id.
     * 
     * @param  string $stripeId 
     * @return User         
     */
    public static function byStripeId($stripeId)
    {
        return static::where('stripe_id', $stripeId)->firstOrFail();
    }

    /**
     * Activate the user's subscription.
     * 
     * @param  string $customerId     
     * @param  string $subscriptionId 
     * @return bool                
     */
    public function activate($customerId, $subscriptionId)
    {
        return $this->forceFill([
            'stripe_id' => $customerId,
            'stripe_active' => true,
            'stripe_subscription' => $subscriptionId,
            'subscription_end_at' => null
        ])->save();
    }

    /**
     * Deactivate the user's subscription.
     *
     * @param  mixed $endDate
     * @return bool
     */
    public function deactivate($endDate = null)
    {
        $endDate = $endDate ?: \Carbon\Carbon::now();

        return $this->forceFill([
            'stripe_active' => false,
            'subscription_end_at' => $endDate
        ])->save();
    }

    /**
     * Fetch a Subscription instance.
     * 
     * @return Subscription
     */
    public function subscription()
    {
        return new Subscription($this);
    }

    /**
     * Determine if the user is subscribed.
     * 
     * @return boolean
     */
    public function isSubscribed()
    {
        return !! $this->stripe_active;
    }

    /**
     * Fetch the user's payments.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
