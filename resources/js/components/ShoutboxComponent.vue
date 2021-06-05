<template>
    <div class="shoutbox">
        <h3>Shoutbox</h3>
        <b-row class="box">
            <b-col v-if="posts">
                <b-card no-body no-header>
                    <infinite-loading direction="top" @infinite="infiniteHandler">
                        <span slot="no-more"></span>
                        <span slot="no-results"></span>
                    </infinite-loading>
                    <b-list-group flush v-if="show">
                        <b-list-group-item v-for="(post, index) in posts" :key="post.id" :class="(post.active === 3) ? 'admin' : ''">
                            <b-row class="head">
                                <b-col >
                                    <h6 class="float-left mr-2 mb-0 pb-0">#{{ post.id }}</h6>
                                    <h6 :class="'float-left mb-0 pb-0 ' + ((post.active > 1) ? 'text-danger' : 'text-info')">{{ post.nickname }}</h6>
                                    <h6><small class="ml-2">{{ date(index) }}</small></h6>
                                </b-col>
                                <b-col v-if="arole === 's'" class="data">
                                    <b-row>
                                        <b-col sm="4"><small class="text-success font-weight-bolder">IP:</small></b-col>
                                        <b-col><small>{{ post.ip }}</small></b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col sm="4"><small class="text-primary font-weight-bolder">Fingerprint:</small></b-col>
                                        <b-col><small>{{ post.fp }}</small></b-col>
                                    </b-row>
                                </b-col>
                                <b-col class="text-right actions">
                                    <b-row>
                                        <b-col class="text-warning" v-if="user !== null && post.user_id === user.id">
                                            <b-icon-pencil-fill class="actions" role="button" @click="edit(post.id)"></b-icon-pencil-fill>
                                        </b-col>
                                        <b-col class="text-danger ml-2" v-if="arole === 's'">
                                            <b-icon-trash-fill class="actions" role="button" @click="del(post.id)"></b-icon-trash-fill>
                                        </b-col>
                                    </b-row>
                                </b-col>
                            </b-row>
                            <b-row>
                                <b-col><hr /></b-col>
                            </b-row>
                            <b-row>
                                <b-col>{{ post.message }}</b-col>
                            </b-row>
                            <b-row v-if="post.created_at < post.updated_at">
                                <b-col><small class="font-italic">Last edited: {{ date(index, true) }}</small></b-col>
                            </b-row>
                        </b-list-group-item>
                    </b-list-group>
                </b-card>
            </b-col>
        </b-row>
        <b-row class="actions mt-2">
            <b-col>
                <b-card>
                    <b-row>
                        <b-col md="4" cols="12" class="nick_admin">
                                <b-row>
                                    <b-col class="nickname">
                                        <b-input-group>
                                            <b-input-group-prepend>
                                                <b-button variant="warning" :disabled="true"><b-icon-people-fill variant="white"></b-icon-people-fill></b-button>
                                            </b-input-group-prepend>
                                            <b-form-input
                                                id="nickname"
                                                placeholder="PokerTH Nickname"
                                                v-model="nickname"
                                                :disabled="arole !== ''"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                                <b-row v-if="arole === 'a' || arole === 's'">
                                    <b-col class="admin">
                                        <b-form-group
                                            label="Admin-post:"
                                            label-for="check-button"
                                            label-cols="6"
                                            label-size="lg"
                                            label-align-sm="left"
                                        >
                                            <b-form-checkbox size="lg" class="mt-2 text-right" v-model="admin_post" name="check-button" switch></b-form-checkbox>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col class="message">
                                <b-row>
                                    <b-col class="msg">
                                        <b-form-textarea
                                            id="sbmsg"
                                            placeholder="Type your message"
                                            rows="3"
                                            max-rows="8"
                                            v-model="sbmsg"
                                        ></b-form-textarea>
                                    </b-col>
                                    <b-col class="send">
                                        <b-button variant="success" @click="post" class="send"><b-icon-cursor-fill></b-icon-cursor-fill></b-button>
                                    </b-col>
                                </b-row>
                            </b-col>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
        <b-modal ref="delete" id="delete" :title="'Delete Post #' + this.sbid" ok-disabled hide-footer>
            Are you sure to delete post <strong class="text-warning">#{{ this.sbid }}</strong>?<br />
            <b-button class="mt-3" variant="outline-info" block @click="$refs['delete'].hide()">Cancel</b-button>
            <b-button class="mt-2" variant="outline-danger" block @click="doDel">Yes, Delete!</b-button>
        </b-modal>
        <b-modal ref="edit" id="edit" :title="'Edit Post #' + this.sbid" ok-disabled hide-footer>
            <b-form-textarea
                id="sbmsg"
                rows="3"
                max-rows="8"
                v-model="sbmsg"
            ></b-form-textarea>
            <b-form-group
                label="Admin-post:"
                label-for="check-button"
                label-cols="6"
                label-size="lg"
                label-align-sm="left"
            >
                <b-form-checkbox size="lg" class="mt-2 text-right" v-model="admin_post" name="check-button" switch></b-form-checkbox>
            </b-form-group>
            <b-button class="mt-3" variant="outline-info" block @click="$refs['edit'].hide()">Cancel</b-button>
            <b-button class="mt-2" variant="outline-danger" block @click="doUpdate">Update Post!</b-button>
        </b-modal>
    </div>
