<template>
    <div class="w-full flex flex-col">
        <div class="flex items-center space-x-4 mt-5">
            <div class="relative cursor-pointer"
                 @click="toggleDropdown()"
                 v-click-outside-element="closeDropdown"
            >
                <div class="text-sm text-dark h-7 flex items-center">
                    <span class="pl-2 pr-3 uppercase">{{selectedMetric}}</span>
                    <span class="transition duration-200" :style="{ transform: isOpenDropdown ? 'rotate(0deg)' : 'rotate(180deg)' }">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                            <path d="M4.61114 14.2361C4.4822 14.3651 4.30734 14.4375 4.125 14.4375C3.94266 14.4375 3.7678 14.3651 3.63886 14.2361C3.50993 14.1072 3.4375 13.9323 3.4375 13.75C3.4375 13.5677 3.50993 13.3928 3.63886 13.2639L10.5139 6.38886C10.7823 6.12038 11.2177 6.12038 11.4861 6.38886L18.3611 13.2639C18.4901 13.3928 18.5625 13.5677 18.5625 13.75C18.5625 13.9323 18.4901 14.1072 18.3611 14.2361C18.2322 14.3651 18.0573 14.4375 17.875 14.4375C17.6927 14.4375 17.5178 14.3651 17.3889 14.2361L11 7.84727L4.61114 14.2361Z" fill="#1C1C1C"/>
                        </svg>
                    </span>
                </div>
                <ul
                    v-if="isOpenDropdown"
                    class="absolute mt-3 pt-2.5 px-2.5 pb-3 bg-white whitespace-nowrap min-w-[8rem] -translate-x-1/2"
                    style="box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.15); z-index: 100; border-radius: 10px;"
                >
                    <li class="text-xs text-dark text-opacity-50 pb-1.5" style="line-height: 18px;">
                        Select Metric
                    </li>
                    <li v-for="(metric,index) in metrics" :key="metric"
                        class="flex items-center text-sm text-dark uppercase pb-1.5"
                        :class="{ 'border-t border-dark border-opacity-10 py-1.5': index !== 0 }"
                        style="line-height: 18px;"
                        @click="selectMetric(metric)"
                    >
                        {{ metric }}
                    </li>
                </ul>
            </div>
            <span class="w-px h-5 bg-black bg-opacity-20"></span>
            <div class="flex items-center text-xs text-black px-1 cursor-pointer"
                 @click="toggleSeries('current')"
                 :class="{ 'text-opacity-50': !seriesVisibility.current }"
            >
                <span class="mr-1 text-black">
                    <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 8C11 9.65685 9.65685 11 8 11C6.34315 11 5 9.65685 5 8C5 6.34315 6.34315 5 8 5C9.65685 5 11 6.34315 11 8Z" fill="#1C1C1C"/>
                    </svg>
                </span>
                Current Week
            </div>
            <div class="flex items-center text-xs text-black px-1 cursor-pointer"
                 @click="toggleSeries('previous')"
                 :class="{ 'text-opacity-40': !seriesVisibility.previous }"
            >
                <span class="mr-1 text-secondary_blue">
                    <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 8C11 9.65685 9.65685 11 8 11C6.34315 11 5 9.65685 5 8C5 6.34315 6.34315 5 8 5C9.65685 5 11 6.34315 11 8Z" fill="#A8C5DA"/>
                    </svg>
                </span>
                Previous Week
            </div>
        </div>
        <div class="w-full">
            <highcharts ref="chart" id="dashboard-chart" :options="chartOptions"></highcharts>
        </div>
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
            isOpenDropdown: false,
            metrics:  ['l', 'c', 'p', 'q', 'ltv'],
            selectedMetric: 'l',
            chartOptions: {
                series: [{
                    data: null
                }]
            },
            seriesVisibility: {
                current: true,
                previous: true
            }
        }
    },
    methods: {
        toggleDropdown() {
            this.isOpenDropdown = !this.isOpenDropdown;
        },
        closeDropdown(){
            this.isOpenDropdown = false;
        },
        selectMetric(metric){
            this.isOpenDropdown = false;
            this.selectedMetric = metric;
            this.generateChart(metric);
        },
        generateChart(metric) {
            let data = this.chartData;
            this.chartOptions = {
                series: [
                    {
                        id: 'current',
                        name: 'Current Week',
                        data: data[metric].current,
                        lineWidth: 3,
                        visible: true
                    },
                    {
                        id: 'previous',
                        name: 'Previous Week',
                        data: data[metric].previous,
                        lineWidth: 3,
                        visible: true
                    }
                ]
            };
        },
        getChartOptions(){
            return {
                chart: {
                    zoomType: 'x',
                    backgroundColor: 'transparent',
                    style: {
                        fontFamily: 'Inter'
                    },
                    marginTop: 35
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
                        let point = this.point;
                        // let s = '<div class="text-xs"><span class="block border-b border-gray-dark border-opacity-50 pb-1 mb-1">' + new Date(this.point.x).toLocaleDateString('en-US', {  day: 'numeric', year: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' }) + '</span>';
                        let s = '<div class="text-xs"><span class="block border-b border-gray-dark border-opacity-50 pb-1 mb-1">' + new Date(point.x).toLocaleDateString('en-US', {  weekday: 'long' }) + '</span>';

                        s += '<div class="mb-0.5"><span class="mr-1" style="color:' + point.color + '">●</span> ' + point.series.name + ':  <span class="font-bold ml-1">' + point.y.toLocaleString() + '</span></div>';
                        s+= '</div>'

                        return s;
                    }
                },
                xAxis: {
                    type: 'datetime',
                    gapGridLineWidth: 0,
                    gridLineColor: 'rgba(28, 28, 28, 0.20)',
                    labels: {
                        style: {
                            color: 'rgba(28, 28, 28, 0.40)',
                            fontSize: '0.75rem'
                        },
                        formatter: function () {
                            const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            const currentDate = new Date(this.value);
                            const currentDay = daysOfWeek[currentDate.getDay()];

                            if (currentDay !== this.axis.customLastDay) {
                                this.axis.customLastDay = currentDay;
                                return currentDay;
                            }
                            return '';
                        }
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

                            // Визначення відповідного префіксу (K, M, G) на основі абсолютної величини числа
                            let absValue = Math.abs(value.value);
                            let item = lookup.slice().reverse().find(function(item) {
                                return absValue >= item.value;
                            });

                            // Встановлення мінімального кількості знаків після коми для малих значень
                            const decimalPlaces = absValue < 0.01 ? 4 : 1;

                            // Форматування числа і додавання мінуса, якщо воно від'ємне
                            let formattedValue = item ? (absValue / item.value).toFixed(decimalPlaces).replace(rx, "$1") + item.symbol : (absValue == 0 ? 0 : absValue.toFixed(decimalPlaces));
                            return value.value < 0 ? '-' + formattedValue : formattedValue;
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
                }
            }
        },
        toggleSeries(seriesId) {
            const currentVisibility = this.seriesVisibility[seriesId];
            this.seriesVisibility[seriesId] = !currentVisibility;

            const updatedSeries = this.chartOptions.series.map((series) => {
                if (series.id === seriesId) {
                    series.visible = !series.visible;
                }
                return series;
            });
            this.chartOptions.series = updatedSeries;
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
                this.chartData = data.metrics;
                this.generateChart(this.selectedMetric);
            })
            .catch(({response}) => {
                console.log(response);
            });
        }
    },
    mounted() {
        this.chartOptions = this.getChartOptions();
        this.getChartData();
    }
}
</script>
