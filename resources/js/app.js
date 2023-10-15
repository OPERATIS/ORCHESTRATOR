require('./bootstrap');

import { createApp } from 'vue';
import vueClickOutsideElement from 'vue-click-outside-element'
import Highcharts from 'highcharts-vue';

import LoginBlocks from './components/LoginBlocks.vue';
import ResetPasswordBlock from './components/ResetPasswordBlock.vue';
import ProfilePage from './components/ProfilePage.vue';
import DashboardChartBlock from './components/DashboardChartBlock.vue';

createApp({
    components: {
        LoginBlocks, ResetPasswordBlock, ProfilePage, DashboardChartBlock
    },
    data() {
        return {
            isOpenMenuDropdown: false
        }
    },
    methods: {
        closeMenuDropdown(){
            this.isOpenMenuDropdown = false;
        }
    }
}).use(vueClickOutsideElement)
  .use(Highcharts)
  .mount('#app');

