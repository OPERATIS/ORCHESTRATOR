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
                                @if (@$shopifyFirstConnectedAt ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$shopifyFirstConnectedAt->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5">
                            @if (@$shopifyFirstConnectedAt ?? null)
                                <button class="w-full btn md btn_connect"
                                        @click="openModal('modal_shopify')"
                                >
                                    Configure
                                </button>
                            @else
                                <button class="w-full btn md btn_connect"
                                        @click="openModal('modal_shopify_url')"
                                >
                                    Connect
                                </button>
                            @endif
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
                                @if ($googleAnalyticsFirstConnectedAt ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$googleAnalyticsFirstConnectedAt->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5">
                            @if($googleAnalyticsFirstConnectedAt)
                                <button class="w-full btn md btn_connect"
                                        @click="openModal('modal_google')"
                                >
                                    Configure
                                </button>
                            @else
                                <a href="{{route('integrationsGoogleLogin', ['service' => 'analytics'])}}"
                                   class="w-full btn md btn_connect"
                                >
                                    Connect
                                </a>
                            @endif
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
                                @if ($googleAdwordsFirstConnectedAt ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$googleAdwordsFirstConnectedAt->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5">
                            @if($googleAdwordsFirstConnectedAt)
                                <button class="w-full btn md btn_connect"
                                        @click="openModal('modal_google')"
                                >
                                    Configure
                                </button>
                            @else
                                <a href="{{route('integrationsGoogleLogin', ['service' => 'adwords'])}}"
                                   class="w-full btn md btn_connect"
                                >
                                    Connect
                                </a>
                            @endif
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
                                @if ($facebookFirstConnectedAt ?? null)
                                    <div class="ml-auto">
                                        <div class="w-max flex text-sm text-black font-semibold px-4 py-2.5 bg-primary_green rounded-[0.625rem] ml-auto"
                                             style="line-height: 14px;"
                                        >
                                            Connected
                                        </div>
                                        <div class="text-xs text-default text-opacity-50 pt-1.5">
                                            {{$facebookFirstConnectedAt->format('Y-m-d, H:i')}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5">
                            @if($facebookFirstConnectedAt)
                                <button class="w-full btn md btn_connect"
                                        @click="openModal('modal_facebook')"
                                >
                                    Configure
                                </button>
                            @else
                                <a href="{{route('integrationsFacebookLogin')}}"
                                   class="w-full btn md btn_connect"
                                >
                                    Connect
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(@$shopifyFirstConnectedAt)
        <modal-component ref="modal_shopify" :max-width="'460px'">
            <div class="modal-slot">
                <shopify-modal :accounts="{{@$shopifyAccounts}}"></shopify-modal>
            </div>
        </modal-component>
    @else
        <modal-component ref="modal_shopify_url" :max-width="'635px'">
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
                        <form id="shopifyForm" @submit.prevent="submitShopifyForm" method='POST' action="{{route('integrationsShopifyLogin')}}">
                            {{ csrf_field() }}
                            <div class="input-block">
                                <label for="shopify_url" class="label">MyShopify URL</label>
                                <input id="shopify_url"
                                       placeholder="Link"
                                       class="input !pr-40"
                                       type="text"
                                       name="shop"
                                       v-model="shopifyUrl"
                                >
                                <div v-if="showShopifyErrorMessage" class="error">URL should end with '.myshopify.com'</div>
                                <button type="submit" class="absolute w-max top-1.5 right-1.5 w-full btn md btn_connect2">
                                    Connect
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </modal-component>
    @endif

    <modal-component ref="modal_google" :max-width="'460px'">
        <div class="modal-slot">
            <google-modal :accounts="{{@$googleAccounts}}"></google-modal>
        </div>
    </modal-component>

    <modal-component ref="modal_facebook" :max-width="'460px'">
        <div class="modal-slot">
            <facebook-modal :accounts="{{@$facebookAccounts}}"></facebook-modal>
        </div>
    </modal-component>

@endsection

