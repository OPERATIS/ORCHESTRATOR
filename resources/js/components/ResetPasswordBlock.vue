<template>
    <div class="w-full flex flex-col items-center max-w-[28.75rem]">
        <img class="h-12" src="/img/logo.svg" alt="logo">
        <div class="auth-block w-full mt-9 py-9 px-11 border border-grey bg-white rounded-[1.24rem]">
            <div>
                <div class="text-black_5 text-2xl">
                    Reset password
                </div>
                <div class="mt-10 space-y-6">
                    <div class="input-block"
                         v-bind:class="{
                                'error': error && error.password
                             }"
                    >
                        <label for="user_password" class="label">Password</label>
                        <input id="user_password"
                               v-bind:class="{
                                    'fill': password
                               }"
                               placeholder="Enter password"
                               class="input"
                               type="password"
                               v-model="password"
                               @input="error.password = null"
                        >
                        <div v-if="error && error.password" class="error">{{ error.password }}</div>
                    </div>
                    <div class="input-block"
                         v-bind:class="{
                                'error': error && error.confirm_password
                             }"
                    >
                        <label for="user_password_confirm" class="label">Confirm Password</label>
                        <input id="user_password_confirm"
                               v-bind:class="{
                                    'fill': confirm_password
                               }"
                               placeholder="Enter password again"
                               class="input"
                               type="password"
                               v-model="confirm_password"
                               @input="error.confirm_password = null"
                        >
                        <div v-if="error && error.confirm_password" class="error">{{ error.confirm_password }}</div>
                    </div>
                </div>
                <div class="mt-11">
                    <button class="btn lg btn_login w-full"
                            v-bind:class="{
                                'disabled': !password || !confirm_password
                            }"
                            @click="resetPassword()"
                    >
                        Reset password
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            resetToken: null,
            password: null,
            confirm_password: null,
            error: {
                password: null,
                confirm_password: null
            }
        };
    },
    methods: {
        resetPassword() {
            if (this.password && this.confirm_password){
                let data = {
                    token: this.resetToken,
                    password: this.password,
                    confirm_password: this.confirm_password
                };

                axios.post('/reset-password/'+this.resetToken, data)
                    .then(({data}) => {
                        if(data.status === true){
                            window.location.href = data.redirect;
                        } else {
                            console.log(data);
                            this.error.password = data['errors'] && data['errors']['password'] ? data['errors']['password'][0] : null;
                            this.error.confirm_password = data['errors'] && data['errors']['confirm_password'] ? data['errors']['confirm_password'][0] : null;
                        }
                    })
                    .catch(({response}) => {
                        console.log(response.data.message);
                    });
            }
        }
    },
    mounted() {
        const currentUrl = window.location.href;
        const urlParts = currentUrl.split('/');
        const resetPasswordIndex = urlParts.indexOf('reset-password');
        if (resetPasswordIndex !== -1 && resetPasswordIndex + 1 < urlParts.length) {
            this.resetToken = urlParts[resetPasswordIndex + 1];
        }
    }
}
</script>
