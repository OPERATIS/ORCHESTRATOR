@extends('app')
@section('content')
    <div class="container">
        <form method='POST' action="{{route('shopifyLogin')}}">
            {{ csrf_field() }}
            <label>
                <input name="shop" value="quickstart-2bd07cf2.myshopify.com" placeholder="quickstart-2bd07cf2.myshopify.com">
            </label>
            <button type="submit">Shopify</button>
            @if ($shopify ?? null)
                {{$shopify->created_at}}
                @endif
        </form>
        <br>
        <br>
        <a class="button" href="{{route('facebookLogin')}}">Facebook Ads</a>
        @if ($facebook ?? null)
            {{$facebook->created_at}}
        @endif
        <br>
        <br>
        <a class="button" href="{{route('googleLogin', ['service' => 'analytics'])}}">Google Analytics</a>
        @if ($googleAnalytics ?? null)
            {{$googleAnalytics->created_at}}
        @endif
        <br>
        <br>
        <a class="button" href="{{route('googleLogin', ['service' => 'adwords'])}}">Google Ads</a>
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
        <a href="https://t.me/{{config('connects.telegram.botName')}}?start={{base64_encode($user->id)}}">Telegram</a>
        <br>
        <a href="https://wa.me/{{config('connects.whatsapp.displayPhoneNumber')}}?text={{urlencode('Start to notifications #' . $user->id)}}">WhatsApp</a>
        <br>
        <a href="https://m.me/{{config('connects.messenger.pageName')}}?text={{urlencode('Start to notifications #' . $user->id)}}">Messenger</a>
    </div>
@endsection
