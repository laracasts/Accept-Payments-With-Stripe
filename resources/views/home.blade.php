@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @unless (auth()->user()->isSubscribed())
                    <div class="panel panel-default">
                        <div class="panel-heading">Create a Subscription</div>

                        <div class="panel-body">
                            <checkout-form :plans="{{ $plans }}"></checkout-form>
                        </div>
                    </div>
                @endunless

                @if (auth()->user()->isSubscribed())
                    <div class="panel panel-default">
                        <div class="panel-heading">Payments</div>

                        <div class="panel-body">
                            <ul class="list-group">
                                @foreach (auth()->user()->payments as $payment)
                                    <li class="list-group-item">{{ $payment->created_at->diffForHumans() }}: 
                                        <strong>${{ $payment->inDollars() }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">Cancel</div>

                        <div class="panel-body">
                            <form method="POST" action="/subscriptions">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button class="btn btn-danger">Cancel My Subscription</button>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
