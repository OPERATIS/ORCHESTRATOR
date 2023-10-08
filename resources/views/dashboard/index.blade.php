@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center px-7 text-sm text-gray_1 border-b border-black border-opacity-10">
            Dashboard
        </div>
        <div>
{{--            Metrics--}}
{{--            @foreach($metricsActualData as $metricKey => $metricActualData)--}}
{{--                {{$metricKey}}--}}
{{--                {{$metricActualData['last']}}--}}
{{--                {{$metricActualData['previous']}}--}}
{{--                {{$metricActualData['percent']}}--}}
{{--                <br>--}}
{{--            @endforeach--}}
        </div>
    </div>
@endsection



