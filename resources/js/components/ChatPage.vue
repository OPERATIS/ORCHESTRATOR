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
                         :class="{'bg-primary_blue': chat.id == chatId, 'pointer-events-none': loading}"
                    >
                        <img class="w-6 h-6 mr-2.5"
                             src="/icons/last-chat.svg"
                             alt="chat"
                        >
                        <div class="line-clamp-1">
                            {{ chat.title }}
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
                    <!--header-->
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
                        <div class="h-11 flex items-center px-2 rounded-lg bg-primary_light ml-auto text-black hover:text-dangers transition duration-200">
                            <div class="h-full flex items-center text-sm px-2 cursor-pointer"
                                @click="deleteChat(chatId)">
                                <span class="mr-2.5 group-hover:text-dangers group-hover:fill-current">
                                    <svg width="16" height="16" viewBox="0 0 16 16" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.5 3H2.5C2.22386 3 2 3.22386 2 3.5C2 3.77614 2.22386 4 2.5 4H13.5C13.7761 4 14 3.77614 14 3.5C14 3.22386 13.7761 3 13.5 3Z"/>
                                        <path d="M6 6.5V10.5C6 10.7761 6.22386 11 6.5 11C6.77614 11 7 10.7761 7 10.5V6.5C7 6.22386 6.77614 6 6.5 6C6.22386 6 6 6.22386 6 6.5Z" />
                                        <path d="M9 6.5V10.5C9 10.7761 9.22386 11 9.5 11C9.77614 11 10 10.7761 10 10.5V6.5C10 6.22386 9.77614 6 9.5 6C9.22386 6 9 6.22386 9 6.5Z" />
                                        <path d="M4 13V3.5C4 3.22386 3.77614 3 3.5 3C3.22386 3 3 3.22386 3 3.5V13C3 13.4142 3.29289 13.7071 3.29289 13.7071C3.58579 14 4 14 4 14H12C12.4142 14 12.7071 13.7071 12.7071 13.7071C13 13.4142 13 13 13 13V3.5C13 3.22386 12.7761 3 12.5 3C12.2239 3 12 3.22386 12 3.5V13H4Z"/>
                                        <path d="M5.43934 1.43934C5 1.87868 5 2.5 5 2.5V3.5C5 3.77614 5.22386 4 5.5 4C5.77614 4 6 3.77614 6 3.5V2.5C6 2.29289 6.14645 2.14645 6.14645 2.14645C6.29289 2 6.5 2 6.5 2H9.5C9.70711 2 9.85355 2.14645 9.85355 2.14645C10 2.29289 10 2.5 10 2.5V3.5C10 3.77614 10.2239 4 10.5 4C10.7761 4 11 3.77614 11 3.5V2.5C11 1.87868 10.5607 1.43934 10.5607 1.43934C10.1213 1 9.5 1 9.5 1H6.5C5.87868 1 5.43934 1.43934 5.43934 1.43934Z" />
                                    </svg>
                                </span>
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
                                    {{ systemMessage }}
                                </div>
                            </div>
                        </template>

                        <template v-for="(item, index) in chatMessages" :key="index">
