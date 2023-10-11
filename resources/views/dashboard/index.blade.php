@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center px-7 text-sm text-gray_1 border-b border-black border-opacity-10">
            Dashboard
        </div>
        <div class="max-w-[76.75rem]">
            <div class="px-7 py-8">
                <div class="grid grid-cols-6 gap-3">
                    <div class="h-[7rem] w-full text-black p-6 col-span-1 rounded-2xl" style="background: #E3FFE4;">
                        <div class="text-sm font-semibold mb-2">
                            L
                        </div>
                        <div class="w-full flex items-center">
                            <div class="text-2xl font-semibold mr-2" style="line-height: 36px;">
                                721K
                            </div>
                            <div class="flex items-center text-xs ml-auto" style="line-height: 18px;">
                                <span>+11.01%</span>
                                <x-icon name="arrow-rise-icon" class="w-4 h-4 ml-1"/>
                            </div>
                        </div>
                    </div>
                    <div class="h-[7rem] w-full text-black p-6 col-span-1 rounded-2xl" style="background: #F6E5E5;">
                        <div class="text-sm font-semibold mb-2">
                            C
                        </div>
                        <div class="w-full flex items-center">
                            <div class="text-2xl font-semibold mr-2" style="line-height: 36px;">
                                367K
                            </div>
                            <div class="flex items-center text-xs ml-auto" style="line-height: 18px;">
                                <span>-11.01%</span>
                                <span>
                                <x-icon name="arrow-rise-down-icon" class="w-4 h-4 ml-1"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--            Metrics--}}
{{--            @foreach($metricsActualData as $metricKey => $metricActualData)--}}
{{--                {{$metricKey}}--}}
{{--                {{$metricActualData['last']}}--}}
{{--                {{$metricActualData['previous']}}--}}
{{--                {{$metricActualData['percent']}}--}}
{{--                <br>--}}
{{--            @endforeach--}}
        </div>
        <div>
            @if ($lastUpdateRecommendations)
                Last Update {{$lastUpdateRecommendations}}
            @endif
            <br>
            Recommendations
            <br>
            @if ($recommendationShort)
                Here is priority list for today:
                @if (count($recommendations) == 1)
                    <a href="{{route('chatsCreate', ['alert' => $lastAlertIdForRecommendation])}}">
                        1. Improve {{$recommendationShort}}
                    </a>
                @else
                    1. Improve {{$recommendationShort}}
                @endif
            @endif
            @if (count($recommendations) > 1)
                <a href="{{route('chatsCreate', ['alert' => $lastAlertIdForRecommendation])}}">
                    +{{count($recommendations) - 1}} others.
                ></a>
            @endif
        </div>
        <div>
            Revenue Attribution Factors
            <br>
            @if ($lastUpdateRevenueAttributionFactors)
                Last Update {{$lastUpdateRevenueAttributionFactors}}
            @endif
            <br>
            @if ($revenueAttributionFactors['positive'] ?? false)
                @foreach ($revenueAttributionFactors['positive'] as $item)
                    {{$item}}
                    <br>
                @endforeach
            @endif
            <br>
            @if ($revenueAttributionFactors['negative'] ?? false)
                @foreach ($revenueAttributionFactors['negative'] as $item)
                    {{$item}}
                    <br>
                @endforeach
            @endif
        </div>
    </div>
@endsection



