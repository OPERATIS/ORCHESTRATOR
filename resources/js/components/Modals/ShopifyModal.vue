<template>
    <div class="flex flex-col">
        <div class="flex flex-col text-black_5 text-2xl">
            <div class="mb-3">
                <img class="h-10 w-auto" src="/img/integrations/shopping.png" alt="shopify">
            </div>
            Connect your Shopify Account
        </div>
        <div class="flex flex-col mt-6">
            <template v-if="isEmptyData">
                <div class="text-center">
                    No profiles data
                </div>
            </template>
            <template v-else v-for="(item, index) in accountsData" :key="index">
                <div class="w-full" :class="{'opacity-50': loading, 'hidden': item.delete}">
                    <div class="relative flex items-center border border-black border-opacity-10 px-4 mb-3.5"
                         style="height: 50px; border-radius: 5px;"
                    >
                        <label class="custom-checkbox mr-5">
                            <input :id="'shopify_'+item.id" type="checkbox" v-model="item.actual" class="checkbox">
                            <span class="checkmark"></span>
                        </label>
                        <label :for="'shopify_'+item.id" class="text-sm text-black cursor-pointer">{{item.app_user_slug}}</label>
                        <div class="absolute flex items-center p-2 right-3 top-0 text-right">
                            <template v-if="item.actual">
                                <div class="h-8 flex items-center px-4 text-xs font-semibold text-black bg-primary_green mr-4" style="border-radius: 10px;">
                                    Active
                                </div>
                            </template>
                            <template v-else>
                                <div class="h-8 flex items-center px-4 text-xs font-semibold text-black bg-gray_4 bg-opacity-20 mr-4" style="border-radius: 10px;">
                                    Paused
                                </div>
                            </template>
                            <span class="cursor-pointer hover:text-dangers hover:fill-current" @click="deleteAccount(item.id)">
                                <svg width="16" height="16" viewBox="0 0 16 16" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.5 3H2.5C2.22386 3 2 3.22386 2 3.5C2 3.77614 2.22386 4 2.5 4H13.5C13.7761 4 14 3.77614 14 3.5C14 3.22386 13.7761 3 13.5 3Z"/>
                                    <path d="M6 6.5V10.5C6 10.7761 6.22386 11 6.5 11C6.77614 11 7 10.7761 7 10.5V6.5C7 6.22386 6.77614 6 6.5 6C6.22386 6 6 6.22386 6 6.5Z" />
                                    <path d="M9 6.5V10.5C9 10.7761 9.22386 11 9.5 11C9.77614 11 10 10.7761 10 10.5V6.5C10 6.22386 9.77614 6 9.5 6C9.22386 6 9 6.22386 9 6.5Z" />
                                    <path d="M4 13V3.5C4 3.22386 3.77614 3 3.5 3C3.22386 3 3 3.22386 3 3.5V13C3 13.4142 3.29289 13.7071 3.29289 13.7071C3.58579 14 4 14 4 14H12C12.4142 14 12.7071 13.7071 12.7071 13.7071C13 13.4142 13 13 13 13V3.5C13 3.22386 12.7761 3 12.5 3C12.2239 3 12 3.22386 12 3.5V13H4Z"/>
                                    <path d="M5.43934 1.43934C5 1.87868 5 2.5 5 2.5V3.5C5 3.77614 5.22386 4 5.5 4C5.77614 4 6 3.77614 6 3.5V2.5C6 2.29289 6.14645 2.14645 6.14645 2.14645C6.29289 2 6.5 2 6.5 2H9.5C9.70711 2 9.85355 2.14645 9.85355 2.14645C10 2.29289 10 2.5 10 2.5V3.5C10 3.77614 10.2239 4 10.5 4C10.7761 4 11 3.77614 11 3.5V2.5C11 1.87868 10.5607 1.43934 10.5607 1.43934C10.1213 1 9.5 1 9.5 1H6.5C5.87868 1 5.43934 1.43934 5.43934 1.43934Z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div class="mt-[1.625rem]">
            <div @click="emitOpenModal" class="flex items-center justify-center cursor-pointer text-green_2 hover:opacity-75">
                <span class="mr-2">+</span> Add new Shopify Account
            </div>
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
        },
        emitOpenModal() {
            this.$parent.closeModal();
            this.$root.openModal('modal_shopify_url');
        }
    },
    mounted(){
        this.accountsData = this.accounts;
        this.loading = true;
        this.getAccountsData();
    }
}
</script>
