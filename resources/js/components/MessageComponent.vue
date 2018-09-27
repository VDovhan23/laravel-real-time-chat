<template>
    <div class="card card-default chat-box" >
        <div class="card-header">
            <b :class="{'text-danger' :session.block}">
                {{friend.name}} <span class="isTyping" v-if="isTyping">typing...</span>
                <span v-if="session.block">(Blocked)</span>
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
                    <a class="dropdown-item" href="#" v-if="session.block && can" @click.prevent='unblock' >UnBlock User</a>
                    <a class="dropdown-item" href="#" v-if="!session.block" @click.prevent='block'>Block User</a>
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
                <input type="text" class="form-control" placeholder="Write your message" :disabled="session.block" v-model="message">
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
                isTyping: false
            }
        },
        computed:{
          session(){
              return this.friend.session
          },
            can(){
              return this.session.blocked_by == auth.id;
            }
        },
        watch:{
          message(value){
              if(value){
              Echo.private((`Chat.${this.friend.session.id}`))
                  .whisper('typing', {
                      name: auth.name
                  });
              }
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
                this.session.block = true;
                axios
                    .post(`/session/${this.friend.session.id}/block`)
                    .then(res => (this.session.blocked_by = auth.id))
            },
            unblock(){
                this.session.block = false;
                axios
                    .post(`/session/${this.friend.session.id}/unblock`)
                    .then(res => (this.session.blocked_by = null))
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
            }),
                Echo.private(`Chat.${this.friend.session.id}`).listen(
                    "BlockEvent",
                    e => (this.session.block = e.blocked)
                );

            Echo.private(`Chat.${this.friend.session.id}`)
                .listenForWhisper('typing', (e) => {
                    this.isTyping = true,
                    setTimeout(()=>{
                        this.isTyping = false
                    },1500)
                }
                );
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
    .message_time {
        font-size: 10px;
        color: #1d6173;
    }
    .isTyping{
        font-size: 10px;
        color: #1d6173;
    }
</style>