<!--                            <div class="flex items-start p-4 bg-primary_light mt-4">-->
<!--                                <img class="w-8 h-8 mr-4"-->
<!--                                     src="/icons/chat-gpt.svg"-->
<!--                                     alt="chat gpt"-->
<!--                                >-->
<!--                                <div class="text-sm text-black" v-html="item"></div>-->
<!--                            </div>-->
                            <div class="flex items-start p-4 mt-4">
                                <img class="h-8 w-8 mr-4"
                                     src="/img/profile_icon.png"
                                     alt="profile icon"
                                >
                                <div class="text-sm text-black" v-html="item"></div>
                            </div>
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
            <!-- message & suggestions-->
            <div class="sticky bottom-0 ml-6 pb-8 bg-white w-full">
                <template v-if="!chatId && !loading">
                    <div class="grid grid-cols-3 gap-5 mb-5">
                        <div v-for="one in suggestions"
                             class="flex flex-col border border-gray_5 col-span-1 p-3 rounded-[0.625rem] cursor-pointer
                             transition duration-200 hover:border-blue_1 animate-slide-up"
                             style="background: #F6FDFF;"
                             @click="sendSuggestion(one.id)"
                        >
                            <div class="text-sm text-blue_1 font-semibold">{{ one.title }}</div>
                            <div class="text-xs text-gray_3">
                                {{ one.subtitle }}
                            </div>
                        </div>
                    </div>
                </template>
                <form>
                    <div class="flex relative">
                        <textarea v-model="userMessage" rows="3" :class="{'pointer-events-none': loading}" placeholder="Ask a question or request, or type '/' for suggestions" id="promt-textarea" class="chat-textarea" @keydown="handleKeyDown"></textarea>
                        <button class="absolute right-6 bottom-5"
                                type="button"
                                @click="sendMessage"
                                :class="{'!cursor-default' : !userMessage}"
                        >
                            <span class="transition duration-200"
                                  :class="{'text-green_1': userMessage}"
                            >
                                <svg width="20" height="20" viewBox="0 0 20 20" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4539 8.90788C17.4539 8.90788 17.7503 9.07282 17.9227 9.36521C17.9227 9.36521 18.0959 9.659 18.0959 10.0001C18.0959 10.0001 18.0959 10.3411 17.9227 10.6349C17.9227 10.6349 17.7495 10.9287 17.451 11.0938L4.25857 18.4828C4.25857 18.4828 3.91401 18.6732 3.52596 18.6342C3.52596 18.6342 3.13791 18.5952 2.84088 18.3425C2.84088 18.3425 2.54385 18.0898 2.44324 17.713C2.44324 17.713 2.34263 17.3362 2.47413 16.969L4.95858 10.0001L2.47382 3.03024C2.47382 3.03024 2.34263 2.66395 2.44324 2.28715C2.44324 2.28715 2.54385 1.91035 2.84088 1.65761C2.84088 1.65761 3.13791 1.40488 3.52596 1.36589C3.52596 1.36589 3.91401 1.3269 4.25538 1.51549L17.451 8.9063L17.4539 8.90788ZM3.65093 2.60962L16.8431 9.99847L16.8459 10.0001L3.65093 17.3905L6.13187 10.4314C6.13187 10.4314 6.30086 10.0001 6.13187 9.56875L3.65093 2.60962Z"/>
                                    <path d="M5.62503 10.6251H10.625C10.9702 10.6251 11.25 10.3453 11.25 10.0001C11.25 9.65491 10.9702 9.37509 10.625 9.37509H5.62503C5.27985 9.37509 5.00003 9.65491 5.00003 10.0001C5.00003 10.3453 5.27985 10.6251 5.62503 10.6251Z"/>
                                </svg>
                            </span>
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
            chatId: null,
            title: '',
            chatMessages: [],
            systemMessage: '',
            userMessage: '',
            loading: false,
            suggestions: [
                {
                    'id': 1,
                    'title': 'Explain RFM segmentation',
                    'subtitle': 'Learn more about this information'
                },
                {
                    'id': 2,
                    'title': 'Explain RFM segmentation',
                    'subtitle': 'Learn more about this information'
                },
                {
                    'id': 3,
                    'title': 'Explain RFM segmentation',
                    'subtitle': 'Learn more about this information'
                }
            ]
        };
    },
    methods: {
        checkUrl(){
            const currentURL = window.location.href;
            const urlParts = currentURL.split('?');
            if (urlParts.length === 2) {
                const queryParams = urlParts[1].split('&');
                for (const param of queryParams) {
                    const [name, value] = param.split('=');
                    if (name === 'c'){
                        this.loading = true;
                        this.getChat(value);
                        break;
                    }
                }
            }
        },
        getChatsList(){
            axios.get('/chats/list')
                .then(({data}) => {
                    this.chatsList = data.chats;
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                });
        },
        selectChat(chatId){
            this.loading = true;
            this.generateUrl(chatId);
            this.getChat(chatId);
        },
        getChat(chatId){
            axios.get('/chats/'+chatId)
                .then(({data}) => {
                    this.chatId = data.chat.id;
                    this.title = data.chat.title;
                    this.systemMessage = data.systemMessage;
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                    this.generateUrl();
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        deleteChat(chatId){
            axios.post('/chats/'+chatId+'/delete')
                .then(({data}) => {
                    if(data.status === true){
                        this.generateUrl();
                        this.getChatsList();
                        const customEvent = new CustomEvent('flash-message', {
                            detail: {
                                title: 'Chat deleted!',
                                subtitle: 'Chat was successfully deleted!',
                                type: 'delete',
                                time: 3000,
                            }
                        });
                        window.dispatchEvent(customEvent);
                    } else {
                        const customEvent = new CustomEvent('flash-message', {
                            detail: {
                                title: 'Something went wrong!',
                                subtitle: 'Try it again later!',
                                type: 'warning',
                                time: 3000,
                            }
                        });
                        window.dispatchEvent(customEvent);
                    }
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                });
        },
        generateUrl(chatId){
            let currentURL = window.location.href;
            const paramsRegex = /[?&].*?(?=&|$)/g;
            const params = currentURL.match(paramsRegex);
            if (params) {
                params.forEach(param => {
                    currentURL = currentURL.replace(param, '');
                });
            }
            let newURL = `${currentURL}`;
            if (chatId){
                const separator = currentURL.includes('?') ? '&' : '?';
                newURL = `${currentURL}${separator}c=${chatId}`;
                this.chatId = chatId;
            } else {
                this.chatId = null;
            }
            window.history.pushState(null, null, newURL);
        },
        getMessages(chatId){
            axios.get('/chats/'+chatId+'/messages')
                .then(({data}) => {
                    this.chatMessages = data.messages;
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                });
        },
        sendSuggestion(id){
            const suggestion = this.suggestions.find(item => item.id === id);
            if (suggestion) {
                this.userMessage = `${suggestion.title} ${suggestion.subtitle}`;

                // add create chat and send message
                this.chatId = 12;
                this.sendMessage();
            }
        },
        sendMessage(){
            if(this.userMessage){
                let message = this.userMessage;
                message = message.replace(/\n/g, "<br>");
                this.userMessage = "";

                // temp test
                this.chatMessages.push(message);
                this.scrollToBottom();
            }

            // axios.post('/chats/'+this.chatId+'/send-message', {
            //     'content': message
            // })
            //     .then(({data}) => {
            //         this.chatMessages = data.messages;
            //     })
            //     .catch(({response}) => {
            //         console.log(response.data.message);
            //     })
            //     .finally(() => {
            //         this.scrollToBottom();
            //     });
        },
        createNewChat(){
           this.generateUrl();
        },
        handleKeyDown(event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault();
                this.sendMessage();
            }
        },
        scrollToBottom(){
            setTimeout(function (){
                const scrollTo = document.body.scrollHeight - (window.innerHeight - 120);
                window.scrollTo(0, scrollTo);
            }, 1);
        }
    },
    mounted() {
        this.checkUrl();
        this.getChatsList();
    }
}
</script>

<style scoped>
    .animate-slide-up {
        animation: slide-up 0.4s ease-out;
        animation-fill-mode: both;
    }
    @keyframes slide-up {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
