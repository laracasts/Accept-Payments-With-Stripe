<template>
    <form action="/subscriptions" method="POST">
        <input type="hidden" name="stripeToken" v-model="stripeToken">
        <input type="hidden" name="stripeEmail" v-model="stripeEmail">

        <div class="form-group">
            <select name="plan" v-model="plan">
                <option v-for="plan in plans" :value="plan.id">
                    {{ plan.name }} &mdash; ${{ plan.price / 100 }}
                </option>
            </select>
        </div>

        <div class="form-group">
            <input type="text" name="coupon" placeholder="Have a coupon code?" class="form-control" v-model="coupon">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary" @click.prevent="subscribe">Subscribe</button>
        </div>

        <p class="help is-danger" v-show="status" v-text="status"></p>
    </form>
</template>

<script>
    export default {
        props: ['plans'],

        data() {
            return {
                stripeEmail: '',
                stripeToken: '',
                plan: 1,
                status: false,
                coupon: ''
            };
        },

        created() {
            this.stripe = StripeCheckout.configure({
                key: Laracasts.stripeKey,
                image: "https://stripe.com/img/documentation/checkout/marketplace.png",
                locale: "auto",
                panelLabel: "Subscribe For",
                email: Laracasts.user.email,
                token: (token) => {
                    this.stripeToken = token.id;
                    this.stripeEmail = token.email;

                    this.$http.post('/subscriptions', this.$data)
                        .then(
                            response => alert('Complete! Thanks for your payment!'),
                            response => this.status = response.body.status
                        )
                }
            });
        },

        methods: {
            subscribe() {
                let plan = this.findPlanById(this.plan);

                this.stripe.open({
                    name: plan.name,
                    description: plan.name,
                    zipCode: true,
                    amount: plan.price,
                });
            },

            findPlanById(id) {
                return this.plans.find(plan => plan.id == id);
            }
        }
    }
</script>
