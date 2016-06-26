<?php
/**
 * template_default_html_user_all_results_results:
 *
 */

$top3 = app::$content["top3"];

$cup_dates = json_decode(app::$settings["dates"]);
$forum_links = json_decode(app::$settings["forum_links"]);

?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary">Series <?=date("Y")?> Results</h3>
  </div>
</div>
<?php for($i=1; $i<=intval(date("m")); $i++): ?>
<?php if(!is_null($top3[$i])): ?>
<div class="row">
  <div class="col-md-6 col-md-offset-3 text-center">
    <h4 class="text-success"><?=date("F", strtotime(date("Y-$i-01")));?> Cup</h4>
  </div>
  <div class="col-md-6 col-md-offset-3 text-center">
    <h6 class="text-warning"><?=date("l, F jS Y", strtotime($cup_dates->$i))?> Cup</h6>
  </div>
  <div class="col-md-6 col-md-offset-3 text-center">
    <h6 class="text-primary"><a href="<?=$forum_links->$i?>" target="_blank">Forum thread for all results</a></h6>
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <br />
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <div class="row">
      <div class="col-md-4 text-center">
        <img src="/res/award/?type=gold1st&month=<?=$i?>" alt="Gold 1st" />
        <br />
        <strong><?=$top3[$i][0]->playername?></strong>
      </div>
      <div class="col-md-4 text-center">
        <img src="/res/award/?type=gold2nd&month=<?=$i?>" alt="Gold 2nd" />
        <br />
        <strong><?=$top3[$i][1]->playername?></strong>
      </div>
      <div class="col-md-4 text-center">
        <img src="/res/award/?type=gold3rd&month=<?=$i?>" alt="Gold 3rd" />
        <br />
        <strong><?=$top3[$i][2]->playername?></strong>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <hr />
  </div>
</div>
<?php endif; ?>
<?php endfor;?>
