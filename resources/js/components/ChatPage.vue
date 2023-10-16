<template>
    <div class="relative flex max-w-[76.75rem] p-6 pb-0 flex-1">
        <!--menu-->
        <div class="sticky top-6 w-[13.5rem] bg-primary_light rounded-2xl py-5 px-3 h-full flex flex-col"
             style="height: calc(100vh - 120px);">
            <div class="flex items-center justify-center text-sm text-green_2 cursor-pointer hover:text-opacity-80">
                <img class="w-6 h-6 mr-2"
                     src="/icons/chat-round.svg"
                     alt="new chat"
                >
                New chat
            </div>
            <div class="text-sm text-black text-opacity-20 mt-6">
                Last Opened
            </div>
            <template v-if="chatsList && chatsList.length">
                <div class="flex-col mt-1.5 flex-1 overflow-y-auto -mx-3">
                    <div class="flex items-center h-8 text-sm text-gray_1 px-3"
                         v-for="chat in chatsList"
                         :key="chat.id"

                    >
                        <!--                    style="background: #E2EAF2;"-->
                        <img class="w-6 h-6 mr-2.5"
                             src="/icons/last-chat.svg"
                             alt="chat"
                        >
                        <div class="line-clamp-1">
                            {{chat.title}}
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="h-8 flex items-center mt-1.5"
                     v-for="n in 4" :key="n"
                >
                    <div class="w-6 h-6 bg-black_2 bg-opacity-30 rounded-md mr-2.5"></div>
                    <div class="h-5 w-[8rem] bg-black_2 bg-opacity-30 rounded-md"></div>
                </div>
            </template>
        </div>
        <div class="relative w-full flex-1">
            <div class="flex flex-col w-full ml-6 mt-3 pb-6">
                <div class="w-full flex items-center">
                    <div class="flex items-center text-2xl text-black font-semibold">
                        <img class="mr-2.5"
                             src="/icons/chats-circle.svg"
                             alt="chat"
                             style="width: 30px; height: 30px;"
                        >
                        <span>
                            Topic 1 with tag
                        </span>
                    </div>
                    <div class="h-11 flex items-center px-2 rounded-lg bg-primary_light ml-auto">
                        <div class="flex items-center text-sm text-black px-2 cursor-pointer">
                            <img class="w-4 h-4 mr-2.5"
                                 src="/icons/trash.svg"
                                 alt="delete"
                            >
                            Delete
                        </div>
                    </div>
                </div>
                <div class="pt-3">
                    <template v-for="n in 6" :key="n">
                        <div class="flex items-start p-4 bg-primary_light mt-4">
                            <img class="w-8 h-8 mr-4"
                                 src="/icons/chat-gpt.svg"
                                 alt="chat gpt"
                            >
                            <div class="text-sm text-black">
                                our CL1 (Conversion to Leads) is unusually high. Your best performing campaign is campaign A. You might want to invest more here. At the same time your worst performing campaign is campaign B. You might want to invest less here
                            </div>
                        </div>
                        <div class="flex items-start p-4 mt-4">
                            <img class="h-8 w-8 mr-4"
                                 src="/img/profile_icon.png"
                                 alt="profile icon"
                            >
                            <div class="text-sm text-black">
                                Can I remove campaign B completely?
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="sticky bottom-0 ml-6 pb-8 bg-white w-full">
                <div class="flex relative">
                    <textarea rows="3" placeholder="Ask a question or request, or type '/' for suggestions" id="promt-textarea" class="chat-textarea"></textarea>
                    <div class="absolute right-6 bottom-5">
                        <img class="h-5 w-5"
                             src="/icons/paper-plane.svg"
                             alt="profile icon"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            chatsList: null
        };
    },
    methods: {
        getChatsList(){
            axios.get('/chats/list')
                .then(({data}) => {
                    this.chatsList = data.chats;
                    console.log(this.chatsList);
                })
                .catch(({response}) => {
                    console.log(response);
                });
        }
    },
    async mounted() {
        await this.getChatsList();
    }
}
</script>
