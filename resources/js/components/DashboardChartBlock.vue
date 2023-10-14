<template>
    <div class="w-full">
        <highcharts id="dashboard-chart" :options="chartOptions"></highcharts>
    </div>
</template>

<script>


import { Chart } from 'highcharts-vue'

export default {
    components: {
        highcharts: Chart
    },
    data(){
        return {
            chart_data: null,
            chartOptions: {
                series: [{
                    data: null
                }]
            }
        }
    },
    methods: {
        generateRandomData() {
            const data = [];
            for (let i = 0; i < 7; i++) {
                data.push(Math.floor(Math.random() * 100));
            }
            return data;
        },
        getChartData(){
            const today = new Date();
            const sevenDaysAgo = new Date(today);
            sevenDaysAgo.setDate(today.getDate() - 7);

            const data = {
                start: sevenDaysAgo.toISOString().slice(0, 19),
                end: today.toISOString().slice(0, 19)
            };

            axios.get('/dashboard/metrics-chart', {
                params: data
            })
            .then(({data}) => {
                // console.log(data, data.current)
                // this.chartOptions = {
                //     title: {
                //         text: 'Графік метрик'
                //     },
                //     xAxis: {
                //         categories: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7']
                //     },
                //     series: [{
                //         name: 'Поточні',
                //         data: data.metrics.current
                //     }, {
                //         name: 'Попередні',
                //         data: data.metrics.previous
                //     }]
                // };
                // console.log(this.chartOptions);
                // this.chart_data = data.metrics;
                // console.log(this.chart_data);

                // Highcharts.charts[0].update(this.chartOptions, true);

            })
            .catch(({response}) => {
                console.log(response.data.message);
            });
        }
    },
    async mounted() {
        await this.getChartData();

        const current = this.generateRandomData();
        const previous = this.generateRandomData();

        this.chart_data = {
            current,
            previous
        };

        this.chartOptions = {
            chart: {
                zoomType: 'x',
                backgroundColor: 'transparent',
                style: {
                    fontFamily: 'Inter'
                },
            },
            title: {
                style: {
                    display: 'none'
                }
            },
            navigator: {
                enabled: false
            },
            legend: {
                enabled: false
            },
            scrollbar: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            colors: ['#000', '#A8C5DA'],
            tooltip: {
                backgroundColor: '#fff',
                borderWidth: 1,
                borderColor: '#000',
                shadow: false,
                style: {
                    color: '#000'
                },
                useHTML: true,
                formatter() {
                    let s = '<div>';

                    let point = this.point;

                    s += '<div class="mb-0.5"><span class="mr-1" style="color:' + point.color + '">●</span> ' + point.series.name + ':  <span class="font-bold ml-1">' + point.y.toLocaleString() + '</span></div>';
                    s+= '</div>'

                    return s;
                }
            },
            xAxis: {
                gapGridLineWidth: 0,
                gridLineColor: 'rgba(28, 28, 28, 0.20)',
                labels: {
                    style: {
                        color: 'rgba(28, 28, 28, 0.40)',
                        fontSize: '0.75rem'
                    },
                },
                lineColor: 'rgba(28, 28, 28, 0.20)',
                minorGridLineColor: 'rgba(28, 28, 28, 0.20)',
                tickColor: 'transparent',
                crosshair: {
                    width: 24,
                    color: 'rgba(180, 180, 180, 0.15)'
                },
                showEmpty: false,
                showFirstLabel: false,
                showLastLabel: false
            },
            yAxis: {
                labels: {
                    formatter: function (value, index) {
                        const lookup = [
                            { value: 1, symbol: "" },
                            { value: 1e3, symbol: "K" },
                            { value: 1e6, symbol: "M" },
                            { value: 1e9, symbol: "G" }
                        ];
                        const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
                        let item = lookup.slice().reverse().find(function(item) {
                            return value.value >= item.value;
                        });
                        return item ? (value.value / item.value).toFixed(1).replace(rx, "$1") + item.symbol : value.value.toFixed(0);
                    },
                    style: {
                        color: 'rgba(28, 28, 28, 0.40)'
                    },
                    y: -4,
                },
                gridLineColor: 'rgba(28, 28, 28, 0.05)',
                lineColor: 'rgba(28, 28, 28, 0.40)',
                minorGridLineColor: 'rgba(28, 28, 28, 0.05)',
                tickColor: 'rgba(28, 28, 28, 0.40)',
                title: {
                    enabled: false
                },
                showLastLabel: true,
                tickAmount: 5,
            },
            series: [{
                name: 'Current Week',
                data: current,
                lineWidth: 2
            }, {
                name: 'Previous Week',
                data: previous,
                lineWidth: 3
            }]
        };
    }
}
</script>
