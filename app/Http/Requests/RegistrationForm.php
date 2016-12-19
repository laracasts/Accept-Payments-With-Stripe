<?php

namespace App\Http\Requests;

use App\Plan;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !! $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'stripeEmail' => 'required|email',
            'stripeToken' => 'required',
            'plan'        => 'required'
        ];
    }

    /**
     * Persist the form.
     * 
     * @return void
     */
    public function save()
    {
        $plan = Plan::findOrFail($this->plan);

        $this->user()
             ->subscription()
             ->usingCoupon($this->coupon)
             ->create($plan, $this->stripeToken);
    }
}
