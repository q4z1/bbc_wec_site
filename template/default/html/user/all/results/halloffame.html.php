<?php
/**
 * template_default_html_user_all_results_halloffame:
 *
 */
$plyrs = app::$content['ranking'];
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary">Hall-of-Fame <?=date("Y")?></h3>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th>Position</th>
          <th>Avatar</th>
          <th>Player</th>
          <th>Points</th>
          <th>Awards</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($plyrs as $i => $plyr): ?>
        <tr>
          <td><?=($i+1)?></td>
          <td class="avatar">
            <?php if(!is_null($plyr->avatar_mime) && $plyr->avatar_mime != ""): ?>
            <?='<img src="data:'.$plyr->avatar_mime.';base64,'.base64_encode(stripslashes($plyr->avatar)).'" alt="Avatar" />';?>
            <?php else: ?>
            &nbsp;
            <?php endif; ?>
          </td>
          <td><?=$plyr->playername?></td>
          <td><?=$plyr->points?></td>
          <td>
            <?php foreach($plyr->awds as $award): ?>
            <?=$award?>
            <?php endforeach; ?>
          </td>
        <?php endforeach; ?>
        </tr>
      </tbody>
    </table>
  </div>
</div>