</template>
<script>
import FingerprintJS from '@fingerprintjs/fingerprintjs'
import InfiniteLoading from 'vue-infinite-loading';
export default {
    components: {
        InfiniteLoading,
    },
    props: ['user'],
    data() {
        return {
            fp: null,
            arole: window.arole,
            total: 0,
            posts: [],
            sbmsg: null,
            sbid: null,
            nickname: null,
            offset: 0,
            admin_post: false,
            show: true,
        }
    },
    computed: {

    },
    mounted() {
        const fpPromise = FingerprintJS.load();(async () => {
            let fp = await fpPromise
            let result = await fp.get()
            this.fp = result.visitorId
        })()
        if(this.user !== null){
            this.nickname = this.user.name
        }
        // this.filter()
    },
    methods: {
        date(index, updated=false) {
            let d = new Date(Date.parse((updated) ? this.posts[index].updated_at : this.posts[index].created_at))
            return d.getFullYear() + '-' + this.pad(d.getMonth()) + '-' + this.pad((d.getDay() + 1)) + ' ' + this.pad(d.getHours()) + ':' + this.pad(d.getMinutes()) + ':' + this.pad(d.getSeconds())
        },
        pad(num) {
            num = num.toString()
            while (num.length < 2) num = "0" + num
            return num
        },
        filter(state=null){
            let data = new FormData()
            if(this.sbid !== null) data.append('id', this.sbid)
            data.append('offset', this.offset)
            axios({
                method: "post",
                url: "/shoutbox",
                data: data,
                headers: { "Content-Type": "multipart/form-data" },
            })
            .then(response => {
                if(response.data.success === true){
                    if(state === null){
                        this.posts = response.data.posts.reverse()
                        this.$nextTick(() => {
                            $( ".shoutbox .box .card" ).prop('scrollTop', $( ".shoutbox .box .card" ).prop('scrollHeight'))
                        })
                    }
                    else{
                        if(response.data.posts.length !== 0){
                            this.offset += 25;
                            this.posts.unshift(...response.data.posts.reverse())
                            state.loaded()
                        }else{
                            state.complete()
                        }
                    } 
                }
            }, (error) => {
                console.log(error)
            })
        },
        post(){
            let data = new FormData()
            data.append('message', this.sbmsg)
            data.append('fp', this.fp)
            data.append('nickname', this.nickname)
            data.append('admin_post', (this.admin_post) ? 1 : 0)
            axios({
                method: "post",
                url: "/shoutbox/new",
                data: data,
                headers: { "Content-Type": "multipart/form-data" },
            })
            .then(response => {
                if(response.data.success === true){
                    this.posts = response.data.posts
                    this.$nextTick(() => {
                        $( ".shoutbox .box .card" ).prop('scrollTop', $( ".shoutbox .box .card" ).prop('scrollHeight'))
                    })
                }
            }, (error) => {
                console.log(error)
            })
            this.sbmsg = null
        },
        getPostById(id){
            for(let i in this.posts){
                if(this.posts[i].id === id) return this.posts[i]
            }
            return null;
        },
        edit(id){
            this.sbid = id
            let post = this.getPostById(id)
            this.sbmsg = post.message
            this.admin_post = (post.active === 3) ? true : false
            this.$refs['edit'].show()
        },
        doUpdate(){
            let data = new FormData()
            data.append('message', this.sbmsg)
            data.append('admin_post', (this.admin_post) ? 1 : 0)
            axios({
                method: "post",
                url: "/shoutbox/update/" + this.sbid,
                data: data,
                headers: { "Content-Type": "multipart/form-data" },
            })
            .then(response => {
                if(response.data.success === true){
                    let post = response.data.post
                    for(let i in this.posts){
                        if(this.posts[i].id == post.id){
                            this.posts[i] = post
                            break
                        }
                    }
                    this.$refs['edit'].hide()
                    this.sbid = null
                }
            }, (error) => {
                console.log(error)
            })
            this.sbmsg = null
        },
        del(id){
            console.log('delete', id)
            this.sbid = id
            this.$refs['delete'].show()
        },
        doDel(){
            axios.get("/shoutbox/delete/" + this.sbid)
            .then(response => {
                if(response.data.success === true){
                    for(let i in this.posts){
                        if(this.posts[i].id == this.sbid){
                            this.posts.splice(i, 1)
                        }
                    }
                    this.$refs['delete'].hide()
                }
            }, (error) => {
                console.log(error)
            })
        },
        infiniteHandler($state) {
            this.filter($state)
        },
    }
}
</script>
<style lang="scss" scoped>
.shoutbox{
    height: calc(100vh - 150px);
    width: 100%;
    .btn{
        &.disabled, &:disabled{
            opacity: 1;
        }
    }

    .row{
        &.box{
            height: calc(100vh - 300px);
            .col{
                .card{
                    height: calc(100vh - 300px);
                    overflow-y: auto;
                    .list-group{
                        .list-group-item{
                            &.admin{
                                background-color: var(--admin);
                            }
                            .row{
                                hr{
                                    border: 1px solid var(--secondary);
                                    margin: 0 0 0.1em 0;
                                    opacity: 0.1;
                                }
                                &.head{
                                    .col{
                                        &.actions{
                                            display: flex;
                                            flex-shrink: 2;
                                            justify-content: flex-end;
                                            .row{
                                                margin-left: 0;
                                                margin-right: 0;
                                                .col{
                                                    padding-left: 0;
                                                    padding-right: 0;
                                                }
                                            }
                                        }
                                        &.data{
                                            .row{
                                                margin-top: -0.75em;
                                            }
                                            opacity: 0.6;
                                        }
                                    }
                                }
                                
                            }
                        }
                    }
                }
            }
        }
        &.actions{
            .col{
                .card{
                    .row {
                        .col{
                            &.nick_admin{
                                .row{
                                    .col{
                                        button{
                                            cursor: default;
                                            .b-icon {
                                                cursor: default;
                                            }
                                        }
                                    }
                                }
                            }
                            &.message{
                                .row{
                                    margin-left: 0;
                                    margin-right: 0;
                                    .col{
                                        padding-right: 0px;
                                        padding-left: 0px;
                                        &.msg{
                                            display: flex;
                                            flex-grow: 10;
                                        }
                                        &.send{
                                            display: flex;
                                            flex-shrink: 2;
                                            justify-content: flex-end;
                                            align-items: flex-start;
                                            margin-left: 5px;
                                            button{
                                                svg{
                                                    transform: rotate(45deg);
                                                }
                                            }
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
</style>