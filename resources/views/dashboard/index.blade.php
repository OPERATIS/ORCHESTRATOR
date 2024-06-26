@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center text-sm text-gray_1 border-b border-black border-opacity-10">
            <div class="max-w-[84rem] w-full px-7 mx-auto">
                Dashboard
            </div>
        </div>
        <div class="max-w-[84rem] mx-auto">
            <div class="px-7 py-8">
                <div class="relative flex items-center w-full items-center gap-3">
                    @if($metricsActualData && count($actualIntegrations))
                        @foreach($metricsActualData as $metricKey => $metricActualData)
                            <div class="h-[7rem] w-full text-black p-6 rounded-2xl" style="@if($metricActualData['percent'] > 0) background: #E3FFE4; @else background: #F6E5E5; @endif">
                                <div class="text-sm font-semibold uppercase mb-2">
                                    {{$metricKey}}
                                </div>
                                <div class="w-full flex items-center">
                                    <div class="text-2xl font-semibold mr-2" style="line-height: 36px;">
                                        {{\App\Helpers\Shorts::formatNumber($metricActualData['last'], null, 1)}}
                                        {{--                                    {{$metricActualData['previous']}}--}}
                                    </div>
                                    <div class="flex items-center text-xs ml-auto" style="line-height: 18px;">
                                        <span>{{$metricActualData['percent']}}%</span>
                                        @if($metricActualData['percent'] > 0)
                                            <x-icon name="arrow-rise-icon" class="w-4 h-4 ml-1"/>
                                        @else
                                            <x-icon name="arrow-rise-down-icon" class="w-4 h-4 ml-1"/>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @for($i = 0; $i < 5; $i++)
                            <div class="h-[7rem] w-full text-black p-6 rounded-2xl bg-primary_light">
                            </div>
                        @endfor
                        <div class="w-full absolute flex flex-col items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-[0.625rem]" style="background: rgba(229, 236, 244, 0.75);">
                                <x-icon name="circle-wavy-warning" class="w-4 h-4"/>
                            </div>
                            <div class="text-sm text-black text-center mt-3" style="max-width: 500px;">
                                @if(@$warningWhenShopifyIntegratedLess1Hour)
                                    We need 1 hour after connecting your data to populate metrics, you will see 0s if there will be no orders in curren hour
                                @elseif(@$warningWhenShopifyIntegrationNotFound || !count(@$actualIntegrations))
                                    Please connect data for your Shopify store (optionally for other data sources) in the Integrations tab
                                @else
                                    You have no metrics data yet
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="flex justify-center items-center w-full min-h-[18rem] mt-8 bg-primary_light rounded-2xl pt-0 pb-4 px-4">
                    <dashboard-chart-block :integrations-count="{{count($actualIntegrations)}}"></dashboard-chart-block>
                </div>
                <div class="mt-8">
                    <div class="flex items-center text-black text-2xl font-semibold">
                        <x-icon name="lightbulb-icon" class="w-8 h-8 text-black mr-1"/>
                        AI Assistance Hints
                    </div>
                    <div class="grid grid-cols-2 gap-7 mt-5">
                        <div class="relative rounded-2xl py-7 px-5 pb-5 col-span-1" style="background: #FBFAF7;">
                            <div class="absolute left-0 top-0 h-full w-full z-1 rounded-2xl" style="opacity: 0.12;">
                                <img src="/img/assistance_bg.png" alt="bg assistance">
                            </div>
                            <div class="relative z-10">
                                <div class="text-2xl text-black font-semibold">
                                    Recommendations
                                </div>
                                <div class="w-full bg-black h-px bg-opacity-20 mb-6 mt-5"></div>
                                @if (@$lastUpdateRecommendations)
                                    @if ($lastUpdateRecommendations)
                                        <div class="text-sm text-black text-opacity-40">
                                            Last Update {{$lastUpdateRecommendations->format('d.m.y H:i')}}
                                        </div>
                                    @endif
                                    <ul class="flex flex-col text-sm text-dark mt-4 list-disc" style="margin-left: 18px;">
                                        @foreach ($recommendations as $alertId => $list)
                                            @foreach ($list as $item)
                                                <li class="mb-2.5">
                                                    @if ($loop->index === 0)
                                                        {{$item}}
                                                        <a class="text-green_2 font-bold underline"
                                                           href="{{route('chatsCreate', ['alert' => $alertId])}}">
                                                            Learn more
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="flex flex-col items-center mt-12">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-[0.625rem]" style="background: rgba(229, 236, 244, 0.75);">
                                            <x-icon name="circle-wavy-warning" class="w-6 h-6"/>
                                        </div>
                                        <div class="text-sm text-black text-center mt-4" style="max-width: 400px;">
                                            @if(count($actualIntegrations))
                                                @if(@$warningWhenShopifyIntegratedLess24Hours)
                                                    We need 24 hours to obtain enough data for relevant recommendations
                                                @else
                                                    You have no recommendations yet
                                                @endif
                                            @else
                                                Please connect data for your Shopify store (optionally for other data sources) in the Integrations tab
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="relative rounded-2xl py-7 px-5 pb-10 col-span-1" style="background: #F8FBF7;">
                            <div class="absolute left-0 top-0 h-full w-full z-1 rounded-2xl" style="opacity: 0.12;">
                                <img src="/img/assistance_bg.png" alt="bg assistance">
                            </div>
                            <div class="relative z-10">
                                <div class="text-2xl text-black font-semibold">
                                    Revenue Attribution Factors
                                </div>
                                <div class="w-full bg-black h-px bg-opacity-20 mb-6 mt-5"></div>
                                @if(@$lastUpdateRevenueAttributionFactors || @$revenueAttributionFactors['positive'] || @$revenueAttributionFactors['negative'])
                                    @if ($lastUpdateRevenueAttributionFactors)
                                        <div class="text-sm text-black text-opacity-40">
                                            Last Update {{$lastUpdateRevenueAttributionFactors->format('d.m.y H:i')}}
                                        </div>
                                    @endif
                                    <div class="w-full flex items-start mt-6 space-x-6">
                                        @if ($revenueAttributionFactors['positive'] ?? false)
                                            <div class="w-full border-2 border-dark bg-primary_light p-6" style="border-radius: 8px;">
                                                <div class="flex items-center text-sm text-black font-semibold mb-1">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-green_2 mr-2"></span>
                                                    Top positive factors
                                                </div>
                                                @foreach ($revenueAttributionFactors['positive'] as $item)
                                                    <div class="text-sm text-black text-opacity-40" style="margin-bottom: 1px;">
                                                        {{$item}}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if ($revenueAttributionFactors['negative'] ?? false)
                                            <div class="w-full border-2 border-dark bg-primary_light p-6" style="border-radius: 8px;">
                                                <div class="flex items-center text-sm text-black font-semibold mb-1">
                                                    <span class="w-2.5 h-2.5 rounded-full bg-red mr-2"></span>
                                                    Top negative factors
                                                </div>
                                                @foreach ($revenueAttributionFactors['negative'] as $item)
                                                    <div class="text-sm text-black text-opacity-40" style="margin-bottom: 1px;">
                                                        {{$item}}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col items-center mt-12">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-[0.625rem]" style="background: rgba(229, 236, 244, 0.75);">
                                            <x-icon name="circle-wavy-warning" class="w-6 h-6"/>
                                        </div>
                                        <div class="text-sm text-black text-center mt-4" style="max-width: 400px;">
                                            @if(count($actualIntegrations))
                                                @if(@$warningWhenShopifyIntegratedLess1Hour)
                                                    We need 1 hour after connecting your data to populate attributions
                                                @else
                                                    You have no attributions factors yet
                                                @endif
                                            @else
                                                Please connect data for your Shopify store (optionally for other data sources) in the Integrations tab
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
