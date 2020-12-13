<template>
    <div>
        <h3>Hand Cash</h3>
        <line-chart-component :chart-data="datacollection1"></line-chart-component>
        <h3>Pot Size</h3>
        <bar-chart-component :chart-data="datacollection2" :options="options2"></bar-chart-component>
    </div>
</template>
<script>
export default {
    props: ['game'],
    data () {
        return {
            datacollection1: null
        }
    },
    methods:{
        init(){
            let colors = [
                'rgba(86, 226, 137, 1.0)',
                'rgba(104, 226, 86, 1.0)',
                'rgba(174, 226, 86, 1.0)',
                'rgba(226, 297, 86, 1.0)',
                'rgba(226, 137, 86, 1.0)',
                'rgba(226, 84, 104, 1.0)',
                'rgba(226, 86, 174, 1.0)',
                'rgba(207, 86, 226, 1.0)',
                'rgba(138, 86, 226, 1.0)',
                'rgba(86, 104, 226, 1.0)'
            ]
            // hand cash
            let labels1 = []
            for(let i=1;i<=this.game.stats.hand_cash[0].length;i++){
                labels1.push(i);
            }
            let datasets1 = []
            for(let i=0;i<this.game.stats.player_list[0].length;i++){
                let data = []
                for(let j=0;j<=this.game.stats.hand_cash[i].length;j++){
                    data.push(Number(this.game.stats.hand_cash[i][j]));
                }
                let set = {
                    label: this.game.stats.player_list[1][i],
                    borderColor: colors[i],
                    data: data
                }
                datasets1.push(set);
            }
            console.log(datasets1)
            this.datacollection1 = {
                labels: labels1,
                datasets: datasets1
            }
            // pot size
            let datasets2 = []
            for(let i=0;i<this.game.stats.pot_size[0].length;i++){
                let data = []
                data.push(100000 - Number(this.game.stats.pot_size[0][i]));
                let set = {
                    label: this.game.stats.pot_size[0][i],
                    borderColor: colors[i],
                    data: data
                }
                datasets2.push(set);
            }
            this.datacollection2 = {
                // labels: labels1,
                datasets: datasets2
            }
            this.options2 = {
                legend: {
                    display: false
                },
            }
/*
		$total_start_cash = get_total_start_cash($db,$_GET['UniqueGameID']);
    
        //die("<pre>".var_export($total_start_cash,true)."</pre>");
        
            if($total_start_cash == 0) $total_start_cash = 100000;
            
            $blind_steps = get_blind_steps($db,$_GET['UniqueGameID']);
            $blind_steps[0][2] = 0;
            $blind_steps[] = array($blind_steps[count($blind_steps)-1][0],$blind_steps[count($blind_steps)-1][1],end($pot_size[1]));
    
            if(isset($_GET['width']) && is_numeric($_GET['width']) && $_GET['width'] > 0) {
                $width = $_GET['width'];
            } else {
                $width = 500;
            }
        

        $pot_size_new = array();
        foreach($pot_size[0] as $i => $size ){
        if($i > 0){
            $pot_size_new[$i] = 100000 - $size;
        }else{
            $pot_size_new[$i] = $size;
        }
        }
        //Some data

        //Create the graph. These two calls are always required
        $graph = new Graph($width,240);
        $graph->SetScale('linlin');
        $graph->SetMargin(70,25,15,40);
        $graph->xaxis->title->Set('Hand');
        $graph->xaxis->title->SetMargin(-5);
        $graph->yaxis->title->Set('Pot Size $');
        $graph->yaxis->title->SetMargin(25);
        //$graph->xscale->SetGrace(100);
        //$graph->xscale->ticks->Set(10,count($pot_size_new));
        //Create the linear plot
        $bar1 = new BarPlot($pot_size_new);
        $bar1->SetColor('green');
        
        //$bar1->SetMargin(15);

        //Add the plot to the graph
        $graph->Add($bar1);

        //Display the graph
        $graph->Stroke();
*/        
        }
    },
    mounted(){
        this.init()
    },
    computed: {
        // a computed getter
        handCash: function () {
            return ""
        }
    }
}
</script>
<style lang="sass" scoped>

</style>