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
                        class="flex items-center text-sm text-dark uppercase pb-1.5 transition duration-200 hover:opacity-75"
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
            <template v-if="Object.keys(chartData).length || loading">
                <highcharts ref="chart" id="dashboard-chart" :options="chartOptions"></highcharts>
            </template>
            <template v-else>
                <div class="flex flex-col items-center justify-center" id="dashboard-chart">
                    <div class="flex items-center justify-center w-12 h-12 rounded-[0.625rem]" style="background: rgba(229, 236, 244, 0.75);">
                        <svg width="24" height="24" viewBox="0 0 61 60" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.8773 50.7841C21.8773 50.7841 21.0621 50.4427 18.5261 50.4081C18.5261 50.4081 16.3578 50.3784 15.2913 50.1834C15.2913 50.1834 13.1953 49.8001 11.9476 48.5524C11.9476 48.5524 10.6924 47.2972 10.3033 45.1654C10.3033 45.1654 10.1055 44.0816 10.0712 41.8811C10.0712 41.8811 10.0323 39.3835 9.71409 38.6185C9.71409 38.6185 9.38925 37.8375 7.61934 35.9858C7.61934 35.9858 6.09734 34.3934 5.47756 33.4978C5.47756 33.4978 4.25 31.724 4.25 30C4.25 30 4.25 28.2836 5.45782 26.5354C5.45782 26.5354 6.06833 25.6518 7.5733 24.0814C7.5733 24.0814 9.36744 22.2092 9.71586 21.3773C9.71586 21.3773 10.0573 20.5621 10.0919 18.0261C10.0919 18.0261 10.1216 15.8578 10.3166 14.7913C10.3166 14.7913 10.6999 12.6953 11.9476 11.4476C11.9476 11.4476 13.2028 10.1924 15.3346 9.80332C15.3346 9.80332 16.4184 9.60552 18.6189 9.57122C18.6189 9.57122 21.1165 9.53229 21.8815 9.21409C21.8815 9.21409 22.6625 8.88925 24.5142 7.11934C24.5142 7.11934 26.1066 5.59734 27.0022 4.97756C27.0022 4.97756 28.776 3.75 30.5 3.75C30.5 3.75 32.2164 3.75 33.9646 4.95782C33.9646 4.95782 34.8482 5.56833 36.4186 7.0733C36.4186 7.0733 38.2908 8.86744 39.1227 9.21586C39.1227 9.21586 39.9379 9.55726 42.4739 9.59193C42.4739 9.59193 44.6422 9.62157 45.7087 9.81662C45.7087 9.81662 47.8047 10.1999 49.0524 11.4476C49.0524 11.4476 50.3076 12.7028 50.6967 14.8346C50.6967 14.8346 50.8945 15.9185 50.9288 18.1189C50.9288 18.1189 50.9677 20.6165 51.2859 21.3815C51.2859 21.3815 51.6108 22.1625 53.3807 24.0142C53.3807 24.0142 54.9027 25.6066 55.5224 26.5022C55.5224 26.5022 56.75 28.276 56.75 30C56.75 30 56.75 31.7164 55.5422 33.4646C55.5422 33.4646 54.9317 34.3483 53.4267 35.9186C53.4267 35.9186 51.6326 37.7908 51.2841 38.6227C51.2841 38.6227 50.9427 39.4379 50.9081 41.9739C50.9081 41.9739 50.8784 44.1422 50.6834 45.2087C50.6834 45.2087 50.3001 47.3047 49.0524 48.5524C49.0524 48.5524 47.7972 49.8076 45.6654 50.1967C45.6654 50.1967 44.5815 50.3945 42.3811 50.4288C42.3811 50.4288 39.8835 50.4677 39.1185 50.7859C39.1185 50.7859 38.3375 51.1108 36.4858 52.8807C36.4858 52.8807 34.8934 54.4027 33.9978 55.0224C33.9978 55.0224 32.224 56.25 30.5 56.25C30.5 56.25 28.7836 56.25 27.0354 55.0422C27.0354 55.0422 26.1517 54.4317 24.5814 52.9267C24.5814 52.9267 22.7092 51.1326 21.8773 50.7841ZM23.3259 47.3252C23.3259 47.3252 24.8006 47.9429 27.176 50.2192C27.176 50.2192 29.5559 52.5 30.5 52.5C30.5 52.5 31.4567 52.5 33.8947 50.1698C33.8947 50.1698 36.2518 47.9168 37.6784 47.3235C37.6784 47.3235 39.1067 46.7294 42.3226 46.6792C42.3226 46.6792 45.6745 46.627 46.4007 45.9007C46.4007 45.9007 47.1138 45.1877 47.1584 41.9227C47.1584 41.9227 47.203 38.6598 47.8252 37.1741C47.8252 37.1741 48.4429 35.6994 50.7192 33.324C50.7192 33.324 53 30.9441 53 30C53 30 53 29.0433 50.6698 26.6053C50.6698 26.6053 48.4168 24.2482 47.8235 22.8216C47.8235 22.8216 47.2294 21.3933 47.1792 18.1774C47.1792 18.1774 47.127 14.8255 46.4007 14.0993C46.4007 14.0993 45.6877 13.3862 42.4227 13.3416C42.4227 13.3416 39.1598 13.297 37.6741 12.6748C37.6741 12.6748 36.1993 12.0571 33.824 9.78076C33.824 9.78076 31.4441 7.5 30.5 7.5C30.5 7.5 29.5433 7.5 27.1053 9.83021C27.1053 9.83021 24.7482 12.0832 23.3216 12.6765C23.3216 12.6765 21.8933 13.2706 18.6774 13.3208C18.6774 13.3208 15.3255 13.373 14.5993 14.0993C14.5993 14.0993 13.8862 14.8123 13.8416 18.0773C13.8416 18.0773 13.797 21.3402 13.1748 22.8259C13.1748 22.8259 12.5571 24.3007 10.2808 26.676C10.2808 26.676 8 29.0559 8 30C8 30 8 30.9567 10.3302 33.3947C10.3302 33.3947 12.5832 35.7518 13.1765 37.1784C13.1765 37.1784 13.7706 38.6067 13.8208 41.8226C13.8208 41.8226 13.873 45.1745 14.5993 45.9007C14.5993 45.9007 15.3123 46.6138 18.5773 46.6584C18.5773 46.6584 21.8402 46.703 23.3259 47.3252Z"/>
                            <path d="M28.625 18.75V31.875C28.625 32.9105 29.4645 33.75 30.5 33.75C31.5355 33.75 32.375 32.9105 32.375 31.875V18.75C32.375 17.7145 31.5355 16.875 30.5 16.875C29.4645 16.875 28.625 17.7145 28.625 18.75Z" />
                            <path d="M33.3125 40.3125C33.3125 41.8658 32.0532 43.125 30.5 43.125C28.9468 43.125 27.6875 41.8658 27.6875 40.3125C27.6875 38.7592 28.9468 37.5 30.5 37.5C32.0532 37.5 33.3125 38.7592 33.3125 40.3125Z" />
                        </svg>
                    </div>
                    <div class="text-sm text-black text-center mt-4" style="max-width: 500px;">
                        {{ actualIntegrationsText }}
                    </div>
                </div>
            </template>

        </div>
    </div>
</template>

<script>

import { Chart } from 'highcharts-vue'

export default {
    components: {
        highcharts: Chart
    },
    props: {
        integrationsCount: {
            type: Number,
            default: false
        }
    },
    data(){
        return {
            isOpenDropdown: false,
            metrics:  ['l', 'c', 'p', 'q', 'car'],
            selectedMetric: 'l',
            chartOptions: {
                series: [{
                    data: null
                }]
            },
            seriesVisibility: {
                current: true,
                previous: true
            },
            chartData: {},
            loading: false
        }
    },
    computed: {
      actualIntegrationsText(){
        return this.integrationsCount && this.integrationsCount > 0 ? 'You have no chart data yet' : 'Please connect data for your Shopify store (optionally for other data sources) in the Integrations tab';
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

            this.loading = true;

            axios.get('/dashboard/metrics-chart', {
                params: data
            })
            .then(({data}) => {
                this.chartData = data.metrics;
                this.generateChart(this.selectedMetric);
            })
            .catch(({response}) => {
                console.log(response);
            })
            .finally(() => {
                this.loading = false;
            });
        }
    },
    mounted() {
        this.chartOptions = this.getChartOptions();
        this.getChartData();
    }
}
</script>
