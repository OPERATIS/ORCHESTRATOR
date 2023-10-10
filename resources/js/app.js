require('./bootstrap');

import { createApp } from 'vue';
import LoginBlocks from './components/LoginBlocks.vue';
import ResetPasswordBlock from './components/ResetPasswordBlock.vue';

createApp({
    components: {
        LoginBlocks, ResetPasswordBlock
    }
}).mount('#app');
