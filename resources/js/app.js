require('./bootstrap');

import { createApp } from 'vue';
import LoginBlocks from './components/LoginBlocks.vue';

createApp({
    components: {
        LoginBlocks,
    }
}).mount('#app');
