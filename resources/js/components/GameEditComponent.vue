<template>
    <div>
        <b-card title="Edit Game" class="mb-2 mt-2">
            <b-card-text>
                <div class="row">
                    <div class="col">
                        <div class="row">
                            <div class="col-7">
                                <b-form-group
                                    id="input-group-1"
                                    label="Game-Number:"
                                    label-for="input-1">
                                    <b-form-input
                                    id="input-1"
                                    v-model="form.gameno"
                                    type="text"
                                    required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group id="input-group-2" label="Game Type:" label-for="input-2">
                                    <b-form-select
                                    id="input-2"
                                    v-model="form.gametype"
                                    required>
                                    <option v-for="type in types" 
                                        :key="type.value"
                                        :value="type.value"
                                    >{{ type.text }}</option>
                                    </b-form-select>
                                </b-form-group>
                                <b-form-group
                                    id="input-group-3"
                                    label="Date/Time:"
                                    label-for="input-3">
                                    <b-form-row>
                                        <b-col><b-form-datepicker v-model="form.date" class="mb-2" required></b-form-datepicker></b-col>
                                        <b-col><b-form-timepicker v-model="form.time" class="mb-2" required></b-form-timepicker></b-col>
                                    </b-form-row>
                                </b-form-group>
                            </div>
                            <div class="col">
                                <b-form-group
                                    id="input-group-4"
                                    label="Player:"
                                    label-for="input-4">
                                    <div class="row" v-for="(mPlayer,index) in this.form.player" v-bind:key="index">
                                        <div class="col-2 font-weight-bold mt-1 mr-2">{{ (index+1).toString() }}.</div>
                                        <div class="col-8">
                                            <b-form-input
                                            type="text"
                                            required
                                            v-model="form.player[index]"
                                            class="mb-1"
                                            size="sm"
                                            :disabled="form.disco_dummy[index] === 1"
                                            ></b-form-input>
                                        </div>
                                        <div class="col-1 float-right" v-b-tooltip.hover title="Disco Dummy">
                                            <b-form-checkbox
                                            v-model="form.disco_dummy[index]"
                                            value="1"
                                            unchecked-value="0"
                                            class="mt-1"
                                            @change="discoDummy"
                                            >
                                            </b-form-checkbox>
                                        </div>
                                    </div>
                                </b-form-group>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <b-button
                                    variant="primary"
                                    class="float-left"
                                    @click="$emit('back')"
                                >Cancel</b-button>
                            </div>
                            <div class="col float-right">
                                <b-button
                                    variant="success"
                                    class="float-right mr-2"
                                    @click="saveGame"
                                    title="Save"
                                >Save</b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </b-card-text>
        </b-card>
    </div>
</template>
<script>
export default {
    props: ['game'],
    data () {
        return {
            types: [{ text: 'Regular', value: 1 }, { text: 'Monthly', value: 5 }, { text: 'Yearly', value: 6 }],
            game_edited: null,
            form: {
                gametype: this.game.type,
                gameno: this.game.number,
                date: this.game.started.slice(0, 10),
                time: this.game.started.slice(11),
                disco_dummy: [],
                player: []
            },
        }
    },
    methods:{
        init(){ 
            for(let i=1;i<=10;i++){
                let player = this.game['pos'+i]
                if(player === 'disco_dummy'){
                    this.form.disco_dummy.push(1)
                }else{
                    this.form.disco_dummy.push(0)
                }
                this.form.player.push(player)
            }
        },
        discoDummy(val) {
            let player = window.event.currentTarget.parentNode.parentNode.parentNode.querySelector('input[type=text]')
            let original =  this.game.stats.player_list[1][parseInt(window.event.currentTarget.parentNode.parentNode.parentNode.querySelector('.font-weight-bold').innerText.replace('.', ''))-1]
            if(val == 0){
                if(typeof original === 'undefined') original = ''
                player.value = original
                window.event.currentTarget.parentNode.parentNode.parentNode.querySelector('input[type=text]').disabled = false
            } 
            else{
                window.event.currentTarget.parentNode.parentNode.parentNode.querySelector('input[type=text]').disabled = true
                window.event.currentTarget.parentNode.parentNode.parentNode.querySelector('input[type=text]').value = 'disco_dummy'
            }
            player.dispatchEvent(new Event('change'))
        },
        saveGame(){
            axios({
                method: 'post',
                url: '/update/game/' + this.game.number,
                data: this.form,
                headers: {'Content-Type': 'multipart/form-data' }
                })
                .then(response => {
                    if(response.data.status){
                        this.$bvToast.toast(response.data.msg, {
                            title: 'Success!',
                            autoHideDelay: 5000,
                            appendToast: true,
                            variant: 'success',
                        })
                        $(this.$emit('update'))
                        $(this.$emit('back'))
                    }else{
                        this.$bvToast.toast(response.data.msg, {
                            title: 'Game saving failed!',
                            autoHideDelay: 5000,
                            appendToast: true,
                            variant: 'danger',
                        })
                    }
                })
                .catch(response => {
                    this.$bvToast.toast(response, {
                        title: 'Game saving failed!',
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'danger',
                    })
                });
        }
    },
    created(){
        this.init()
    },
}
</script>
<style lang="scss" scoped>
    label{
        font-size: 24px;
        font-weight: bold;
    }
</style>