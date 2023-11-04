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
                                @if ($shopifyFirstConnectedAt ?? null)
                                    {{$shopifyFirstConnectedAt}}
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
                            @if ($googleAnalyticsFirstConnectedAt ?? null)
                                {{$googleAnalyticsFirstConnectedAt}}
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
                            @if ($googleAdwordsFirstConnectedAt ?? null)
                                {{$googleAdwordsFirstConnectedAt}}
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
                            @if ($facebookFirstConnectedAt ?? null)
                                {{$facebookFirstConnectedAt}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            accounts
                - id
                - delete
                - actual
                - profiles
                    - id
                    - actual
        </div>
    </div>
@endsection

