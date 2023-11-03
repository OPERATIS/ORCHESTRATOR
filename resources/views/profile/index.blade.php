@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center text-sm text-gray_1 border-b border-black border-opacity-10">
            <div class="max-w-[84rem] w-full px-7 mx-auto">
                Profile settings
            </div>
        </div>
        <div>
            <profile-page :user-data="{{ json_encode($user) }}"></profile-page>
        </div>
        <div>
            @if ($subscription)
                Subscription #{{$subscription->id}}
                <br>
                @if (!empty($subscription->manual_status))
                    Waiting [LOCKED BUTTON]
                    <br>
                @else
                    @if ($subscription->getIsProblem()))
                        <a href="{{route('paymentCancelSubscriptionNow', $subscription->id)}}">Cancel subscription now</a>
                        <br>
                    @else
                        @if (empty($subscription->user_subscribe_to))
                            Next payment at {{$subscription->next_billing_period_at}}
                            <br>
                        @endif
                        @if (empty($subscription->user_subscribe_to))
                            <a href="{{route('paymentCancelSubscription', $subscription->id)}}">Cancel subscription</a>
                            <br>
                        @elseif (\Illuminate\Support\Carbon::parse($subscription->ends_at)->timestamp > time())
                            <a href="{{route('paymentResumeSubscription', $subscription->id)}}">Reopen subscription</a>
                            <br>
                        @else
                            <a href="{{route('paymentCreate')}}">Buy</a>
                            <br>
                        @endif
                    @endif
                @endif
            @else
                <a href="{{route('paymentCreate')}}">Buy</a>
            @endif

            @if ($transactions)
                @foreach ($transactions as $transaction)
                    <a href="{{$transaction['invoice_pdf']}}">PDF</a>
                    Created at {{$transaction['created']}}
                    Total {{$transaction['total']}}
                    <br>
                    @foreach ($transaction['items'] as $item)
                        {{$item['amount']}}
                        {{$item['description']}}
                        <br>
                    @endforeach
                    <br>
                @endforeach
            @endif
        </div>
    </div>
@endsection




