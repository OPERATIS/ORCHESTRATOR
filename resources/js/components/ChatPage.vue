<template>
    <div class="relative flex p-6 pb-0 flex-1">
        <!--menu-->
        <div class="sticky top-6 w-[13.5rem] bg-primary_light rounded-2xl py-5 px-3 h-full flex flex-col"
             style="height: calc(100vh - 44px);">
            <div class="flex items-center justify-center text-sm text-green_2 cursor-pointer hover:text-opacity-80"
                 @click="createNewChat()">
                <img class="w-6 h-6 mr-2"
                     src="/icons/chat-round.svg"
                     alt="new chat"
                >
                New chat
            </div>
            <template v-if="(chatsList && chatsList.length) || chatsListLoading">
                <div class="text-sm text-black text-opacity-20 mt-6">
                    Last Opened
                </div>
            </template>
            <template v-if="chatsList && chatsList.length">
                <div class="flex-col mt-1.5 flex-1 overflow-y-auto -mx-3">
                    <div class="flex items-center h-8 text-sm text-gray_1 px-3 cursor-pointer hover:bg-primary_blue transition duration-200"
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
            <template v-else-if="chatsListLoading">
                <div class="h-8 flex items-center mt-1.5"
                     v-for="n in 4" :key="n"
                >
                    <div class="w-6 h-6 bg-black_2 bg-opacity-30 rounded-md mr-2.5"></div>
                    <div class="h-5 w-[8rem] bg-black_2 bg-opacity-30 rounded-md"></div>
                </div>
            </template>
        </div>
        <div class="relative w-full flex flex-col flex-1 max-w-[84rem] mx-auto">
            <div class="flex flex-col ml-6 mt-3 pb-6 flex-1">
                <template v-if="chatId && !loading">
                    <!--header-->
                    <div class="w-full flex items-center pb-3 pt-9 -mt-9 sticky top-0 bg-white z-10 border-primary_light border-opacity-50"
                    :class="{'border-b': chatMessages.length > 6}">
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
                    <div>
                        <template v-for="(item, index) in chatMessages" :key="index">
                            <template v-if="item.role == 'inner-system'">
                                <div class="p-4 bg-primary_light mt-4">
                                    <div class="flex items-start">
                                        <img class="w-8 h-8 mr-4"
                                             src="/logo.png"
                                             alt="chat gpt"
                                        >
                                        <div class="flex flex-col">
                                            <div class="text-sm text-black text-wrap" v-html="item.content"></div>
                                            <template v-if="showMoreDetails">
                                                <div class="w-max font text-sm font-bold underline text-green_2 mt-2 cursor-pointer hover:opacity-75"
                                                     @click="showMoreDetailsMsg()"
                                                >
                                                    More details
                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                </div>
                            </template>
                            <div v-else-if="item.role == 'assistant'" class="flex items-start p-4 bg-primary_light mt-4">
                                <img class="w-8 h-8 mr-4"
                                     src="/logo.png"
                                     alt="chat gpt"
                                >
                                <div class="text-sm text-black text-wrap" v-html="item.content"></div>
                            </div>
                            <div v-else-if="item.role == 'user'" class="flex items-start p-4 mt-4 user-msg">
                                <img class="h-8 w-8 mr-4" src="/img/profile_icon.png" alt="profile icon">
                                <div class="flex w-full" v-if="!item.editing" >
                                    <div class="text-sm text-black text-wrap" v-html="item.content"></div>
                                    <div class="ml-auto flex-shrink-0">
                                        <div class="user-msg_edit flex items-center cursor-pointer"
                                             @click="editMessage(index, item.id)"
                                        >
                                            <img class="h-6 w-6 ml-6"
                                                 src="/icons/pencil-simple.svg"
                                                 alt="pencil simple"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full flex flex-col pr-12" v-else>
                                    <textarea :ref="'textarea_'+index" rows="1" @input="adjustTextareaHeight(index)" class="textarea-msg resize-none text-sm text-black m-0 border-0 bg-transparent p-0 focus:ring-0 focus-visible:ring-0" v-model="item.editedMessage"></textarea>
                                    <div class="text-center space-x-3 flex justify-center mt-5">
                                        <button class="btn btn_default !rounded-lg md font-medium" @click="saveChanges(index, item.id)">Save Changes</button>
                                        <button class="btn font-medium rounded-lg border border-black border-opacity-40 hover:border-opacity-80 text-gray_1 text-opacity-80" @click="cancelEdit(index)">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-if="loadingMessage">
                            <div class="flex items-start p-4 bg-primary_light mt-4">
                                <img class="w-8 h-8 mr-4"
                                     src="/logo.png"
                                     alt="chat gpt"
                                >
                                <div class="text-sm text-black text-opacity-50 text-wrap">
                                    <div class="load-message">
                                        <div class="dot dot1"></div>
                                        <div class="dot dot2"></div>
                                        <div class="dot dot3"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template v-else-if="!loading">
                    <div>
                        <div class="flex items-center justify-center opacity-30 mt-6">
                            <img class="h-12" src="/img/logo.svg" alt="logo">
                        </div>
                    </div>
                </template>
            </div>
            <!-- message & suggestions-->
            <div class="sticky bottom-0 ml-6 pb-8 bg-white">
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
            showMoreDetails: false,
            userMessage: '',
            loading: false,
            loadingMessage: false,
            chatsListLoading: false,
            suggestions: [
                {
                    'id': 1,
                    'title': 'Tell me what is',
                    'subtitle': 'my CAR metric value?'
                },
                {
                    'id': 2,
                    'title': 'Tell me what metrics',
                    'subtitle': 'should I collect in Google Analytics?'
                },
                {
                    'id': 3,
                    'title': 'Tell me what is',
                    'subtitle': 'TCCP and how it influences revenue?'
                }
            ]
        };
    },
    methods: {
        checkUrl(){
            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            const pathname = url.pathname;
            const regex = /\/chats\/(\d+)/;
            const match = pathname.match(regex);

            if (match) {
                let chatId = match[1];
                this.loading = true;
                this.getChat(chatId);
            }
        },
        getChatsList(){
            this.chatsListLoading = true;
            axios.get('/chats/list')
                .then(({data}) => {
                    this.chatsList = data.chats;
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                })
                .finally(() => {
                    this.chatsListLoading = false;
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
                    this.showMoreDetails = data.showMoreDetails;
                    this.generateChatMessages(data.messages);
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                    this.generateUrl();
                })
                .finally(() => {
                    this.loading = false;
                    this.scrollToBottom();
                });
        },
        generateChatMessages(messages){
            this.chatMessages = messages.map(function(message) {
                if (message.role === "user") {
                    return {
                        ...message,
                        editing: false,
                        editedMessage: ""
                    };
                } else {
                    return message;
                }
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
        generateUrl(chatId){
            let currentURL = window.location.href;
            currentURL = currentURL.split('?')[0];
            currentURL = currentURL.replace(/\/chats\/\d+/g, '/chats');

            if (chatId) {
                currentURL = `${currentURL}/${chatId}`;
                this.chatId = chatId;
            } else {
                this.chatId = null;
            }
            window.history.pushState(null, null, currentURL);
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
                this.sendMessage();
            }
        },
        sendMessage(){
            if(this.userMessage){
                let message = this.userMessage;
                this.userMessage = "";

                this.chatMessages.push({
                    id: null,
                    role: 'user',
                    content: message,
                    editing: false,
                    editedMessage: ""
                });

                this.loadingMessage = true;
                this.showMoreDetails = false;
                this.scrollToBottom();

                if (this.chatId){
                    axios.post('/chats/'+this.chatId+'/send-message', {
                        'content': message
                    })
                        .then(({data}) => {
                            this.loadingMessage = false;
                            this.generateAnswer(data.message);
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
                        })
                        .finally(() => {
                            this.scrollToBottom();
                        });
                } else {
                    this.chatId = -1;
                    axios.post('/chats/create', {
                        'content': message
                    })
                        .then(({data}) => {
                            console.log('req', data);
                            this.chatId = data.chat.id;
                            this.title = data.chat.title;
                            this.showMoreDetails = data.showMoreDetails;
                            this.generateAnswer(data.messages[1]);
                            this.generateUrl(this.chatId);
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
                        })
                        .finally(() => {
                            this.loadingMessage = false;
                            this.scrollToBottom();
                            this.getChatsList();
                        });
                }

            }
        },
        generateAnswer(data){
            console.log('log', this.chatMessages, data);
            const lastIndex = this.chatMessages.slice().reverse().findIndex(item => item.id === null);
            if (lastIndex !== -1) {
                this.chatMessages[this.chatMessages.length - 1 - lastIndex].id = data.send_id || data.id;
            }

            console.log(this.chatMessages);
            this.chatMessages.push({
                id: data.receive_id || data.id,
                role: data.role,
                content: '',
            });

            this.typeMessage(data.content);
        },
        createNewChat(){
           this.generateUrl();
           this.chatMessages = [];
           this.title = "";
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
        },

        editMessage(index) {
            this.chatMessages[index].editing = true;
            this.chatMessages[index].editedMessage = this.chatMessages[index].content;
            this.$nextTick(() => {
                this.adjustTextareaHeight(index);
            });
        },
        saveChanges(index, id) {
            let newMessage = this.chatMessages[index].editedMessage;
            this.chatMessages[index].content = newMessage;
            this.chatMessages[index].editing = false;
            this.chatMessages.splice(index + 1);

            this.loadingMessage = true;

            axios.post('/chats/'+this.chatId+'/edit-message/'+id, {
                'content': newMessage
            })
                .then(({data}) => {
                    this.generateAnswer(data.message);
                })
                .catch(({response}) => {
                    console.log(response.data.message);
                })
                .finally(() => {
                    this.scrollToBottom();
                    this.loadingMessage = false;
                });
        },
        cancelEdit(index) {
            this.chatMessages[index].editing = false;
        },
        adjustTextareaHeight(index) {
            const textarea = this.$refs['textarea_'+index][0];
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        },
        typeMessage(message) {
            const delay = 12;
            let index = 0;

            const typeNextCharacter = () => {
                if (index < message.length) {
                    this.chatMessages[this.chatMessages.length - 1].content += message.charAt(index);
                    index++;
                    this.scrollToBottom();
                    setTimeout(typeNextCharacter, delay);
                }
            };
            typeNextCharacter();
        },
        showMoreDetailsMsg(){
            this.loadingMessage = true;
            this.showMoreDetails = false;

            axios.post('/chats/'+this.chatId+'/more-details')
                .then(({data}) => {
                    this.generateAnswer(data.message);
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
                })
                .finally(() => {
                    this.loadingMessage = false;
                    this.scrollToBottom();
                });
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
    .textarea-msg {
        display: block;
        min-height: 20px;
        line-height: 20px;
        white-space: pre-wrap;
    }
    .text-wrap {
        white-space: pre-line;
    }
</style>
