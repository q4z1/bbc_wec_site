<template>
    <div class="awards">
        <b-row>
            <b-col><h3>Awards</h3></b-col>
            <b-col class="text-right h3 text-primary"><b-icon-plus-circle v-b-modal.modal-upload role="button"></b-icon-plus-circle></b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-table responsive :fields="fields" :items="award_items">
                    <template #cell(award)="data">
                        <img class="img-col" :src="data.value" />
                    </template>
                    <template #cell(actions)="row">
                        <b-row>
                            <b-col class="text-warning h5"><b-icon-pencil-fill class="actions" role="button" @click="editAward(row.item, row.index, $event.target)"></b-icon-pencil-fill></b-col>
                            <b-col class="text-primary h5"><b-icon-people-fill class="actions" role="button" @click="assignAward(row.item, row.index, $event.target)"></b-icon-people-fill></b-col>
                            <b-col class="text-danger h5"><b-icon-trash-fill class="actions" role="button" @click="deleteAward(row.item, row.index, $event.target)"></b-icon-trash-fill></b-col>
                        </b-row>
                    </template>
                </b-table>
            </b-col>
        </b-row>
        <b-modal id="modal-upload" title="Upload Award"  hide-footer>
            <award-upload-component @update-awards="updateAwards"></award-upload-component>
        </b-modal>
        <b-modal id="modal-assign" title="Assign Award"  hide-footer>
            <award-assign-component :award="award" :players="players" @update-awards="updateAwards"></award-assign-component>
        </b-modal>
        <b-modal id="modal-edit" title="Edit Award"  hide-footer>
            <award-edit-component :award="award" @update-awards="updateAwards"></award-edit-component>
        </b-modal>
        <b-modal id="modal-delete" title="Delete Award" ok-title="Yes" cancel-title="No" @ok="handleYes">
            <b-row v-if="award">
                <b-col class="text-center"><img :src="award.award" :alt="award.title" class="img-col"></b-col>
            </b-row>
            <b-row v-if="award">
                <b-col class="text-center">{{ award.title }}</b-col>
            </b-row>
            <hr />
            <b-row>
                <b-col class="text-center"><strong class="text-danger">Are you sure to delete this award?</strong> </b-col>
            </b-row>
        </b-modal>
    </div>
</template>
<script>
import AwardAssignComponent from './AwardAssignComponent.vue'
import AwardEditComponent from './AwardEditComponent.vue'
import AwardUploadComponent from './AwardUploadComponent.vue'
export default {
    components: { AwardUploadComponent, AwardAssignComponent, AwardEditComponent },
    props: ['awards'],
    data() {
        return {
            award_items: null,
            fields: [
                { key: 'id', sortable: true, class: 'col-id' },
                { key: 'award', class: 'col-award' },
                { key: 'title', sortable: true },
                { key: 'actions', class: 'col-actions' }
            ],
            award: null,
            players: null,
        }
    },
    mounted() {
        this.updateAwards(this.awards)
        this.fetchPlayers()
    },
    methods: {
        fetchPlayers(){
            axios.get('/players/list')
            .then(response => {
                this.players = response.data
            }, (error) => {
                console.log(error)
            });
        },
        updateAwards(awards){
            this.award_items = []
            for(let i=0;i<awards.length;i++){
                let item = awards[i]
                this.award_items.push(
                    {
                        id: item.id,
                        award: item.filename, 
                        title: item.title
                    }
                )
            }
        },
        editAward(item, index, target){
            this.award = item
            this.$bvModal.show('modal-edit')
        },
        assignAward(item, index, target){
            this.award = item
            this.$bvModal.show('modal-assign')
        },
        deleteAward(item, index, target){
            this.award = item
            this.$bvModal.show('modal-delete')
        },
        handleYes(){
            axios.get('/awards/delete/' + this.award.id)
            .then(response => {
                if(response.data.success === true){
                    this.updateAwards(response.data.awards)
                }else{
                    console.log(response.data)
                }
            }, (error) => {
                console.log(error)
            });
        }
    }
}
</script>
<style lang="scss" scoped>
img.img-col{
    width: 175px;
}
</style>
<style lang="scss">
.awards {
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
    .col-id{
        width: 75px!important;
    }
    .col-award{
        width: 190px;
    }
    .col-actions{
        width: 150px;
    }
}
</style>