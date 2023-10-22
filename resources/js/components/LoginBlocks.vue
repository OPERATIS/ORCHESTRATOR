<template>
    <div class="w-full flex flex-col items-center max-w-[28.75rem]">
        <img class="h-12" src="/img/logo.svg" alt="logo">
        <div class="auth-block w-full mt-9 py-9 px-11 border border-grey bg-white rounded-[1.24rem]">
        <transition name="auth-block" mode="out-in">
            <template v-if="view === 'login'" :key="'login'">
                <div>
                    <div class="text-black_5 text-2xl">
                        Sign in
                    </div>
                    <div class="mt-10 space-y-6">
                        <div class="input-block"
                             v-bind:class="{
                                'error': error && error.email
                             }">
                            <label for="email" class="label">Email</label>
                            <input id="email"
                                   v-bind:class="{
                                    'fill': email
                              }"
                                   placeholder="Enter email"
                                   class="input"
                                   type="text"
                                   v-model="email"
                                   @input="error.email = null"
                                   @keyup.enter="login()"
                            >
                            <div v-if="error && error.email" class="error">{{ error.email }}</div>
                        </div>
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
                                   autocomplete="true"
                                   @input="error.password = null"
                                   @keyup.enter="login()"
                            >
                            <div v-if="error && error.password" class="error">{{ error.password }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center border-t border-black border-opacity-20 mt-6">
                        <div class="text-xs text-black_4 py-6">
                            Log In with:
                        </div>
                        <div class="p-2 border !border-gray_4 hover:!border-black_2 cursor-pointer"
                             style="border-radius: 10px;"
                        >
                            <a href="/google/login">
                                <img width="30" src="/icons/google-icon.png" alt="google">
                            </a>
                        </div>
                    </div>
                    <div @click="changeView('forgot_password')"
                         class="mt-11 text-sm underline hover:opacity-75 cursor-pointer text-black_4">
                        Forgot password?
                    </div>
                    <div class="mt-3">
                        <button class="btn lg btn_default w-full cursor-pointer"
                                v-bind:class="{
                                'disabled': !email || !password
                            }"
                                @click="login()"
                        >
                            Sign in
                        </button>
                    </div>
                    <div class="mt-3 text-sm text-center text-black_4">
                        Don’t have an account yet?
                        <span @click="changeView('registration')"
                              class="font-bold underline text-green_2 hover:opacity-75 cursor-pointer"
                        >
                            Sign up
                        </span>
                    </div>
                </div>
            </template>
            <template v-else-if="view === 'registration'" :key="'registration'">
                <div>
                    <div class="text-black_5 text-2xl">
                        Sign up
                    </div>
                    <div class="mt-10 space-y-6">
                        <div class="input-block"
                             v-bind:class="{
                                'error': error && error.email
                             }"
                        >
                            <label for="user_email" class="label">Email</label>
                            <input id="user_email"
                                   v-bind:class="{
                                    'fill': email
                              }"
                                   placeholder="Enter email"
                                   class="input"
                                   type="email"
                                   v-model="email"
                                   @input="error.email = null"
                                   @keyup.enter="registration()"
                            >
                            <div v-if="error && error.email" class="error">{{ error.email }}</div>
                        </div>
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
                                   @keyup.enter="registration()"
                            >
                            <div v-if="error && error.password" class="error">{{ error.password }}</div>
                        </div>
                        <div class="input-block"
                             v-bind:class="{
                                'error': error && error.password_confirmation
                             }"
                        >
                            <label for="user_password_confirm" class="label">Confirm Password</label>
                            <input id="user_password_confirm"
                                   v-bind:class="{
                                    'fill': password_confirmation
                               }"
                                   placeholder="Enter password again"
                                   class="input"
                                   type="password"
                                   v-model="password_confirmation"
                                   @input="error.password_confirmation = null"
                                   @keyup.enter="registration()"
                            >
                            <div v-if="error && error.password_confirmation" class="error">{{ error.password_confirmation }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center border-t border-black border-opacity-20 mt-6">
                        <div class="text-xs text-black_4 py-6">
                            Sign Up with:
                        </div>
                        <div class="p-2 border !border-gray_4 hover:!border-black_2 cursor-pointer"
                             style="border-radius: 10px;"
                        >
                            <img width="30" src="/icons/google-icon.png" alt="google">
                        </div>
                    </div>
                    <div class="mt-11">
                        <button class="btn lg btn_default w-full"
                                v-bind:class="{
                                    'disabled': !email || !password || !password_confirmation
                                }"
                                @click="registration()"
                        >
                            Sign up
                        </button>
                    </div>
                    <div class="mt-3 text-sm text-center text-black_4">
                        Already have an account?
                        <span @click="changeView('login')"
                              class="font-bold underline text-green_2 hover:opacity-75 cursor-pointer">
                            Sign in
                        </span>
                    </div>
                </div>
            </template>
            <template v-else-if="view === 'forgot_password'" :key="'forgot_password'">
                <div>
                    <div class="text-black_5 text-2xl">
                        Forgot password?
                    </div>
                    <div class="mt-10 space-y-6">
                        <div class="input-block"
                             v-bind:class="{
                                'error': error && error.email
                             }"
                        >
                            <label for="user_email" class="label">Email</label>
                            <input id="user_email"
                                   v-bind:class="{
                                    'fill': email
                              }"
                                   placeholder="Enter email"
                                   class="input"
                                   type="email"
                                   v-model="email"
                                   @input="error.email = null"
                                   @keyup.enter="forgotPassword()"
                            >
                            <div v-if="error && error.email" class="error">{{ error.email }}</div>
                        </div>
                    </div>
                    <div class="mt-11">
                        <button class="btn lg btn_default w-full"
                                v-bind:class="{
                                'disabled': !email
                            }"
                                @click="forgotPassword()"
                        >
                            Send
                        </button>
                    </div>
                    <div class="mt-3 text-sm text-center text-black_4">
                        Back to
                        <span @click="changeView('login')"
                              class="font-bold underline text-green_2 hover:opacity-75 cursor-pointer">
                            Sign in
                        </span>
                    </div>
                </div>
            </template>
            <template v-else-if="view === 'forgot_password_sent'" :key="'forgot_password_sent'">
                <div>
                    <div class="text-black_5 text-center text-xl">
                        Password reset instruction has been sent to your email.
                    </div>
                    <button class="mt-10 btn lg btn_default w-full"
                            @click="changeView('login')"
                    >
                        Back to Sign in
                    </button>
                </div>
            </template>
        </transition>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            view: 'login',
            email: null,
            password: null,
            password_confirmation: null,
            error: {
                email: null,
                password: null,
                password_confirmation: null
            }
        };
    },
    methods: {
        changeView(view){
            this.view = view;
            this.email = null;
            this.password = null;
            this.password_confirmation = null;

            this.error = {
                email: null,
                password: null,
                password_confirmation: null
            };
        },
        login() {
            if (this.email && this.password){
                let data = {
                    email: this.email,
                    password: this.password
                };
                axios.post('/login', data)
                    .then(({data}) => {
                        if(data.status === true){
                            window.location.href = data.redirect;
                        } else {
                            this.error.email = data['errors'] && data['errors']['email'] ? data['errors']['email'][0] : null;
                            this.error.password = data['errors'] && data['errors']['password'] ? data['errors']['password'][0] : null;
                        }
                    })
                    .catch(({response}) => {
                        console.log(response.data.message);
                    });
            }
        },
        registration() {
            if (this.email && this.password && this.password_confirmation){
                let data = {
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                };

                axios.post('/registration', data)
                    .then(({data}) => {
                        if(data.status === true){
                            window.location.href = data.redirect;
                        } else {
                            this.error.email = data['errors'] && data['errors']['email'] ? data['errors']['email'][0] : null;
                            this.error.password = data['errors'] && data['errors']['password'] ? data['errors']['password'][0] : null;
                            this.error.password_confirmation = data['errors'] && data['errors']['password_confirmation'] ? data['errors']['password_confirmation'][0] : null;
                        }
                    })
                    .catch(({response}) => {
                        console.log(response.data.message);
                    });
            }
        },
        forgotPassword() {
            if (this.email){
                let data = {
                    email: this.email
                };

                axios.post('/forgot-password', data)
                    .then(({data}) => {
                        if(data.status == true){
                            this.changeView('forgot_password_sent');
                        } else {
                            this.error.email = data['errors'] && data['errors']['email'] ? data['errors']['email'][0] : null;
                        }
                    })
                    .catch(({response}) => {
                        console.log(response.data.message);
                    });
            }
        }
    }
}
</script>
