<template>
    <div class="flex flex-col">
        <div class="text-black_5 text-2xl text-center">
            Chose Accounts for <br/>
            Shopify
        </div>
        <div class="flex flex-col mt-6">
            <template v-for="(account, index) in accounts" :key="index">
                <template v-for="item in account.profiles">
                    <div class="flex items-center border border-black border-opacity-10 px-4 mb-3.5" style="height: 50px; border-radius: 5px;">
                        <label class="custom-checkbox mr-5">
                            <input :id="'shopify_'+item.id" type="checkbox" v-model="item.actual" class="checkbox">
                            <span class="checkmark"></span>
                        </label>
                        <label :for="'shopify_'+item.id" class="text-sm text-black cursor-pointer">{{item.name}}</label>
                    </div>
                </template>
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
            axios.post('/integrations/shopify', data)
                .then(({data}) => {
                    console.log(data);
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                });
        }
    }
}
</script>
