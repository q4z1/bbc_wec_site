<template>
    <div>
        <b-card
            title="Game Upload"
            class="mb-2"
        >
            <b-card-text>
                <b-form @submit="onSubmit" @reset="onReset" v-if="show">

                    <b-form-group id="input-group-1" label="Game Type:" label-for="input-1">
                        <b-form-select
                        id="input-1"
                        v-model="form.gametype"
                        :options="types"
                        required
                        ></b-form-select>
                    </b-form-group>

                    <b-form-group
                        id="input-group-2"
                        label="Log-URL:"
                        label-for="input-2"
                    >
                        <b-form-input
                        id="input-2"
                        v-model="form.loglink"
                        type="text"
                        required
                        placeholder="https://www.pokerth.net/log-file-analysis/?ID=1234567890abcdef&UniqueGameID=1"
                        ></b-form-input>
                    </b-form-group>

                    <b-button type="submit" variant="primary">Submit</b-button>
                    <b-button type="reset" variant="danger">Reset</b-button>
                </b-form>
                <b-card class="mt-3" header="Form Data">
                    <pre class="m-0">{{ form }}</pre>
                </b-card>
                <b-card class="mt-3" header="Result">
                    <pre class="m-0">{{ game }}</pre>
                </b-card>
            </b-card-text>
        </b-card>

    </div>
</template>
<script>
export default {
    data() {
        return {
            form: {
                loglink: '',
                gametype: null,
                preview: true
            },
            types: [{ text: 'Select One', value: null }, 'Regular', 'Monthly', 'Yearly'],
            game: null,
            show: true
        }
    },
    methods: {
        onSubmit(evt) {
            evt.preventDefault()
            // alert(JSON.stringify(this.form))
            axios({
                method: 'post',
                url: '/upload/game',
                data: this.form,
                headers: {'Content-Type': 'multipart/form-data' }
                })
                .then(response => {
                    //handle 
                    if(response.data.status){
                        this.game = response.data.msg
                        this.form.preview = !this.form.preview
                    }
                })
                .catch(response => {
                    //handle error
                    this.game = null
                    this.form.preview = true
                    console.log(response)
                });

        },
        onReset(evt) {
            evt.preventDefault()
            // Reset our form values
            this.form.loglink = ''
            this.form.gametype = null
            this.form.preview = true
            this.game = null
            // Trick to reset/clear native browser form validation state
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        }
    }
}
</script>