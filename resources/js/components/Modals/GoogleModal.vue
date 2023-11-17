<template>
    <div class="flex flex-col">
        <div class="flex flex-col text-black_5 text-2xl">
            <div class="mb-3">
                <template v-if="ads">
                    <img class="h-10 w-auto" src="/img/integrations/google_ads.png" alt="google ads">
                </template>
                <template v-else>
                    <img class="h-10 w-auto" src="/img/integrations/google_analytics.png" alt="google analytics">
                </template>
            </div>
            {{title}}
        </div>
        <div class="flex flex-col mt-6">
            <template v-if="isEmptyData">
                <div class="text-center">
                    No profiles data
                </div>
            </template>
            <template v-else>
                <div class="max-h-[25rem] overflow-auto">
                    <template v-for="(account, index) in accountsData" :key="index">
                        <div class="border border-black border-opacity-10 w-full px-5 pt-3 mb-3" :class="{'opacity-50': loading, 'hidden': account.delete}" style="border-radius: 5px;">
                            <div class="flex text-sm items-center mb-6">
                                Account {{account.id}}
                                <div class="flex items-center ml-auto">
                                    <div class="cursor-pointer" @click="account.actual = !account.actual">
                                        <template v-if="account.actual">
                                            <div class="h-8 flex items-center px-4 text-xs font-semibold text-black bg-primary_green mr-4" style="border-radius: 10px;">
                                                Active
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="h-8 flex items-center px-4 text-xs font-semibold text-black bg-gray_4 bg-opacity-20 mr-4" style="border-radius: 10px;">
                                                Paused
                                            </div>
                                        </template>
                                    </div>
                                    <div class="hover:text-dangers cursor-pointer" @click="deleteAccount(account.id)">
                                    <span class="hover:fill-current">
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
                            <template v-for="(item, index) in account.profiles">
                                <div class="flex items-center border border-black border-opacity-10 px-4 mb-3.5"
                                     style="height: 50px; border-radius: 5px;"
                                >
                                    <label class="custom-checkbox mr-5">
                                        <input :id="'google_'+item.id" class="checkbox" type="checkbox" v-model="item.actual">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label :for="'google_'+item.id" class="w-full text-sm text-black cursor-pointer">{{item.name}}</label>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
        <div class="mt-7">
            <a :href="link" class="flex items-center justify-center cursor-pointer text-green_2 hover:opacity-75">
                <span class="mr-2">+</span> {{add_text}}
            </a>
        </div>
        <button class="btn btn_default lg mt-[1.625rem]" @click="update()">
            Save
        </button>
    </div>
</template>

<script>

export default {
    props: {
        accounts: Object,
        link: String,
        ads: Boolean,
        title: String,
        add_text: String
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
            axios.post('/integrations/google', data)
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
            axios.get('/integrations/google')
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
