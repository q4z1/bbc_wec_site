<template>
    <div>
        <b-card title="Award Upload" class="mb-2">
            <b-card-text>
                <b-form @submit="doUpload" @reset="doReset" v-if="show">
                    <b-form-group id="input-group-1" label="Image File:" label-for="input-1">
                    <b-form-file
                        accept="image/jpeg, image/png, image/gif"
                        v-model="award_file"
                        :state="Boolean(award_file)"
                        placeholder="Choose an image-file or drop it here..."
                        drop-placeholder="Drop image-file here..."
                        ></b-form-file>
                        <div class="mt-3">Selected Award: {{ award_file ? award_file.name : '' }}</div>
                    </b-form-group>
                    <b-button type="submit" variant="primary">Upload</b-button>
                    <b-button type="reset" variant="danger">Reset</b-button>
                </b-form>
            </b-card-text>
        </b-card>
    </div>
</template>
<script>
export default {
    data() {
        return {
            show: true,
            award_file: null,
        }
    },
    mounted() {

    },
    methods: {
        doUpload(evt) {
            evt.preventDefault()
            console.log('doUpload', this.award_file)
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