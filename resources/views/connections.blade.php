@extends('app')
@section('content')
    <div class="container">
        <form method='POST' action="{{route('integrationsShopifyLogin')}}">
            {{ csrf_field() }}
            <label>
                <input name="shop" value="test-orchestrator.myshopify.com" placeholder="test-orchestrator.myshopify.com">
            </label>
            <button type="submit">Shopify</button>
            @if ($shopify ?? null)
                {{$shopify->created_at}}
                @endif
        </form>
        <br>
        <br>
        <a class="button" href="{{route('integrationsFacebookLogin')}}">Facebook Ads</a>
        @if ($facebook ?? null)
            {{$facebook->created_at}}
        @endif
        <br>
        <br>
        <a class="button" href="{{route('integrationsGoogleLogin', ['service' => 'analytics'])}}">Google Analytics</a>
        @if ($googleAnalytics ?? null)
            {{$googleAnalytics->created_at}}
        @endif
        <br>
        <br>
        <a class="button" href="{{route('integrationsGoogleLogin', ['service' => 'adwords'])}}">Google Ads</a>
        @if ($googleAdwords ?? null)
            {{$googleAdwords->created_at}}
        @endif
        <br>
        <br>
        @if ($gaProfiles ?? null)
            @foreach ($gaProfiles as $googleProfile)
                {{$googleProfile->name}}
                <br>
                {{$googleProfile->timezone}}
                <br>
                {{$googleProfile->actual}}
                <br>
            @endforeach
        @endif
    </div>
    <div>
        <a href="https://t.me/{{config('integrations.telegram.botName')}}?start={{base64_encode($user->id)}}">Telegram</a>
        <br>
        <a href="https://wa.me/{{config('integrations.whatsapp.displayPhoneNumber')}}?text={{urlencode(\App\Services\Notifications::getMessageForInitSubscribe() . $user->id)}}">WhatsApp</a>
        <br>
        <a href="https://m.me/{{config('integrations.messenger.pageName')}}?text={{urlencode(\App\Services\Notifications::getMessageForInitSubscribe() . $user->id)}}">Messenger</a>
        <br>
        <a href="{{route('integrationsSlackLogin')}}">Slack</a>
    </div>
@endsection
