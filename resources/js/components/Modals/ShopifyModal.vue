<template>
    <div class="flex flex-col">
        <div class="text-black_5 text-2xl text-center">
            Chose Accounts for <br/>
            Shopify
        </div>
        <div class="flex flex-col mt-6">
            <template v-if="isEmptyData">
                <div class="text-center">
                    No profiles data
                </div>
            </template>
            <template v-else v-for="(item, index) in accountsData" :key="index">
                <div class="w-full" :class="{'opacity-50': loading, 'hidden': item.delete}">
                    <div class="mb-2 text-right ml-auto" @click="deleteAccount(item.id)">
                        Delete account
                    </div>
                    <div class="flex items-center border border-black border-opacity-10 px-4 mb-3.5"
                         style="height: 50px; border-radius: 5px;"
                    >
                        <label class="custom-checkbox mr-5">
                            <input :id="'shopify_'+item.id" type="checkbox" v-model="item.actual" class="checkbox">
                            <span class="checkmark"></span>
                        </label>
                        <label :for="'shopify_'+item.id" class="text-sm text-black cursor-pointer">{{item.app_user_slug}}</label>
                    </div>
                </div>
            </template>
        </div>
        <button class="btn btn_default lg mt-[1.625rem]" @click="update()">
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
            accountsData: {},
            loading: false
        };
    },
    computed: {
        isEmptyData() {
            return Object.keys(this.accountsData).length === 0;
        }
    },
    methods: {
        update(){
            let data = {
                accounts: this.accountsData
            }
            axios.post('/integrations/shopify', data)
                .then(() => {
                    const customEvent = new CustomEvent('flash-message', {
                        detail: {
                            title: 'Account data updated!',
                            subtitle: 'Data was successfully updated!',
                            type: 'success',
                            time: 3000,
                        }
                    });
                    window.dispatchEvent(customEvent);
                    this.$parent.closeModal();
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                    const customEvent = new CustomEvent('flash-message', {
                        detail: {
                            title: 'Something went wrong!',
                            subtitle: 'Try it again later!',
                            type: 'warning',
                            time: 3000,
                        }
                    });
                    window.dispatchEvent(customEvent);
                });
        },
        getAccountsData(){
            axios.get('/integrations/shopify')
                .then(({data}) => {
                    this.accountsData = data.info;
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        deleteAccount(id){
            let index = this.accountsData.findIndex(obj => obj.id === id);
            if (index !== -1) {
                this.accountsData[index]['delete'] = true;
            }
        }
    },
    mounted(){
        this.accountsData = this.accounts;
        this.loading = true;
        this.getAccountsData();
    }
}
</script>
