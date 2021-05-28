<template>
    <div class="award-upload">
        <b-card class="mb-2">
            <b-card-text>
                <div class="preview-area mb-2 text-center" v-if="award">
                    <b-row>
                        <b-col class="img">
                            <span class="h4 text-warning" v-if="!replace"><b-icon-pencil-fill class="actions" role="button" @click="replaceAward"></b-icon-pencil-fill></span>
                            <img id="preview" :src="award_preview" :alt="award.title" class="img-fluid mx-auto d-block" />
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col class="text-primary mt-2">{{ title }}</b-col>
                    </b-row>
                </div>
                <hr />
                <b-form @submit="doUpload" @reset="doReset" v-if="show">
                    <b-form-group id="input-group-1" label="Image File:" label-for="input-1" v-if="replace">
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
                    <b-button type="submit" variant="primary">Submit</b-button>
                    <b-button @click="closeModal" variant="danger">Cancel</b-button>
                </b-form>
            </b-card-text>
        </b-card>
    </div>
</template>
<script>
export default {
    props: ['award'],
    data() {
        return {
            show: true,
            award_file: null,
            title: null,
            preview: false,
            award_preview: null,
            replace: false,
        }
    },
    mounted() {
        this.title = this.award.title
        this.award_preview = this.award.award
    },
    methods: {
        doUpload(evt) {
            evt.preventDefault()
            evt.stopPropagation()
            let data = new FormData()
            data.append('title', this.title)
            data.append('award', this.award_file)
            axios.post('/awards/edit/' + this.award.id, data)
            .then(response => {
                if(response.data.success === true){
                    this.$emit('update-awards', response.data.awards)
                    this.doReset()
                    this.$root.$emit('bv::hide::modal', 'modal-edit')
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
                    this.award_preview = e.target.result;
                };
                reader.readAsDataURL(this.award_file);
            }else{
                this.preview = false;
            }
        },
        replaceAward(){
            this.replace = true
        },
        closeModal(){
            this.$root.$emit('bv::hide::modal', 'modal-edit')
        },
    }
}
</script>
<style lang="scss" scoped>
.img {
    position: relative;
    display: inline-block; /* <= shrinks container to image size */
    transition: transform 150ms ease-in-out;
    margin: 0 auto;
}

.img img { /* <= optional, for responsiveness */
    display: block;
    max-width: 100%;
    height: auto;
}

.img svg {
    position: absolute;
    bottom: 10px;
    right: 15px;
    filter: brightness(80%);
}

.img svg:hover {
    filter: brightness(100%);
}
</style>