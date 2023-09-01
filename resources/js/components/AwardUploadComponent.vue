<template>
    <div class="award-upload">
        <b-card class="mb-2">
            <b-card-text>
                <div class="preview-area mb-2" v-if="preview"><img id="preview" src="#" :alt="title" class="img-fluid mx-auto d-block" /></div>
                <b-form @submit="doUpload" @reset="doReset" v-if="show">
                    <b-form-group id="input-group-1" label="Image File:" label-for="input-1">
                        <b-form-file
                            accept="image/jpeg, image/png, image/gif"
                            v-model="award_file"
                            :state="Boolean(award_file)"
                            placeholder="Choose an image-file or drop it here..."
                            drop-placeholder="Drop image-file here..."
                            @input="genPreview"
                            ></b-form-file>
                    </b-form-group>
                    <b-form-group id="input-group-2" label="Title:" label-for="input-2">
                        <b-form-input v-model="title"></b-form-input>
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
            title: null,
            preview: false,
        }
    },
    mounted() {

    },
    methods: {
        doUpload(evt) {
            evt.preventDefault()
            evt.stopPropagation()
            let data = new FormData();
            data.append('title', this.title);
            data.append('award', this.award_file);
            axios.post('/awards/upload', data)
            .then(response => {
                if(response.data.success === true){
                    this.$emit('update-awards', response.data.awards)
                    this.doReset()
                    this.$root.$emit('bv::hide::modal', 'modal-upload')
                }else{
                    console.log(response.data)
                }
            }, (error) => {
                console.log(error)
            });
        },
        doReset(evt) {
            this.award_file = this.preview = this.title = null

            // Trick to reset/clear native browser form validation state
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
        genPreview(af){
            if(this.award_file !== null){
                this.preview = true
                var reader = new FileReader();
                reader.onload = e => {
                    $('#preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(this.award_file);
            }else{
                this.preview = false;
            }
        },
    }
}
</script>
<style scoped>

</style>