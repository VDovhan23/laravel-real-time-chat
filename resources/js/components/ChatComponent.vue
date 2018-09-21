<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card card-default">
                    <div class="card-header">
                        Private chat
                    </div>
                    <ul class="list-group">
                        <!--<a href=""-->
                           <!--@click.prevent="openChat(user)"-->
                           <!--:key="user.id"-->
                           <!--v-for="user in users">-->
                            <li class="list-group-item"> GENHJ</li>
                        <!--</a>-->
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <!--<span v-for="user in users" :key="user.id" v-if="user.session">-->
                <message-component
                        v-if="open"
                        @close="close"
                ></message-component>
            <!--</span>-->
            </div>
        </div>
    </div>

</template>

<script>
    import MessageComponent from "./MessageComponent";
    export default {
        data(){
            return{
                open: true
            }
        },
        methods:{
            close(){
                this.open = false
            }
        },
        created(){

        },
        components: { MessageComponent }
    }
</script>
