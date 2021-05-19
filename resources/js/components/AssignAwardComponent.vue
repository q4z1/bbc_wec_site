<template>
    <div>
        <b-card title="Assign Awards" class="mb-2">
            <b-card-text>
                <b-form @submit="doAssign" @reset="doReset" v-if="show">
                    <b-form-group id="input-group-1" label="Award:" label-for="input-1">

                    </b-form-group>
                    <b-form-group id="input-group-2" label="Player:" label-for="input-2">

                    </b-form-group>
                    <b-button type="submit" variant="primary">Assign</b-button>
                    <b-button type="reset" variant="danger">Reset</b-button>
                </b-form>
            </b-card-text>
        </b-card>
    </div>
</template>
<script>
export default {
    props: ['awards', 'players'],
    data() {
        return {
            show: true,
        }
    },
    mounted() {
        
    },
    methods: {
        doAssign(evt) {
            evt.preventDefault()
            console.log('doAssign')
        },
        doReset(evt) {
            evt.preventDefault()
            console.log('doReset')

            // Trick to reset/clear native browser form validation state
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        }
    }
}
</script>
<style scoped>

</style>