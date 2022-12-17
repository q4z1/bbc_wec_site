<template>
    <div>
        <b-card title="Game Upload" class="mb-2">
            <b-card-text>
                <b-form @submit="onSubmit" @reset="onReset" v-if="show">
                    <b-form-group id="input-group-1" label="Game Type:" label-for="input-1">
                        <b-form-select
                        id="input-1"
                        v-model="form.gametype"
                        required>
                        <option v-for="type in types" 
                            :key="type.value"
                            :value="type.value"
                        >{{ type.text }}</option>
                        </b-form-select>
                    </b-form-group>
                    <b-form-group
                        id="input-group-2"
                        label="Log-URL:"
                        label-for="input-2">
                        <b-form-input
                        id="input-2"
                        v-model="form.loglink"
                        type="text"
                        required
                        placeholder="https://pokerth.net/gamelog?pdb=1234567890abcdef&game_id=1"
                        ></b-form-input>
                    </b-form-group>
                    <b-form-group
                        id="input-group-3"
                        label="Game-Number:"
                        label-for="input-3">
                        <b-form-input
                        id="input-3"
                        v-model="form.gameno"
                        type="text"
                        required
                        placeholder="123456"
                        ></b-form-input>
                    </b-form-group>
                    <b-form-group
                        id="input-group-4"
                        label="Date/Time:"
                        label-for="input-4">
                        <b-form-row>
                            <b-col><b-form-datepicker v-model="form.date" class="mb-2" required></b-form-datepicker></b-col>
                            <b-col><b-form-timepicker v-model="form.time" class="mb-2" required></b-form-timepicker></b-col>
                        </b-form-row>
                    </b-form-group>
                    <b-button type="submit" variant="primary">Submit</b-button>
                    <b-button type="reset" variant="danger">Reset</b-button>
                </b-form>
            </b-card-text>
        </b-card>
        <b-modal id="modal-preview" title="Data correct?" hide-footer>
            <h4 class="text-success">Game #{{ this.form.gameno }} - {{ this.game_type }}</h4>
            <h5 class="text-info">{{ new Date(this.form.date + " "  + this.form.time).toLocaleString()}}</h5>
            <b-table striped hover :items="gameOverview" :fields="fields">
                <template #cell(html)="data">
                    <span v-html="data.value"></span>
                </template>
            </b-table>
            <b-row class="mt-3">
                <b-col><b-button variant="outline-success" block @click="onSubmit">
                    Ok - Upload!&nbsp;<b-icon-hand-thumbs-up></b-icon-hand-thumbs-up>
                </b-button></b-col>
                <b-col><b-button variant="outline-secondary" block @click="hideModal">
                    Cancel
                </b-button></b-col>
            </b-row>
        </b-modal>
    </div>
</template>
<script>
export default {
    props: ['last'],
    data() {
        return {
            form: {
                loglink: '',
                gametype: 1,
                gameno: null,
                preview: true,
                date: new Date().toISOString().slice(0, 10),
                time: "22:00",
            },
            game_type: '',
            types: [{ text: 'Step 1', value: 1 }, { text: 'Step 2', value: 2 }, { text: 'Step 3', value: 3 }, { text: 'Step 4', value: 4 }],
            game: null,
            show: true,
            fields: ['Pos', 'Player', 'Hand', { key: 'html', label: 'Eliminated by/Wins with' }],
            arole: window.arole,
        }
    },
    computed: {
        gameOverview: function () {
            if(this.game === null) return null
            let overview = []
            for(let i=0; i<this.game[0].length; i++){
                overview.push(
                    {
                        "Pos": this.game[2][i],
                        "Player": this.game[1][i],
                        "Hand": this.game[3][i],
                        html: this.game[7][i][0]
                    }
                )
            }
            return overview
        }
    },
    mounted() {
        if(!isNaN(this.last)) this.form.gameno = this.last
        
    },
    created() {
        // this.$root.$on('bv::toast:hidden', bvEvent => {
        //     if(bvEvent.vueTarget.variant === 'success'){
        //         window.location.href = window.location.origin + '/results/game/' + this.form.gameno
        //     }
        // });
    },
    methods: {
        onSubmit(evt) {
            evt.preventDefault()
            this.$bvModal.hide('modal-preview')
            axios({
                method: 'post',
                url: '/upload/game',
                data: this.form,
                headers: {'Content-Type': 'application/json' }
                })
                .then(response => {
                    if(response.data.status){
                        
                        if(this.form.preview){
                            this.$bvModal.show('modal-preview')
                            this.game = response.data.msg
                            this.types.map(type => {
                                    if(type.value == this.form.gametype) this.game_type = type.text
                                }
                            )
                        }else{
                            // this.game = null;
                            this.$bvModal.hide('modal-preview')
                            this.$bvToast.toast(`Game succesfully uploaded!`, {
                                title: 'Game uploaded!',
                                autoHideDelay: 1500,
                                appendToast: true,
                                variant: 'success',
                            })
                            window.setTimeout(() => {window.location.href = window.location.origin + '/results/game/' + this.form.gameno}, 1500)
                        } 
                        this.form.preview = !this.form.preview                      
                    }else{
                        this.$bvToast.toast(response.data.msg, {
                            title: 'Game upload failed!',
                            autoHideDelay: 5000,
                            appendToast: true,
                            variant: 'danger',
                        })
                        this.form.loglink = ''
                        this.form.gametype = 1
                        this.form.gameno = null
                        this.form.preview = true
                        this.game = null
                    }
                })
                .catch(response => {
                    this.game = null
                    this.form.preview = true
                    this.$bvToast.toast(response, {
                        title: 'Game upload failed!',
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'danger',
                    })
                });

        },
        onReset(evt) {
            evt.preventDefault()
            // Reset our form values
            this.form.loglink = ''
            this.form.gametype = 1
            this.form.gameno = null
            this.form.preview = true
            this.game = null
            // Trick to reset/clear native browser form validation state
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
        hideModal() {
            this.$bvModal.hide('modal-preview')
            this.form.preview = true
        },
    }
}
</script>
<style lang="scss" scoped>

</style>