<template>
    <div class="flex flex-col">
        <div class="text-black_5 text-2xl text-center">
            Chose Accounts for <br/>
            Google Analytics
        </div>
        <div class="flex flex-col mt-6">
            <template v-for="(item, index) in profiles" :key="index">
                <div class="flex items-center border border-black border-opacity-10 px-4 mb-4" style="height: 50px; border-radius: 5px;">
                    <label class="custom-checkbox mr-5">
                        <input :id="'google_'+item.id" type="checkbox" v-model="item.actual" class="checkbox">
                        <span class="checkmark"></span>
                    </label>
                    <label :for="'google_'+item.id" class="text-sm text-black cursor-pointer">{{item.name}}</label>
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
        profiles: Array
    },
    data() {
        return {
            // profiles: []
        };
    },
    methods: {
        update(){
            console.log(this.profiles);
            let data = {
                platforms: this.profiles
            }
            axios.post('/integrations/google', data)
                .then(({data}) => {
                    console.log(data);
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                });
        }
    },
    mounted() {
        console.log(this.profiles);
        // axios.get('/integrations/google')
        //     .then(({data}) => {
        //         console.log(data);
        //         this.profiles = data.info;
        //     })
        //     .catch(({response}) => {
        //         console.log(response.data.message);
        //     });
    }
}
</script>
