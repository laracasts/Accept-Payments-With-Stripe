<?php

namespace App\Http\Controllers;

use App\User;

class WebhooksController extends Controller
{
    /**
     * Handle the incoming Stripe webhook.
     *
     * @return \Response
     */
    public function handle()
    {
        $payload = request()->all();

        $method = $this->eventToMethod($payload['type']);

        if (method_exists($this, $method)) {
            $this->$method($payload);
        }

        return response('Webhook Received');
    }

    /**
     * Handle when a successful charge has gone through on Stripe's end.
     * 
     * @param  object $payload
     * @return void
     */
    public function whenChargeSucceeded($payload)
    {
        $this->retrieveUser($payload)
             ->payments()
             ->create([
                'amount' => $payload['data']['object']['amount'],
                'charge_id' => $payload['data']['object']['id']
            ]);
    }

    /**
     * Handle when a customer's subscription has been deleted.
     *
     * @param array $payload
     */
    public function whenCustomerSubscriptionDeleted($payload)
    {
        $this->retrieveUser($payload)->deactivate();
    }

    /**
     * Convert a Stripe event name to a method name.
     *
     * @param  string $event
     * @return string
     */
    protected function eventToMethod($event)
    {
        return 'when' . studly_case(str_replace('.', '_', $event));
    }

    /**
     * Fetch a user by their Stripe id.
     * 
     * @param  object $payload 
     * @return User         
     */
    protected function retrieveUser($payload)
    {
        return User::byStripeId(
            $payload['data']['object']['customer']
        );
    }
}
