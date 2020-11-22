<template>
    <div class="v-result">
        <b-card title="Spiel Blah" sub-title="vom 22.11.2020">
            <b-card-text>
            Some quick example text to build on the <em>card title</em> and make up the bulk of the card's
            content.
            </b-card-text>

            <b-card-text>A second paragraph of text in the card.</b-card-text>

            <a href="#" class="card-link">Card link</a>
            <b-link href="#" class="card-link">Another link</b-link>
        </b-card>
    </div>
</template>
<script>
export default {
    props: ['gamedata', 'handcash'],
    data: function() {
        return {
            blah: false,
        };
    },
    computed: {
    },
    mounted() {
        console.log(this.gamedata)
    },
    methods: {
        randomPic: function(gender){
            return 'https://randomuser.me/api/portraits/' + ((gender == 1) ? 'men' : 'women') + '/' + Math.round(Math.random() * 87) + '.jpg';
        },
    }
}
</script>
<style lang="scss" scoped>
    $warning: #f47c3c;
    $success: #93c54b;
    $danger: #ac2925;
    $primary: #325d88;

    .v-result{

    }
</style>