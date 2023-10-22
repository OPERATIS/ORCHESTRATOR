<template>
    <div class="relative flex max-w-[76.75rem] p-6 pb-0 flex-1">
        <!--menu-->
        <div class="sticky top-6 w-[13.5rem] bg-primary_light rounded-2xl py-5 px-3 h-full flex flex-col"
             style="height: calc(100vh - 120px);">
            <div class="flex items-center justify-center text-sm text-green_2 cursor-pointer hover:text-opacity-80"
                 @click="createNewChat()">
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
                    <div class="flex items-center h-8 text-sm text-gray_1 px-3 cursor-pointer"
                         v-for="chat in chatsList"
                         :key="chat.id"
                         @click="selectChat(chat.id)"
                         :class="{'bg-primary_blue': chat.id == chatId}"

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
        <div class="relative w-full flex flex-col flex-1">
            <div class="flex flex-col w-full ml-6 mt-3 pb-6 flex-1">
                <template v-if="chatId && !loading">
                    <div class="w-full flex items-center">
                        <div class="flex items-center text-2xl text-black font-semibold">
                            <img class="mr-2.5"
                                 src="/icons/chats-circle.svg"
                                 alt="chat"
                                 style="width: 30px; height: 30px;"
                            >
                            <span>
                                {{ title }}
                            </span>
                        </div>
                        <div class="h-11 flex items-center px-2 rounded-lg bg-primary_light ml-auto">
                            <div class="flex items-center text-sm text-black px-2 cursor-pointer"
                                @click="deleteChat(chatId)">
                                <img class="w-4 h-4 mr-2.5"
                                     src="/icons/trash.svg"
                                     alt="delete"
                                >
                                Delete
                            </div>
                        </div>
                    </div>
                    <div class="pt-3">
                        <template v-if="systemMessage">
                            <div class="flex items-start p-4 bg-primary_light mt-4">
                                <img class="w-8 h-8 mr-4"
                                     src="/icons/chat-gpt.svg"
                                     alt="chat gpt"
                                >
                                <div class="text-sm text-black">
                                    {{systemMessage}}
                                </div>
                            </div>
                        </template>
<!--                        <template v-for="n in 6" :key="n">-->
<!--                            <div class="flex items-start p-4 bg-primary_light mt-4">-->
<!--                                <img class="w-8 h-8 mr-4"-->
<!--                                     src="/icons/chat-gpt.svg"-->
<!--                                     alt="chat gpt"-->
<!--                                >-->
<!--                                <div class="text-sm text-black">-->
<!--                                    our CL1 (Conversion to Leads) is unusually high. Your best performing campaign is campaign A. You might want to invest more here. At the same time your worst performing campaign is campaign B. You might want to invest less here-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="flex items-start p-4 mt-4">-->
<!--                                <img class="h-8 w-8 mr-4"-->
<!--                                     src="/img/profile_icon.png"-->
<!--                                     alt="profile icon"-->
<!--                                >-->
<!--                                <div class="text-sm text-black">-->
<!--                                    Can I remove campaign B completely?-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </template>-->

                        <template v-for="(item, index) in chatMessages" :key="index">
                            <div class="flex items-start p-4 bg-primary_light mt-4">
                                <img class="w-8 h-8 mr-4"
                                     src="/icons/chat-gpt.svg"
                                     alt="chat gpt"
                                >
                                <div class="text-sm text-black">
                                    {{item}}
                                </div>
                            </div>
