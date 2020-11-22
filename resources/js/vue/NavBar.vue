<template>
    <div class="v-navbar">
        <b-navbar toggleable="lg" type="dark" variant="primary">
            <b-navbar-brand href="#">&nbsp;</b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
            <b-navbar-nav>
                <b-nav-item href="/">Home</b-nav-item>
                <b-nav-item href="/main/results/game/1">Dummy Result</b-nav-item>
                <b-nav-item href="/main/shoutbox/">Shoutbox</b-nav-item>
            </b-navbar-nav>

            <!-- Right aligned nav items -->
            <b-navbar-nav class="ml-auto">
                <b-nav-item-dropdown right v-if="stype=='admin'">
                <!-- Using 'button-content' slot -->
                <template #button-content>
                    <em>User</em>
                </template>
                <b-dropdown-item href="#">Sign Out</b-dropdown-item>
                </b-nav-item-dropdown>
                <b-nav-item v-else href="/main/login/">Login</b-nav-item>
            </b-navbar-nav>
            </b-collapse>
        </b-navbar>
    </div>
</template>
<script>
export default {
    props: ['stype'],
    data: function() {
        return {
            blah: false,
        };
    },
    computed: {
    },
    mounted() {
        console.log(this.stype)
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

    .v-navbar{

    }
</style>