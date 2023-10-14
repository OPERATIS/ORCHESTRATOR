<template>
    <div>
        <highcharts :options="chartOptions"></highcharts>
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
                    data: [1, 2, 3] // sample data
                }]
            }
        }
    },
    methods: {
        generateRandomData() {
            const data = [];
            for (let i = 0; i < 7; i++) {
                data.push(Math.floor(Math.random() * 100)); // Генерувати випадкові числа для 7 днів
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
                console.log(data, data.current)
                this.chartOptions = {
                    title: {
                        text: 'Графік метрик'
                    },
                    xAxis: {
                        categories: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7']
                    },
                    series: [{
                        name: 'Поточні',
                        data: data.metrics.current
                    }, {
                        name: 'Попередні',
                        data: data.metrics.previous
                    }]
                };
                console.log(this.chartOptions);
                // this.chart_data = data.metrics;
                // console.log(this.chart_data);

                Highcharts.charts[0].update(this.chartOptions, true);

            })
            .catch(({response}) => {
                console.log(response.data.message);
            });
        }
    },
    async mounted() {
        await this.getChartData();

        // const current = this.generateRandomData();
        // const previous = this.generateRandomData();
        //
        // this.chart_data = {
        //     current,
        //     previous
        // };
        //
        // this.chartOptions = {
        //     title: {
        //         text: 'Графік метрик'
        //     },
        //     xAxis: {
        //         categories: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7']
        //     },
        //     series: [{
        //         name: 'Поточні',
        //         data: current
        //     }, {
        //         name: 'Попередні',
        //         data: previous
        //     }]
        // };
    }
}
</script>
