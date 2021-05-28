<template>
    <div>
        <b-card class="mb-2">
            <b-card-text>
                <div class="preview-area mb-2 text-center" v-if="award">
                    <b-row>
                        <b-col class="img">
                            <img id="preview" :src="award_preview" :alt="award.title" class="img-fluid mx-auto d-block" />
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col class="text-primary mt-2">{{ award.title }}</b-col>
                    </b-row>
                </div>
                <hr />
                <b-card no-body header="Assignments" v-if="assignments" class="mt-2">
                    <b-list-group flush>
                        <b-list-group-item v-for="(player, key) in assignments" :key="key">
                            <b-row>
                                <b-col>{{ player.nickname }}</b-col>
                                <b-col class="text-right text-success">
                                    <b-icon-check-square-fill role="button" @click="unAssign(player.id)"></b-icon-check-square-fill>
                                </b-col>
                            </b-row>
                        </b-list-group-item>
                    </b-list-group>
                </b-card>
                <hr v-if="assignments" />
                <b-form @submit="doSubmit" @reset="doReset" v-if="show">
                    <b-form-group
                    label="Player:"
                    label-for="player"
                    class="mb-2"
                    v-if="show"
                    >
                        <b-row>
                            <b-col class="w-75"><b-form-input name="player" v-model="p_input" list="player-list" @input="doAssign"></b-form-input></b-col>
                        </b-row>
                        <datalist id="player-list">
                            <option v-for="(player, key) in players" :key="key" v-html="player.nickname"></option>
                        </datalist>
                        <hr />
                        <b-row>
                            <b-col class="form-action">
                                <b-button type="submit" variant="primary">Submit</b-button>
                                <b-button type="reset" variant="danger">Reset</b-button>
                            </b-col>
                        </b-row>
                    </b-form-group>
                </b-form>
            </b-card-text>
        </b-card>
    </div>
</template>
<script>
export default {
    props: ['award', 'players'],
    data() {
        return {
            show: true,
            award_preview: null,
            assignments: null,
            ass_o: null,
            p_input: null,
        }
    },
    computed: {

    },
    mounted() {
        this.award_preview = this.award.award
        this.getAssignments()
    },
    methods: {
        getAssignments(){
            axios.get('/awards/assignments/' + this.award.id)
            .then(response => {
                if(response.data.success === true){
                    this.ass_o = this.assignments = response.data.assignments
                }else{
                    console.log(response.data)
                }
            }, (error) => {
                console.log(error)
            });
        },
        doSubmit(evt){
            evt.preventDefault()
            evt.stopPropagation()
            let data = new FormData()
            for(var i = 0; i < this.assignments.length; i += 1) {
                data.append('player[]', this.assignments[i].id)
            }
            axios.post('/awards/assign/' + this.award.id, data)
            .then(response => {
                if(response.data.success === true){
                    this.$root.$emit('bv::hide::modal', 'modal-assign')
                }else{
                    console.log(response.data)
                }
            }, (error) => {
                console.log(error)
            });
        },
        doAssign(p) {
            p = this.getPlayerByName(p)
            if(p === null) return
            if(this.assignments === null) this.assignments = []
            let pi = this.checkAssignmentIndexById(p.id)
            if(pi === null) this.assignments.push({ nickname: p.nickname, id: p.id})
            // Trick to reset/clear native browser form validation state
            this.p_input = null
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
        unAssign(player_id) {
            let index = this.checkAssignmentIndexById(player_id)
            this.assignments.splice(index, 1)
        },
        getPlayerByName: function (name) {
            for(var i = 0; i < this.players.length; i += 1) {
                if(this.players[i]['nickname'] === name) {
                    return this.players[i];
                }
            }
            return null;
        },
        checkAssignmentIndexById: function (id) {
            for(var i = 0; i < this.assignments.length; i += 1) {
                if(this.assignments[i]['id'] === id) {
                    return i;
                }
            }
            return null;
        },
        doReset(evt) {
            evt.preventDefault()
            this.assignments = this.ass_o
        }
    }
}
</script>
<style lang="scss" scoped>
[role=button] {
    outline: none;
    filter: brightness(80%);
    &:hover{
        filter: brightness(100%);
    }
    &:hover,&:active,&:visited,&:focus{
        outline: none;
    }
}
.form-action{
    margin-left: 10px;
}

img#preview{
    width: 175px;
}
.card-header{
    padding: .4rem .7rem;
}
.list-group{
    .list-group-item{
        font-size: 0.9em;
        padding: .4rem .7rem;
    }
}

</style>