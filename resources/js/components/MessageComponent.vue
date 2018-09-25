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
            <p class="card-text" v-for="chat in chats" :key="chat.message">
                {{chat.message}}
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
            pushToChats(message){
                this.chats.push({message: message});
            },
            close(){
                this.$emit('close');
            },
            clear(){
                this.chats = [];
            },
            block(){
                this.block_session = true
            },
            unblock(){
                this.block_session = false
            }
        },
        created(){
            this.chats.push(
                {message: "heyy"},
                {message: "heyaay"},
                {message: "heyy you to"},

            )
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
</style>