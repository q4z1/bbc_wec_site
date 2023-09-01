<template>
    <div class="container-fluid users">
        <h5>Users</h5>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" id="vtable">
                        <b-table striped responsive :items="tusers" :fields="fields">
                            <template #cell(actions)="data">
                                <div class="row actions float-right">
                                    <div class="col">
                                        <b-icon-pencil-fill variant="warning" @click="edit(data.item.id)"></b-icon-pencil-fill>
                                        <b-icon-trash-fill variant="danger" @click="del(data.item.id)"></b-icon-trash-fill>
                                    </div>
                                </div>
                            </template>
                        </b-table>
                    </div>
                </div>
            </div>
        </div>
        <b-modal v-if="user" ref="delete" id="delete" :title="'Delete User' + this.user.name" ok-disabled hide-footer>
            Are you sure to delete User <strong class="text-warning">{{ this.user.name }}</strong>?<br />
            <b-button class="mt-3" variant="outline-info" block @click="$refs['delete'].hide()">Cancel</b-button>
            <b-button class="mt-2" variant="outline-danger" block @click="doDelete">Yes, Delete!</b-button>
        </b-modal>
        <b-modal v-if="user" ref="edit" id="edit" :title="'Edit '+this.user.name+'\'s role'" ok-disabled hide-footer>
            <b-form-select v-model="role" :options="roleOpts"></b-form-select>
            <b-button class="mt-3" variant="outline-info" block @click="$refs['edit'].hide()">Cancel</b-button>
            <b-button class="mt-2" variant="outline-danger" block @click="doUpdate">Update User!</b-button>
        </b-modal>
    </div>
</template>
<script>
    export default {
        props: ['users'],
        components: {

        },
        data: function() { 
            return {
                user_id: null,
                tusers: null,
                user: null,
                fields: [{key: 'id', sortable: true},{key: 'name', sortable: true},{key: 'role', sortable: true},{key: 'actions', class: 'actions'}],
                roles: {'s': 'Super Admin', a: 'Admin', u: 'User'},
                roleOpts: [
                    { value: 'u', text: 'User' },
                    { value: 'a', text: 'Admin' },
                    { value: 's', text: 'Super Admin' },
                ],
                role: null,
            }
        },
        mounted() {
            this.tusers = this.formatUsers(this.users)
        },
        methods: {
            formatUsers(users){
                return users.map((user) => {
                    let u = {
                        id: user.id,
                        name: user.name,
                        role: this.roles[user.role]
                    }
                    return u
                })
            },
            edit(id) {
                this.user_id = id
                for(let i in this.tusers){
                    if(this.users[i].id == this.user_id){
                        this.user = this.users[i]
                        break
                    }
                }
                this.role = this.user.role
                this.$nextTick(() => {
                    this.$refs['edit'].show()
                })
            },
            doUpdate(){
                let data = new FormData()
                data.append('role', this.role)
                axios({
                    method: "post",
                    url: "/user/update/" + this.user_id,
                    data: data,
                    headers: { "Content-Type": "application/json" },
                })
                .then(response => {
                    if(response.data.success === true){
                        let users = response.data.users
                        this.tusers = this.formatUsers(users)
                        this.$refs['edit'].hide()
                    }
                }, (error) => {
                    console.log(error)
                })
            },
            del(id){
                this.user_id = id
                for(let i in this.tusers){
                    if(this.users[i].id == this.user_id){
                        this.user = this.users[i]
                    }
                }
                this.$nextTick(() => {
                    this.$refs['delete'].show()
                })
            },
            doDelete(){
                axios.get("/user/delete/" + this.user_id)
                .then(response => {
                    if(response.data.success === true){
                        for(let i in this.tusers){
                            if(this.tusers[i].id == this.user_id){
                                this.tusers.splice(i, 1)
                            }
                        }
                        this.$refs['delete'].hide()
                    }
                }, (error) => {
                    console.log(error)
                })
            },
        }
    }
</script>
<style lang="scss">
.users{
    table{
        overflow-x: scroll;
        td, th{
            &.actions{
                width: 75px;
            }
        }
    }
}
</style>
<style lang="scss" scoped>
.users{
    .row{
        &.actions{
            .col{
                display: flex;
                flex-shrink: 2;
                justify-content: flex-end;
                align-items: center;
                .bi-trash-fill{
                    margin-left: 0.5em;
                }
            }
        }
    }
}
</style>