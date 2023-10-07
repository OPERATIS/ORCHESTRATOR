Metrics
@foreach($metricsActualData as $metricKey => $metricActualData)
    {{$metricKey}}
    {{$metricActualData['last']}}
    {{$metricActualData['previous']}}
    {{$metricActualData['percent']}}
    <br>
@endforeach
