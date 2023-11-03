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
                    Data Source
                </div>
                <div class="mt-10 grid grid-cols-2 gap-6">
                    {{--Shopify--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/shopping.png" alt="shopify">
                            </div>
                            <div class="w-full flex items-center">
                                <div class="flex flex-col">
                                    <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                        Shopify
                                    </div>
                                    <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                        E-commerce platform
                                    </div>
                                </div>
                                @if ($shopify ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$shopify->created_at->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5">
                            <button type="submit" class="w-full btn md btn_connect"
                                    @click="openModal('modal_shopify')"
                            >
                                Connect
                            </button>
                            {{--                            <form method='POST' action="{{route('integrationsShopifyLogin')}}">--}}
                            {{--                                {{ csrf_field() }}--}}
                            {{--                                <label class="hidden">--}}
                            {{--                                    <input name="shop" value="test-orchestrator.myshopify.com" placeholder="test-orchestrator.myshopify.com">--}}
                            {{--                                </label>--}}
                            {{--                                <button type="submit" class="w-full btn md btn_connect">--}}
                            {{--                                    Connect--}}
                            {{--                                </button>--}}
                            {{--                            </form>--}}
                        </div>
                    </div>

                    {{--Google Analytics--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/google_analytics.png" alt="google analytics">
                            </div>
                            <div class="w-full flex items-center">
                                <div class="flex flex-col">
                                    <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                        Google Analytics
                                    </div>
                                    <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                        Analytics platform
                                    </div>
                                </div>
                                @if ($googleAnalytics ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$googleAnalytics->created_at->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{--                        <div class="mt-5">--}}
                        {{--                            <a href="{{route('integrationsGoogleLogin', ['service' => 'analytics'])}}"--}}
                        {{--                                class="w-full btn md btn_connect"--}}
                        {{--                            >--}}
                        {{--                                Connect--}}
                        {{--                            </a>--}}
                        {{--                        </div>--}}
                        <div class="mt-5">
                            <button class="w-full btn md btn_connect"
                                    @click="openModal('modal_google_analytics')"
                            >
                                Configure
                            </button>
                        </div>
                    </div>
                    {{--Google Ads--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/google_ads.png" alt="google ads">
                            </div>
                            <div class="w-full flex items-center">
                                <div class="flex flex-col">
                                    <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                        Google Ads
                                    </div>
                                    <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                        Online advertising platform
                                    </div>
                                </div>
                                @if ($googleAdwords ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$googleAdwords->created_at->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{route('integrationsGoogleLogin', ['service' => 'adwords'])}}"
                               class="w-full btn md btn_connect"
                            >
                                Connect
                            </a>
                        </div>
                    </div>
                    {{--Facebook Ads--}}
                    <div class="col-span-1 bg-primary_light px-6 pt-8 pb-10" style="border-radius: 10px;">
                        <div class="flex items-center space-x-4">
                            <div>
                                <img class="h-15 h-auto" src="/img/integrations/meta_ads.png" alt="meta ads">
                            </div>
                            <div class="w-full flex items-center">
                                <div class="flex flex-col">
                                    <div class="text-xl font-bold font-semibold text-black" style="line-height: 18px;">
                                        Meta Ads
                                    </div>
                                    <div class="text-sm text-black mt-2" style="line-height: 18px;">
                                        Online advertising platform
                                    </div>
                                </div>
                                @if ($facebook ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$facebook->created_at->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{route('integrationsFacebookLogin')}}"
                               class="w-full btn md btn_connect"
                            >
                                Connect
                            </a>
                        </div>
                    </div>
                </div>
            </div>

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

    <modal-component ref="modal_shopify" :max-width="'635px'">
        <div class="modal-slot">
            <div class="flex flex-col">
                <div class="text-black_5 text-2xl">Connect your Shopify Account</div>
                <div class="text-green_2 mt-3 font-semibold">Important</div>
                <div class="text-sm text-black_5">
                    Copy and paste the URL you see in your <a class="text-green_2 underline" href="https://admin.shopify.com/" target="_blank">Shopify Admin</a> under <br/>
                    "Online Store" › "Domains". <br/>
                    You can also find the name of your store directly in the shopify admin URL. <br/>
                    Extract "mystorename" from <span class="text-green_2 underline">https://admin.shopify.com/store / mystorename </span>.
                </div>
                <div class="mt-10">
                    <form method='POST' action="{{route('integrationsShopifyLogin')}}">
                        {{ csrf_field() }}
                        <div class="input-block">
                            <label for="shopify_url" class="label">MyShopify URL</label>
                            <input id="shopify_url"
                                   placeholder="Link"
                                   class="input !pr-40"
                                   type="text"
                                   name="shop"
                            >
                            <button type="submit" class="absolute w-max top-1.5 right-1.5 w-full btn md btn_connect2">
                                Connect
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </modal-component>

    <modal-component ref="modal_google_analytics" :max-width="'460px'">
        <div class="modal-slot">
            <div class="flex flex-col">
                <div class="text-black_5 text-2xl text-center">
                    Chose Accounts for <br/>
                    Google Analytics
                </div>
                <div class="flex flex-col mt-6">
                    <div class="flex items-center border border-black border-opacity-10 px-4" style="height: 50px; border-radius: 5px;">
                        <label class="custom-checkbox mr-5">
                            <input id="shopify1" type="checkbox" class="checkbox">
                            <span class="checkmark"></span>
                        </label>
                        <label for="shopify1" class="text-sm text-black cursor-pointer">UA - ovo.ua</label>
                    </div>
                </div>
                <button class="btn btn_default lg mt-10">
                    Save
                </button>
            </div>
        </div>
    </modal-component>

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

@endsection

