<template>
    <div>
        <h5>{{ player.nickname }}</h5>
    </div>
</template>
<script>
    export default {
        props: ['player'],
        data() {
            return {
                ePlayer: false,
                types: [{ text: 'Regular', value: 1 }, { text: 'Monthly', value: 5 }, { text: 'Yearly', value: 6 }],
            }
        },
        computed: {
            init: function(){

            },
        },
        mounted() {
        }
    }
</script>
<style lang="scss" scoped>

</style>