<!--                            <div class="flex items-start p-4 mt-4">-->
<!--                                <img class="h-8 w-8 mr-4"-->
<!--                                     src="/img/profile_icon.png"-->
<!--                                     alt="profile icon"-->
<!--                                >-->
<!--                                <div class="text-sm text-black">-->
<!--                                    Can I remove campaign B completely?-->
<!--                                </div>-->
<!--                            </div>-->
                        </template>
                    </div>
                </template>
                <template v-else-if="!loading">
                    <div>
                        <div class="flex items-center justify-center opacity-30 mt-3">
                            <img class="h-12" src="/img/logo.svg" alt="logo">
                        </div>
                    </div>
                </template>
            </div>
            <div class="sticky bottom-0 ml-6 pb-8 bg-white w-full">
                <form>
                    <div class="flex relative">
                        <textarea v-model="userMessage" rows="3" placeholder="Ask a question or request, or type '/' for suggestions" id="promt-textarea" class="chat-textarea" @keydown="handleKeyDown"></textarea>
                        <button class="absolute right-6 bottom-5" @click="sendMessage">
                            <img class="h-5 w-5"
                                 src="/icons/paper-plane.svg"
                                 alt="profile icon"
                            >
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            chatsList: null,
            chatMessages: [],
            userMessage: '',
            chatId: null,
            title: '',
            systemMessage: '',
            loading: false
        };
    },
    methods: {
        handleKeyDown(event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault();
                this.sendMessage();
            }
        },
        getChatsList(){
            axios.get('/chats/list')
                .then(({data}) => {
                    this.chatsList = data.chats;
                    console.log(this.chatsList);
                })
                .catch(({response}) => {
                    console.log(response);
                });
        },
        getMessages(id){
            axios.get('/chats/'+id+'/messages')
                .then(({data}) => {
                    console.log(data);
                    this.chatMessages = data.messages;
                })
                .catch(({response}) => {
                    console.log(response);
                });
        },
        selectChat(chatId){
            this.chatId = null;
            this.loading = true;
            let currentURL = window.location.href;
            const paramsRegex = /[?&].*?(?=&|$)/g;
            const params = currentURL.match(paramsRegex);

            if (params) {
                params.forEach(param => {
                    currentURL = currentURL.replace(param, '');
                });
            }

            const separator = currentURL.includes('?') ? '&' : '?';
            const newURL = `${currentURL}${separator}chat_id=${chatId}`;
            window.history.pushState(null, null, newURL);

            this.getMessages(chatId);
            this.getChat(chatId);
            this.chatId = chatId;
        },
        sendMessage(){
            console.log("Відправлено: " + this.userMessage);
            let message = this.userMessage;
            this.userMessage = "";
            let id = this.chatId;

            this.chatMessages.push(message);
            this.scrollToBottom();

            // axios.post('/chats/'+id+'/send-message', {
            //     'content': message
            // })
            //     .then(({data}) => {
            //         console.log(data);
            //         this.chatMessages = data.messages;
            //     })
            //     .catch(({response}) => {
            //         console.log(response);
            //     });
        },
        deleteChat(id){
            axios.post('/chats/'+id+'/delete')
                .then(({data}) => {
                    if(data.status === true){
                        this.clearUrl();
                        this.chatId = null;
                        this.getChatsList();
                    }
                })
                .catch(({response}) => {
                    console.log(response);
                });
        },
        getChat(chatId){
            axios.get('/chats/'+chatId)
                .then(({data}) => {
                    console.log(data);
                    this.chatId = data.chat.id;
                    this.title = data.chat.title;
                    this.systemMessage = data.systemMessage;
                })
                .catch(({response}) => {
                    console.log(response);
                    this.clearUrl();
                    this.chatId = null;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        clearUrl(){
            let currentURL = window.location.href;
            const paramsRegex = /[?&].*?(?=&|$)/g;
            const params = currentURL.match(paramsRegex);

            if (params) {
                params.forEach(param => {
                    currentURL = currentURL.replace(param, '');
                });
            }
            let newURL = `${currentURL}`;
            window.history.pushState(null, null, newURL);
        },
        createNewChat(){
           this.clearUrl();
           this.chatId = null;
           this.loading = false;
        },
        scrollToBottom(){
            setTimeout(function (){
                const scrollTo = document.body.scrollHeight - (window.innerHeight - 120);
                window.scrollTo(0, scrollTo);
            }, 1);
        }
    },
    async mounted() {
        await this.getChatsList();

        const currentURL = window.location.href;
        const urlParts = currentURL.split('?');

        if (urlParts.length === 2) {
            const queryParams = urlParts[1].split('&');
            for (const param of queryParams) {
                const [name, value] = param.split('=');
                if (name === 'chat_id'){
                    this.chatId = value;
                    this.getMessages(value);
                    this.getChat(value);
                    break;
                }
            }
        }
    }
}
</script>
