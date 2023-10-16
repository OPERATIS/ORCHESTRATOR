require('./bootstrap');

import { createApp } from 'vue';
import vueClickOutsideElement from 'vue-click-outside-element'
import Highcharts from 'highcharts-vue';

import LoginBlocks from './components/LoginBlocks.vue';
import ResetPasswordBlock from './components/ResetPasswordBlock.vue';
import ProfilePage from './components/ProfilePage.vue';
import DashboardChartBlock from './components/DashboardChartBlock.vue';
import ChatPage from './components/ChatPage.vue';

createApp({
    components: {
        LoginBlocks, ResetPasswordBlock, ProfilePage, DashboardChartBlock, ChatPage
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


function deleteFlash(dom) {
    if (dom) {
        dom.classList.remove('flash_message-appear')
        dom.classList.add('flash_message-disappear')
        setTimeout(() => {
            dom.remove()
        }, 450)
    }
}

window.deleteFlash = deleteFlash;

function showFlash(data) {
    let html = `
        <div class="flash_message flash_message-appear ${data.full ? 'flash_message-full' : ''}">
            ${
        data.type ?
            data.type == 'success' ?
                `<svg class="flash_message-left_icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.00016 14.6668C4.31816 14.6668 1.3335 11.6822 1.3335 8.00016C1.3335 4.31816 4.31816 1.3335 8.00016 1.3335C11.6822 1.3335 14.6668 4.31816 14.6668 8.00016C14.6668 11.6822 11.6822 14.6668 8.00016 14.6668ZM8.00016 13.3335C9.41465 13.3335 10.7712 12.7716 11.7714 11.7714C12.7716 10.7712 13.3335 9.41465 13.3335 8.00016C13.3335 6.58568 12.7716 5.22912 11.7714 4.22893C10.7712 3.22873 9.41465 2.66683 8.00016 2.66683C6.58568 2.66683 5.22912 3.22873 4.22893 4.22893C3.22873 5.22912 2.66683 6.58568 2.66683 8.00016C2.66683 9.41465 3.22873 10.7712 4.22893 11.7714C5.22912 12.7716 6.58568 13.3335 8.00016 13.3335ZM7.3355 10.6668L4.50683 7.83816L5.4495 6.8955L7.3355 8.7815L10.8281 5.28826L11.7714 6.23093L7.3355 10.6668Z" fill="#48F955"/>
                        </svg>` :
                data.type == 'warning' ?
                    `<svg class="flash_message-left_icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.00016 14.6667C4.31816 14.6667 1.3335 11.682 1.3335 8C1.3335 4.318 4.31816 1.33333 8.00016 1.33333C11.6822 1.33333 14.6668 4.318 14.6668 8C14.6668 11.682 11.6822 14.6667 8.00016 14.6667ZM8.00016 13.3333C9.41465 13.3333 10.7712 12.7714 11.7714 11.7712C12.7716 10.771 13.3335 9.41449 13.3335 8C13.3335 6.58551 12.7716 5.22896 11.7714 4.22876C10.7712 3.22857 9.41465 2.66667 8.00016 2.66667C6.58567 2.66667 5.22912 3.22857 4.22893 4.22876C3.22873 5.22896 2.66683 6.58551 2.66683 8C2.66683 9.41449 3.22873 10.771 4.22893 11.7712C5.22912 12.7714 6.58567 13.3333 8.00016 13.3333ZM7.3335 10H8.66683V11.3333H7.3335V10ZM7.3335 4.66667H8.66683V8.66667H7.3335V4.66667Z" fill="#FFCF2A"/>
                        </svg>` :
                    data.type == 'danger' ?
                        `<svg class="flash_message-left_icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.00016 14.6668C4.31816 14.6668 1.3335 11.6822 1.3335 8.00016C1.3335 4.31816 4.31816 1.3335 8.00016 1.3335C11.6822 1.3335 14.6668 4.31816 14.6668 8.00016C14.6668 11.6822 11.6822 14.6668 8.00016 14.6668ZM8.00016 13.3335C9.41465 13.3335 10.7712 12.7716 11.7714 11.7714C12.7716 10.7712 13.3335 9.41465 13.3335 8.00016C13.3335 6.58567 12.7716 5.22912 11.7714 4.22893C10.7712 3.22873 9.41465 2.66683 8.00016 2.66683C6.58567 2.66683 5.22912 3.22873 4.22893 4.22893C3.22873 5.22912 2.66683 6.58567 2.66683 8.00016C2.66683 9.41465 3.22873 10.7712 4.22893 11.7714C5.22912 12.7716 6.58567 13.3335 8.00016 13.3335ZM8.00016 7.0575L9.8855 5.1715L10.8288 6.11483L8.94283 8.00016L10.8288 9.8855L9.8855 10.8288L8.00016 8.94283L6.11483 10.8288L5.1715 9.8855L7.0575 8.00016L5.1715 6.11483L6.11483 5.1715L8.00016 7.0575Z" fill="#FF574C"/>
                        </svg>` :
                        data.type == 'info' ?
                            `<svg class="flash_message-left_icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.00016 14.6667C4.31816 14.6667 1.3335 11.682 1.3335 8C1.3335 4.318 4.31816 1.33334 8.00016 1.33334C11.6822 1.33334 14.6668 4.318 14.6668 8C14.6668 11.682 11.6822 14.6667 8.00016 14.6667ZM8.00016 13.3333C9.41465 13.3333 10.7712 12.7714 11.7714 11.7712C12.7716 10.771 13.3335 9.41449 13.3335 8C13.3335 6.58551 12.7716 5.22896 11.7714 4.22877C10.7712 3.22857 9.41465 2.66667 8.00016 2.66667C6.58567 2.66667 5.22912 3.22857 4.22893 4.22877C3.22873 5.22896 2.66683 6.58551 2.66683 8C2.66683 9.41449 3.22873 10.771 4.22893 11.7712C5.22912 12.7714 6.58567 13.3333 8.00016 13.3333ZM7.3335 4.66667H8.66683V6H7.3335V4.66667ZM7.3335 7.33334H8.66683V11.3333H7.3335V7.33334Z" fill="#BFBFCB"/>
                        </svg>` :
                            data.type == 'delete' ?
                                `<svg class="flash_message-left_icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.66683 4V2C4.66683 1.82319 4.73707 1.65362 4.86209 1.5286C4.98712 1.40357 5.15669 1.33333 5.3335 1.33333H10.6668C10.8436 1.33333 11.0132 1.40357 11.1382 1.5286C11.2633 1.65362 11.3335 1.82319 11.3335 2V4H14.6668V5.33333H13.3335V14C13.3335 14.1768 13.2633 14.3464 13.1382 14.4714C13.0132 14.5964 12.8436 14.6667 12.6668 14.6667H3.3335C3.15669 14.6667 2.98712 14.5964 2.86209 14.4714C2.73707 14.3464 2.66683 14.1768 2.66683 14V5.33333H1.3335V4H4.66683ZM8.94283 9.33333L10.1215 8.15467L9.17883 7.212L8.00016 8.39067L6.8215 7.212L5.87883 8.15467L7.0575 9.33333L5.87883 10.512L6.8215 11.4547L8.00016 10.276L9.17883 11.4547L10.1215 10.512L8.94283 9.33333ZM6.00016 2.66667V4H10.0002V2.66667H6.00016Z" fill="#BFBFCB"/>
                        </svg>` : ''
            : ''}
            <div class="mr-4">
                <div class="flash_message-title">${data.title}</div>
                ${data.subtitle ? `
                    <p class="flash_message-subtitle">${data.subtitle}</p>
                ` : ''}
            </div>
            <button class="flash_message-close" aria-label="Close message" onclick="deleteFlash(event.target.closest('.flash_message'))">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.00011 7.05744L11.7714 3.28614L12.7141 4.2288L8.94278 8.00011L12.714 11.7714L11.7714 12.714L8.00011 8.94278L4.2288 12.7141L3.28613 11.7714L7.05744 8.00011L3.28613 4.2288L4.2288 3.28613L8.00011 7.05744Z"></path>
                </svg>
            </button>
        </div>
    `
    document.querySelector('#flash_messages').insertAdjacentHTML( 'beforeend', html );
    const message = document.querySelector('.flash_message:last-child');
    setTimeout(() => {
        deleteFlash(message)
    }, data.time);
}

window.addEventListener('flash-message', event => {
    showFlash({
        type: event.detail.type,
        title: event.detail.title,
        subtitle: event.detail.subtitle,
        full: event.detail.full,
        time: event.detail.time ? Number(event.detail.time) : 10000,
    })
})

