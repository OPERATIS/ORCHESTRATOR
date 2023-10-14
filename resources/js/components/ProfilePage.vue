<template>
    <div class="max-w-[76.75rem] px-9 py-6">
        <div class="flex items-center">
            <div class="h-10 w-[20rem] flex justify-center items-center border-b-4 border-green_2 text-black_5">
                Profile Details
            </div>
            <div class="h-10 w-[20rem] flex justify-center items-center text-black_2">
                Plans and billing
            </div>
        </div>
        <div class="bg-primary_light rounded-2xl p-7 mt-16 space-y-6">
            <div class="input-block"
                 v-bind:class="{
                    'error': error && error.brand_name
                 }"
            >
                <label for="brand_name" class="label">Brand Name</label>
                <input id="brand_name"
                       v-bind:class="{
                            'fill': brand_name
                       }"
                       placeholder="Enter brand name"
                       class="input"
                       type="text"
                       v-model="brand_name"
                       @input="error.brand_name = null; is_edit = true;"
                >
                <div v-if="error && error.brand_name" class="error">{{ error.brand_name }}</div>
            </div>
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
                       @input="error.email = null; is_edit = true;"
                >
                <div v-if="error && error.email" class="error">{{ error.email }}</div>
            </div>
            <div class="flex items-center">
                <div class="w-full input-block"
                     v-bind:class="{
                            'error': error && error.password
                         }"
                >
                    <label for="user_password" class="label">Current Password</label>
                    <input id="user_password"
                           v-bind:class="{
                                'fill': password
                           }"
                           placeholder="Enter current password"
                           class="input"
                           type="password"
                           v-model="password"
                           @input="error.password = null"
                    >
                    <div v-if="error && error.password" class="error">{{ error.password }}</div>
                </div>
                <div class="ml-6">
                    <template v-if="isChangePasswordFields">
                        <button class="btn lg border border-dark"
                                @click="isChangePasswordFields = false;"
                        >
                            Close
                        </button>
                    </template>
                    <template v-else>
                        <button class="btn lg btn_login"
                                v-bind:class="{
                                    'disabled': !password
                                }"
                                @click="checkPassword();"
                        >
                            Change password
                        </button>
                    </template>

                </div>
            </div>
            <template v-if="isChangePasswordFields">
                <div class="input-block"
                     v-bind:class="{
                            'error': error && error.new_password
                         }"
                >
                    <label for="user_new_password" class="label">New Password</label>
                    <input id="user_new_password"
                           v-bind:class="{
                                'fill': new_password
                           }"
                           placeholder="Enter new password"
                           class="input"
                           type="password"
                           v-model="new_password"
                           @input="error.new_password = null"
                    >
                    <div v-if="error && error.new_password" class="error">{{ error.new_password }}</div>
                </div>
                <div class="input-block"
                     v-bind:class="{
                            'error': error && error.new_password_confirmation
                         }"
                >
                    <label for="user_new_password_confirmation" class="label">Confirm New Password</label>
                    <input id="user_new_password_confirmation"
                           v-bind:class="{
                                'fill': new_password_confirmation
                           }"
                           placeholder="Enter new password again"
                           class="input"
                           type="password"
                           v-model="new_password_confirmation"
                           @input="error.new_password_confirmation = null"
                    >
                    <div v-if="error && error.new_password_confirmation" class="error">{{ error.new_password_confirmation }}</div>
                </div>
            </template>
            <div class="mt-11">
                <button class="btn lg btn_login w-full"
                        v-bind:class="{
                            'disabled': !is_edit
                        }"
                        @click="save()"
                >
                    Save changes
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        userData: Object
    },
    data() {
        return {
            brand_name: null,
            email: null,
            password: null,
            new_password: null,
            new_password_confirmation: null,
            error: {
                brand_name: null,
                email: null,
                password: null,
                new_password: null,
                new_password_confirmation: null,
            },
            is_edit: null,
            isChangePasswordFields: false
        };
    },
    methods: {
        save(){
            let data = {
                brand_name: this.brand_name,
                email: this.email,
                password: this.password ?? null,
                new_password: this.new_password ?? null,
                new_password_confirmation: this.new_password_confirmation ?? null,
            };
            axios.post('/profile/update', data)
                .then(({data}) => {
                    if (data.status === true) {
                        // додати вспливаху
                    } else {
                        this.error.brand_name = data['errors'] && data['errors']['brand_name'] ? data['errors']['brand_name'][0] : null;
                        this.error.email = data['errors'] && data['errors']['email'] ? data['errors']['email'][0] : null;
                        this.error.password = data['errors'] && data['errors']['password'] ? data['errors']['password'][0] : null;
                        this.error.new_password = data['errors'] && data['errors']['new_password'] ? data['errors']['new_password'][0] : null;
                        this.error.new_password_confirmation = data['errors'] && data['errors']['new_password_confirmation'] ? data['errors']['new_password_confirmation'][0] : null;
                    }
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                });
        },
        checkPassword()
        {
            if (this.password) {
                let data = {
                    password: this.password
                };
                axios.post('/profile/check', data)
                    .then(({data}) => {
                        console.log(data);
                        if (data.status === true) {
                            this.isChangePasswordFields = true;
                        } else {
                            this.error.password = data['errors'] && data['errors']['password'] ? data['errors']['password'][0] : null;
                        }
                    })
                    .catch(({response}) => {
                        console.log(response.data.message);
                    });
            }
        }
    },
    mounted() {
        this.brand_name = this.userData.name ?? null;
        this.email = this.userData.email ?? null;
    }
}
</script>
