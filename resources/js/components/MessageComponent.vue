<template>
    <div class="card card-default chat-box" >
        <div class="card-header">
            <b :class="{'text-danger' :block_session}">
                {{friend.name}}
                <span v-if="block_session">(Blocked)</span>
            </b>
            <!--close button-->
            <a href="" @click.prevent='close'>
                <i class="fas fa-times float-right" ></i>
            </a>

            <div class="dropdown float-right mr-4" >
                <a href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v "></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#" v-if="block_session" @click.prevent='unblock' >UnBlock User</a>
                    <a class="dropdown-item" href="#" v-else @click.prevent='block'>Block User</a>
                    <a class="dropdown-item" href="#" @click.prevent='clear'>Clear Chat</a>
                </div>
            </div>

        </div>
        <div class="card-body" v-chat-scroll>
            <p class="card-text" :class="{'text-right':chat.type == 0}" v-for="chat in chats" :key="chat.id">
                {{chat.message}}
                <br>
                <span class="message_time">{{chat.send_at}}</span>
            </p>
        </div>
        <form  class="card-footer" @submit.prevent="send">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Write your message" :disabled="block_session" v-model="message">
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props:['friend'],
        data(){
            return{
                chats:[],
                message: null,
                block_session: false
            }
        },
        methods: {
            send(){
                if (this.message) {
                    this.pushToChats(this.message);
                    axios.post(`/send/${this.friend.session.id}`, {
                        content : this.message,
                        to_user: this.friend.id
                    });
                    this.message = null;
                }
            },
            pushToChats(message) {
                this.chats.push({
                    message: message,
                    type:0,
                    send_at: 'just now'
                });
            },
            close(){
                this.$emit('close');
            },
            clear(){

                axios.post(`/session/${this.friend.session.id}/clear`)
                    .then(res=> (this.chats = []));
            },
            block(){
                this.block_session = true
            },
            unblock(){
                this.block_session = false
            },
            getAllMessages(){
                axios
                    .post(`/session/${this.friend.session.id}/chats`)
                    .then(res=>(this.chats = res.data.data));
            }
        },
        created(){
            this.getAllMessages();

            Echo.private(`Chat.${this.friend.session.id}`).listen('PrivateChatEvent', e=>{
                this.chats.push({
                    message: e.content,
                    type:1,
                    send_at: 'just now'
                });
            })
        }
    }
</script>

<style scoped>
    .chat-box{
        height: 400px;
    }
    .card-body{
        overflow-y: scroll;
    }
    .message_time{
        font-size: 10px;
        color: #1d6173;
    }
</style>