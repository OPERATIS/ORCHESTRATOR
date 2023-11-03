@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center text-sm text-gray_1 border-b border-black border-opacity-10">
            <div class="max-w-[84rem] w-full px-7 mx-auto">
                Integrations
            </div>
        </div>
        <div class="max-w-[84rem] mx-auto">
            <div class="p-9 px-7">
                <div class="ml-5 mt-1 text-2xl text-black font-semibold">
                    Marketing
                </div>
                <div class="mt-10 grid grid-cols-2 gap-6">
                    {{--Shopify--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/shopping.png" alt="shopify">
                            </div>
                            <div class="flex flex-col">
                                <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                    Shopify
                                </div>
                                <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                    E-commerce platform
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <form method='POST' action="{{route('integrationsShopifyLogin')}}">
                                {{ csrf_field() }}
                                <label class="hidden">
                                    <input name="shop" value="test-with-data-orchestrator.myshopify.com" placeholder="test-with-data-orchestrator.myshopify.com">
                                </label>
                                <button type="submit" class="w-full btn md btn_connect">
                                    Connect
                                </button>
{{--                                <button type="submit">--}}
{{--                                    Shopify--}}
{{--                                </button>--}}
                                @if ($shopify ?? null)
                                    {{$shopify->created_at}}
                                @endif
                            </form>
                        </div>
                    </div>
                    {{--Google Analytics--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/google_analytics.png" alt="google analytics">
                            </div>
                            <div class="flex flex-col">
                                <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                    Google Analytics
                                </div>
                                <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                    Analytics platform
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{route('integrationsGoogleLogin', ['service' => 'analytics'])}}"
                                class="w-full btn md btn_connect"
                            >
                                Connect
                            </a>
                            @if ($googleAnalytics ?? null)
                                {{$googleAnalytics->created_at}}
                            @endif
                        </div>
                    </div>
                    {{--Google Ads--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/google_ads.png" alt="google ads">
                            </div>
                            <div class="flex flex-col">
                                <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                    Google Ads
                                </div>
                                <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                    Online advertising platform
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{route('integrationsGoogleLogin', ['service' => 'adwords'])}}"
                               class="w-full btn md btn_connect"
                            >
                                Connect
                            </a>
                            @if ($googleAdwords ?? null)
                                {{$googleAdwords->created_at}}
                            @endif
                        </div>
                    </div>
                    {{--Facebook Ads--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/meta_ads.png" alt="meta ads">
                            </div>
                            <div class="flex flex-col">
                                <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                    Meta Ads
                                </div>
                                <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                    Online advertising platform
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{route('integrationsFacebookLogin')}}"
                               class="w-full btn md btn_connect"
                            >
                                Connect
                            </a>
                            @if ($facebook ?? null)
                                {{$facebook->created_at}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            Example: integrations/google
            {"status":true,"info":[]}
            {"status":true,"info":{"type":"ga_profiles","id":4,"name":"site.com","actual":null,"created_at":null}}

            Example: integrations/shopify
            {"status":true,"info":{"type":"integrations","id":6,"app_user_slug":"test-with-data-orchestrator.myshopify.com","created_at":"2023-10-15T10:05:07.000000Z"}}

            POST
            <form method="POST" action="{{route('integrationsUpdatePlatform', 'google')}}">
                {{ csrf_field() }}
                {{-- id=4 --}}
                <input value="ga_profiles" name="platform[4][type]">
                <input type="checkbox" checked name="platform[4][actual]">
                <button type="submit">SUBMIT</button>
            </form>

            <form method="POST" action="{{route('integrationsUpdatePlatform', 'shopify')}}">
                {{ csrf_field() }}
                {{-- id=6 --}}
                <input value="integrations" name="platform[6][type]">
                <input type="checkbox" checked name="platform[6][delete]">
                <button type="submit">SUBMIT</button>
            </form>

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
    </div>
@endsection

