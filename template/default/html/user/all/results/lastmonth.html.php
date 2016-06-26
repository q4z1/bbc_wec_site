<?php
/**
 * template_default_html_user_all_results_lastmonth:
 *
 */

$last_month = date("m")-1;
if(intval($last_month) < 1){
 $last_month = "01";
}
$last_month_long = date("F", strtotime(date("Y-$last_month-d")));
$last_month = intval($last_month);

$stdgs = app::$content["standings"];

?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary"><?=$last_month_long?> Cup Table Results</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th>Series <?=date("Y")?> Table</th>
          <th>Player</th>
          <th>Place</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
        <!-- 1st round -->
        <?php if(count($stdgs) > 0): ?>
        <?php for($i=1;$i<=10;$i++): ?>
        <?php foreach($stdgs as $table): ?>
        <?php if($table->type != "firstround" && $table->month != intval($last_month)): ?>
        <?php continue; ?>
        <?php elseif(intval($table->table_) == $i): ?>
        <tr>
          <td><?=$last_month_long?> Cup T<?=$i?></td>
          <td><?=$table->playername?></td>
          <td><?=$table->position?></td>
          <td><?=$table->points?></td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <?php endfor; ?>
        <!-- gold table -->
        <?php foreach($stdgs as $table): ?>
        <?php if($table->type == "final" && $table->month == intval($last_month) && $table->table_ ==  "gold"): ?>
        <tr class="success">
          <td><?=$last_month_long?> Gold</td>
          <td><?=$table->playername?></td>
          <td><?=$table->position?></td>
          <td><?=$table->points?></td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <!-- silver table -->
        <?php foreach($stdgs as $table): ?>
        <?php if($table->type == "final" && $table->month == intval($last_month) && $table->table_ == "silver"): ?>
        <tr class="danger">
          <td><?=$last_month_long?> Silver</td>
          <td><?=$table->playername?></td>
          <td><?=$table->position?></td>
          <td><?=$table->points?></td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <!-- bronce table -->
        <?php foreach($stdgs as $table): ?>
        <?php if($table->type == "final" && $table->month == intval($last_month) && $table->table_ == "bronze"): ?>
        <tr class="warning">
          <td><?=$last_month_long?> Bronze</td>
          <td><?=$table->playername?></td>
          <td><?=$table->position?></td>
          <td><?=$table->points?></td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
          <td colspan="4" class="text-center text-warning">No results found!</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
