<template>
    <div>
        <h3>Players</h3>
        <b-row class="mb-3">
            <b-col class="col-input">
                <b-input-group prepend="@" class="mb-2 mr-sm-2 mb-sm-0">
                    <b-form-input class="p-input" v-model="player" @input="searchPlayer" placeholder="Nickname"></b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-table striped hover 
                    v-if="result"
                    id="players_table"
                    :fields="fields"
                    :items="result"
                    sort-icon-left
                    @row-clicked="showPlayer"
                ></b-table>
                <p v-else class="mt-4">No players found.</p>
            </b-col>
        </b-row>
        <b-row>
            <b-col class="col-pag">
                <b-pagination
                    v-if="result"
                    v-model="page"
                    :total-rows="total"
                    :per-page="10"
                    aria-controls="results_table"
                    @page-click="paginate"
                ></b-pagination> 
            </b-col>
        </b-row>
    </div>
</template>
<script>
    export default {
        props: ['players', 'total'],
        components: {

        },
        data: function() { 
            return {
                result: null,
                page: 1,
                tot: 0,
                player: null,
                fields: [
                    { key: 'id', class: 'col-id' },
                    // { key: 'avatar', class: 'col-avatar' },
                    { key: 'nickname' },
                ],
            }
        },
        mounted() {
            this.result = this.formatResult(this.players)
            this.tot = this.total
        },
        methods: {
            formatResult(result){
                let results = result.map(entry => {
                    let newEntry = {
                        id: entry.id,
                        avatar: entry.avatar,
                        nickname: entry.nickname
                    }
                    return newEntry
                })
                return results
            },
            filter(){
                axios.post('/players', {
                    page: this.page,
                })
                .then(response => {
                    if(response.data.success === true){
                        this.result = this.formatResult(response.data.players)
                        this.tot = response.data.total
                    }
                }, (error) => {
                    console.log(error)
                });
            },
            searchPlayer(){
                if(this.player.length > 1){
                    axios.post('/players', {
                        page: 1,
                        nickname: this.player
                    })
                    .then(response => {
                        if(response.data.success === true){
                            this.result = this.formatResult(response.data.players)
                            this.tot = response.data.total
                            this.page = 1
                        }
                    }, (error) => {
                        console.log(error)
                    });
                }else{
                    this.result = this.formatResult(this.players)
                    this.tot = this.total
                    this.page = 1
                }
            },
            paginate(bvEvt, page){
                bvEvt.preventDefault()
                this.page = page
                this.filter()
            },
            showPlayer(item, index, event){
                window.open(window.location.origin + '/player/' + encodeURIComponent(item.nickname), '_blank')
            },
        }
    }
</script>
<style lang="scss" scoped>

</style>
<style lang="scss">
[role=row]{
    cursor: pointer!important;
}
.col-id{
    width: 75px!important;
}
.col-avatar{
    width: 190px;
}
.col-pag{
    max-width: 370px;
}
.col-input{
    max-width: 175px;
}

</style>
