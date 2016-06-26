<?php
/**
 * template_default_html_user_all_results_points:
 *
 */

$points = app::$content['points'];
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary">Cup Ranking Points <?=date("Y")?></h3>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h4 class="text-success">Calculation of Points</h3>
  </div>
</div>
  <div class="col-md-10 col-md-offset-1 text-center">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th class="text-center">Place</th>
          <th class="text-center">1st Round</th>
          <th class="text-center">Bronze</th>
          <th class="text-center">Silver</th>
          <th class="text-center">Gold</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($points as $pos => $column): ?>
        <tr>
          <td><?=$pos?></td>
          <td><?=(intval($column['first'])-1)?></td>
          <td><?=$column['bronze']?></td>
          <td><?=$column['silver']?></td>
          <td><?=$column['gold']?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h4 class="text-warning">Score</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <p>1 point for participation + Round 1 points + Final Round points</p>
  </div>
</div>
