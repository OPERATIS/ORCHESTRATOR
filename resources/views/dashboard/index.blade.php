@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center px-7 text-sm text-gray_1 border-b border-black border-opacity-10">
            Dashboard
        </div>
        <div class="max-w-[76.75rem]">
            <div class="px-7 py-8">
                <div class="flex items-center w-full items-center gap-3">
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
                </div>
                <div class="flex justify-center items-center w-full min-h-[18rem] mt-8 bg-primary_light rounded-2xl pt-0 pb-4 px-4">
                    <dashboard-chart-block></dashboard-chart-block>
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
                                @if ($lastUpdateRecommendations)
                                    <div class="text-sm text-black text-opacity-40">
                                        Last Update {{$lastUpdateRecommendations->format('d.m.y H:i')}}
                                    </div>
                                @endif
                                <div class="flex items-center text-sm text-dark mt-4">
                                    @if ($priorityRecommendation)
                                        {{$priorityRecommendation}}
                                        <a href="{{route('chatsCreate', ['alert' => $lastAlertIdForRecommendation])}}">
                                            Learn more.
                                        </a>
                                    @endif
                                </div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script>
    import DashboardChartBlock from "../../js/components/DashboardChartBlock";
    export default {
        components: {DashboardChartBlock}
    }
</script>
