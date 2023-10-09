<template>
    <div class="w-full flex flex-col items-center max-w-[28.75rem]">
        <img class="h-12" src="/img/logo.svg" alt="logo">
        <div class="auth-block w-full mt-9 py-9 px-11 border border-grey bg-white rounded-[1.24rem]">
        <transition name="auth-block" mode="out-in">
            <template v-if="view === 'sign_in'" :key="'sign_in'">
                <div>
                    <div class="text-black_5 text-2xl">
                        Sign in
                    </div>
                    <div class="mt-10 space-y-6">
                        <div class="input-block">
                            <label for="email" class="label">Email</label>
                            <input id="email"
                                   v-bind:class="{
                                    'fill': email
                              }"
                                   placeholder="Enter email"
                                   class="input"
                                   type="text"
                                   v-model="email"
                            >
                        </div>
                        <div class="input-block"
                             v-bind:class="{
                                'error': error
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
                            >
                            <div v-if="error" class="error">{{ error }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center border-t border-black border-opacity-20 mt-6">
                        <div class="text-xs text-black_4 py-6">
                            Log In with:
                        </div>
                        <div class="p-2 border !border-gray_4 hover:!border-black_2 cursor-pointer"
                             style="border-radius: 10px;"
                        >
                            <img width="30" src="/icons/google-icon.png" alt="google">
                        </div>
                    </div>
                    <div @click="view = 'forgot_password'"
                         class="mt-11 text-sm underline hover:opacity-75 cursor-pointer text-black_4">
                        Forgot password?
                    </div>
                    <div class="mt-3">
                        <button class="btn lg btn_login w-full cursor-pointer"
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
                        <span @click="view = 'sign_up'"
                              class="font-bold underline text-green_2 hover:opacity-75 cursor-pointer"
                        >
                            Sign up
                        </span>
                    </div>
                </div>
            </template>
            <template v-else-if="view === 'sign_up'" :key="'sign_up'">
                <div>
                    <div class="text-black_5 text-2xl">
                        Sign up
                    </div>
                    <div class="mt-10 space-y-6">
                        <div class="input-block">
                            <label for="name" class="label">Name</label>
                            <input id="name"
                                   v-bind:class="{
                                    'fill': name
                              }"
                                   placeholder="Enter name"
                                   class="input"
                                   type="email"
                                   v-model="name"
                            >
                        </div>
                        <div class="input-block">
                            <label for="user_email" class="label">Email</label>
                            <input id="user_email"
                                   v-bind:class="{
                                    'fill': email
                              }"
                                   placeholder="Enter email"
                                   class="input"
                                   type="email"
                                   v-model="email"
                            >
                        </div>
                        <div class="input-block">
                            <label for="user_password" class="label">Password</label>
                            <input id="user_password"
                                   v-bind:class="{
                                    'fill': password
                               }"
                                   placeholder="Enter password"
                                   class="input"
                                   type="password"
                                   v-model="password"
                            >
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
                        <button class="btn lg btn_login w-full"
                                v-bind:class="{
                                    'disabled': !email || !password || !name
                                }"
                                @click="signUp()"
                        >
                            Sign up
                        </button>
                    </div>
                    <div class="mt-3 text-sm text-center text-black_4">
                        Already have an account?
                        <span @click="view = 'sign_in'"
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
                        <div class="input-block">
                            <label for="user_email" class="label">Email</label>
                            <input id="user_email"
                                   v-bind:class="{
                                    'fill': email
                              }"
                                   placeholder="Enter email"
                                   class="input"
                                   type="email"
                                   v-model="email"
                            >
                        </div>
                    </div>
                    <div class="mt-11">
                        <button class="btn lg btn_login w-full"
                                v-bind:class="{
                                'disabled': !email
                            }"
                                @click="resetPassword()"
                        >
                            Send
                        </button>
                    </div>
                    <div class="mt-3 text-sm text-center text-black_4">
                        Back to
                        <span @click="view = 'sign_in'"
                              class="font-bold underline text-green_2 hover:opacity-75 cursor-pointer">
                            Sign in
                        </span>
                    </div>
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
            view: 'sign_in',
            name: null,
            email: null,
            password: null,
            error: null
                // 'Something wrong..'
        };
    },
    methods: {
        login() {
            if (this.email && this.password){
                let data = {
                    email: this.email,
                    password: this.password
                };

                axios.post('/login', data)
                    .then(({data}) => {
                        if(data.status == true){
                            window.location.href = data.redirect;
                        } else {
                            this.error = data['errors'][0];
                            // this.error = data.errors.email[0];
                        }
                        // data.token
                        // data.user
                    })
                    .catch(({response}) => {
                        alert(response.data.message);
                    });
            }
        },
        signUp() {
            if (this.email && this.password && this.name){
                let data = {
                    name: this.name,
                    email: this.email,
                    password: this.password
                };

                axios.post('/registration', data)
                    .then(({data}) => {
                        if(data.status == true){
                            window.location.href = data.redirect;
                        } else {
                            console.log(data);
                            this.error = data['errors'][0];
                            // this.error = data.errors.email[0];
                        }
                        // data.token
                        // data.user
                    })
                    .catch(({response}) => {
                        alert(response.data.message);
                    });
            }
        },
        forgotPassword() {
            if (this.name){
                let data = {
                    email: this.email
                };

                axios.post('/forgot-password', data)
                    .then(({data}) => {
                        if(data.status == true){
                            window.location.href = data.redirect;
                        } else {
                            console.log(data);
                            this.error = data['errors'][0];
                            // this.error = data.errors.email[0];
                        }
                        // data.token
                        // data.user
                    })
                    .catch(({response}) => {
                        alert(response.data.message);
                    });
            }
        }
    },
    mounted() {
    }
}
</script>
