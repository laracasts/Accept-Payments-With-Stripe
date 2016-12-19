<?php

namespace App;

use Carbon\Carbon;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;

class Subscription
{
    /**
     * The user associated with the subscription.
     * 
     * @var User
     */
    protected $user;

    /**
     * An optional coupon for the user's subscription.
     * 
     * @var string
     */
    protected $coupon;

    /**
     * Create a new Subscription instance.
     * 
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Set the coupon that should be applied to the subscription.
     * 
     * @param  string $coupon
     * @return $this
     */
    public function usingCoupon($coupon)
    {
        if ($coupon) {
            $this->coupon = $coupon;
        }

        return $this;
    }

    /**
     * Create a new subscription.
     * 
     * @param  Plan   $plan  
     * @param  string $token 
     * @return void      
     */
    public function create(Plan $plan, $token)
    {
        $customer = Customer::create([
            'email' => $this->user->email,
            'source' => $token,
            'plan'   => $plan->name,
            'coupon' => $this->coupon
        ]);

        $subscriptionId = $customer->subscriptions->data[0]->id;

        $this->user->activate($customer->id, $subscriptionId);
    }

    /**
     * Cancel the user's Stripe subscription.
     * 
     * @param  boolean $atPeriodEnd 
     * @return void          
     */
    public function cancel($atPeriodEnd = true)
    {
        $customer = Customer::retrieve($this->user->stripe_id); 

        $subscription = $customer->cancelSubscription(['at_period_end' => $atPeriodEnd]);

        $endDate = Carbon::createFromTimestamp($subscription->current_period_end);
        
        $this->user->deactivate($endDate);        
    }

    /**
     * Cancel the user's Stripe subscription immediately.
     * 
     * @return void
     */
    public function cancelImmediately()
    {
        return $this->cancel(false);
    }

    /**
     * Retrieve a user's Stripe-specific customer instance.
     * 
     * @return \Stripe\Customer
     */
    public function retrieveStripeCustomer()
    {
        return Customer::retrieve($this->user->stripe_id);
    }    

    /**
     * Retrieve a user's Stripe-specific subscription.
     * 
     * @return \Stripe\SubscriptionItem
     */
    public function retrieveStripeSubscription()
    {
        return StripeSubscription::retrieve($this->user->stripe_subscription);
    }
}
