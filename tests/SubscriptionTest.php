<?php

use App\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscriptionTest extends TestCase
{
    use DatabaseTransactions, InteractsWithStripe;

    /** @test */
    function it_subscribes_a_user()
    {
        $user = $this->makeSubscribedUser(['stripe_active' => false]);

        $this->assertTrue($user->isSubscribed());

        try {
            $user->subscription()->retrieveStripeSubscription();
        } catch (Exception $e) {
            $this->fail('Expected to see a Stripe subscription, but did not.');
        }
    }

    /** @test */
    function it_subscribes_a_user_using_a_coupon_code()
    {
        $user = factory('App\User')->create();

        $user->subscription()
             ->usingCoupon('TEST-COUPON')
             ->create($this->getPlan(), $this->getStripeToken());

        $customer = $user->subscription()->retrieveStripeCustomer();

        try {
            $couponThatWasAppliedToStripe = $customer->invoices()->data[0]->discount->coupon->id;

            $this->assertEquals('TEST-COUPON', $couponThatWasAppliedToStripe);
        } catch (Exception $e) {
            $this->fail('Expected a coupon to be applied to the Stripe customer, but did not find one.');
        }
    }

    /** @test */
    function it_cancels_a_users_subscription()
    {
        // Given we have a subscribed user.
        $user = $this->makeSubscribedUser();
        
        // When we cancel their subscription.
        $user->subscription()->cancel();
        
        // Then it should be canceled on Stripe's end.
        $stripeSubscription = $user->subscription()->retrieveStripeSubscription();

        $this->assertNotNull($stripeSubscription->canceled_at);
        
        $this->assertFalse($user->isSubscribed());
        $this->assertNotNull($user->subscription_end_at);
    }

    protected function makeSubscribedUser($overrides = [])
    {
        $user = factory('App\User')->create($overrides);

        $user->subscription()->create($this->getPlan(), $this->getStripeToken());

        return $user;
    }
}
