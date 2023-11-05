<template>
    <div class="flex flex-col">
        <div class="text-black_5 text-2xl text-center">
            Chose Accounts for <br/>
            Google Analytics/Ads
        </div>
        <div class="flex flex-col mt-6">
            <template v-for="(account, index) in accountsData" :key="index">
                <template v-for="item in account.profiles">
                    <div class="flex items-center border border-black border-opacity-10 px-4 mb-3.5" style="height: 50px; border-radius: 5px;">
                        <label class="custom-checkbox mr-5">
                            <input :id="'google_'+item.id" type="checkbox" v-model="item.actual" class="checkbox">
                            <span class="checkmark"></span>
                        </label>
                        <label :for="'google_'+item.id" class="text-sm text-black cursor-pointer">{{item.name}}</label>
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
            accountsData: {}
        };
    },
    methods: {
        update(){
            let data = {
                accounts: this.accountsData
            }
            axios.post('/integrations/google', data)
                .then(({data}) => {
                    console.log(data);
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
        }
    },
    mounted(){
        this.accountsData = this.accounts;
        axios.get('/integrations/google')
            .then(({data}) => {
                console.log(data);
                this.accountsData = data.info;
            })
            .catch(({response}) => {
                console.log(response.data.message);
            });
    }
}
</script>
