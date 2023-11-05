<template>
    <div class="modal" v-if="show" :class="{ 'active': show }" v-cloak>
        <div class="modal-content" :style="{ 'max-width': maxWidth }">
            <span class="close-modal" @click="closeModal">
                <img class="w-6 h-6"
                     src="/icons/close.svg"
                     alt="close"
                >
            </span>
            <div class="modal-body">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        maxWidth: {
            type: String,
            default: '635px;'
        }
    },
    data() {
        return {
            show: false
        };
    },
    methods: {
        openModal() {
            this.show = true;
        },
        closeModal() {
            this.show = false;
        }
    }
}
</script>
