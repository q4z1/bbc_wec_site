<?php
/**
 * template_default_html_user_all_results_game:
 *
 */
$error = app::$content['error'];
$game = app::$content['game'];
$types = array(
  "s1" => "Step 1",
  "s2" => "Step 2",
  "s3" => "Step 3",
  "s4" => "Step 4",
  "husc" => "HUSC"
);
if(!is_null($game)){
  $data = unserialize($game->log);  
}

// hand cash stuff
$hand_cash = $data['hand_cash'];
$player_list = $data['player_list'];
$total_start_cash = 0;
for($i=0;$i<count($hand_cash)-1;$i++) {
  if(count($hand_cash[$i])>0) $total_start_cash+=max(0,$hand_cash[$i][0]);
}
//die("<pre>".var_export($hand_cash,true)."</pre>");
$winner_found = false;
for($i=0;$i<count($player_list[0]);$i++) {
  for($j=0;$j<count($hand_cash[$i]);$j++) {
    if($j==count($hand_cash[$i])-1 & $hand_cash[$i][$j] > 0) $winner_found = true;
    // if($hand_cash[$i][$j] < 0) $hand_cash[$i][$j] = VOID;
  }
}
if(!$winner_found) {
  for($i=0;$i<count($player_list[0]);$i++) {
    array_pop($hand_cash[$i]);
  }
}

?>

<div id="v-result">
  <result-game :gamedata='<?=strip_tags(json_encode($data))?>' :handcash='<?=json_encode($hand_cash)?>'></result-game>
</div>

<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary">Game result:</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <hr />
  </div>
</div>
<?php if(!is_null($error)): ?>
<div class="row">
  <div class="col-md-6 col-md-offset-3 text-center">
    <h4 class="text-danger">No game ID given!</h4>
  </div>
</div>
<?php elseif(is_null($game)): ?>
<div class="row">
  <div class="col-md-6 col-md-offset-3 text-center">
    <h4 class="text-danger">Game not found!</h4>
  </div>
</div>
<?php else: ?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="row">
      <strong>
        <div class="col-md-4">
          Game: <span class="text-warning">#<?=$game->results_id?></span>
        </div>
        <div class="col-md-4">
          Type: <span class="text-warning"><?=$types[$game->type]?></span>
        </div>
        <div class="col-md-4">
          Date: <span class="text-warning"><?=$game->date?> CET</span>
        </div>
      </strong>
    </div>
    <table class="table table-hover table-bordered table-striped">
      <tbody>
        <tr>
          <td colspan="2">
            <h4 class="text-success">Ranking:</h4>
            <table class="table table-hover table-bordered table-striped">
              <thead>
                <tr>
                  <th>Pos.</th>
                  <th>Player</th>
                  <th>Hand</th>
                  <th>eliminated by / wins with</th>
                </tr>
              </thead>
              <?php for($i=1;$i<=count($data['result']);$i++): ?>
              <tr>
                <td><?=$i?>.</td>
                <td><?=$data['result'][$i]['player']?></td>
                <td><?=$data['result'][$i]['hand']?></td>
                <td><?=$data['player_list'][7][$i-1][0]?></td>
              </tr>
              <?php endfor; ?>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <h4 class="text-success">Course of game:</h4>
            <div class="row">
              <div class="col-md-12">
                <strong>Hand Cash:</strong><br />
                <img class="img-responsive" src="data:image/png;base64,<?=$data['pics']['hand_cash']?>" alt="Hand Cash" /> 
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <hr />
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <strong>Pot Size:</strong><br />
                <img class="img-responsive" src="data:image/png;base64,<?=$data['pics']['pot_size']?>" alt="Hand Cash" /> 
              </div>
            </div>
          </td>
        </tr>
        <!--
        <tr>
          <td class="col-md-6">
            <h4 class="text-success">Most hands played:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Player</th>
                  <th>Count</th>
                  <th>10 to 7</th>
                  <th>6 to 4</th>
                  <th>3 to 1</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['most hands played'][$i]['player']?></td>
                  <td><?=$data['most hands played'][$i]['count']?></td>
                  <td><?=$data['most hands played'][$i]['10 to 7']?></td>
                  <td><?=$data['most hands played'][$i]['6 to 4']?></td>
                  <td><?=$data['most hands played'][$i]['3 to 1']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
          <td class="col-md-6">
            <h4 class="text-success">Best hands:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Cards</th>
                  <th>Player</th>
                  <th>Hands</th>
                  <th>Result</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['best hands'][$i]['cards']?></td>
                  <td><?=$data['best hands'][$i]['player']?></td>
                  <td><?=$data['best hands'][$i]['hand']?></td>
                  <td><?=$data['best hands'][$i]['result']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td class="col-md-6">
            <h4 class="text-success">Most wins:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Player</th>
                  <th>Count</th>
                  <th>Highest</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['most wins'][$i]['player']?></td>
                  <td><?=$data['most wins'][$i]['hand']?></td>
                  <td><?=$data['most wins'][$i]['amount']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
          <td class="col-md-6">
            <h4 class="text-success">Highest wins:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Amount</th>
                  <th>Player</th>
                  <th>Hand</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['highest wins'][$i]['amount']?></td>
                  <td><?=$data['highest wins'][$i]['player']?></td>
                  <td><?=$data['highest wins'][$i]['hand']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td class="col-md-6">
            <h4 class="text-success">Longest series of wins:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Duration</th>
                  <th>Player</th>
                  <th>Hands</th>
                  <th>Total gain</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['longest series of wins'][$i]['duration']?></td>
                  <td><?=$data['longest series of wins'][$i]['player']?></td>
                  <td><?=$data['longest series of wins'][$i]['hands']?></td>
                  <td><?=$data['longest series of wins'][$i]['amount']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
          <td class="col-md-6">
            <h4 class="text-success">Longest series of lossess:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Duration</th>
                  <th>Player</th>
                  <th>Hands</th>
                  <th>Total loss</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['longest series of losses'][$i]['duration']?></td>
                  <td><?=$data['longest series of losses'][$i]['player']?></td>
                  <td><?=$data['longest series of losses'][$i]['hands']?></td>
                  <td><?=$data['longest series of losses'][$i]['amount']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td class="col-md-6">
            <h4 class="text-success">Most bet/raise:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Player</th>
                  <th>Count</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['most bet/raise'][$i]['player']?></td>
                  <td><?=$data['most bet/raise'][$i]['count']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
          <td class="col-md-6">
            <h4 class="text-success">Most all-in:</h4>
            <table class="table table-hover table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th>Player</th>
                  <th>Total count</th>
                  <th>In preflop</th>
                  <th>First 5 hands</th>
                  <th>Total won</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=10;$i++): ?>
                <tr>
                  <td><?=$i?>.</td>
                  <td><?=$data['most all in'][$i]['player']?></td>
                  <td><?=$data['most all in'][$i]['count']?></td>
                  <td><?=$data['most all in'][$i]['in preflop']?></td>
                  <td><?=$data['most all in'][$i]['first 5 hands']?></td>
                  <td><?=$data['most all in'][$i]['total wons']?></td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
        </tr>
        -->
      </tbody>
    </table>
    <!--
    <pre>
      <?=var_export($game, true); ?>
    </pre>
    -->
  </div>
</div>
<?php endif; ?>