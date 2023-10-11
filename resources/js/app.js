require('./bootstrap');

import { createApp } from 'vue';
import vueClickOutsideElement from 'vue-click-outside-element'

import LoginBlocks from './components/LoginBlocks.vue';
import ResetPasswordBlock from './components/ResetPasswordBlock.vue';

createApp({
    components: {
        LoginBlocks, ResetPasswordBlock
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
}).use(vueClickOutsideElement).mount('#app');

