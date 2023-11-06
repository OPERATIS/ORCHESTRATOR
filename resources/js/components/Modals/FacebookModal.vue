<template>
    <div class="flex flex-col">
        <div class="text-black_5 text-2xl text-center">
            Chose Accounts for <br/>
            Meta Ads
        </div>
        <div class="flex flex-col mt-6">
            <template v-for="(item, index) in accounts" :key="index">
                <div class="flex items-center border border-black border-opacity-10 px-4 mb-3.5" style="height: 50px; border-radius: 5px;">
                    <label class="custom-checkbox mr-5">
                        <input :id="'facebook_'+item.id" type="checkbox" v-model="item.actual" class="checkbox">
                        <span class="checkmark"></span>
                    </label>
                    <label :for="'facebook_'+item.id" class="text-sm text-black cursor-pointer">{{item.app_user_slug}}</label>
                </div>
            </template>
        </div>
        <button class="btn btn_default lg mt-10" @click="update()">
            Save
        </button>
    </div>
</template>

<script>

export default {
    props: {
        accounts: Object
    },
    data() {
        return {
        };
    },
    methods: {
        update(){
            let data = {
                accounts: this.accounts
            }
            axios.post('/integrations/facebook', data)
                .then(({data}) => {
                    console.log(data);
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                });
        }
    },
    mounted(){
        console.log(this.accounts);
    }
}
</script>
