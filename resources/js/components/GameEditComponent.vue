<template>
    <div>
        Game Edit Component
    </div>
</template>
<script>
export default {
    props: ['game'],
    data () {
        return {
            game_edited: null,
        }
    },
    methods:{
        init(){ }
    },
    mounted(){
        this.init()
    },
}
</script>
<style lang="sass" scoped>

</style>