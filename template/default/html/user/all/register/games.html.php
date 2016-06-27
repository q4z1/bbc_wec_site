<?php
/**
 * template_default_html_user_all_register_games:
 *
 */
?>
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <h3 class="text-primary">Registration for BBC games:</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <hr />
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <label for="playername">Playername:</label>
    <input class="form-control input-sm" placeholder="Your pokerth nickname" value="<?=(app::$session == "admin")?$_SESSION['admin']. '" readonly="readonly':''?>" />
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <hr />
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <label for="gamedate[]">I want to play:</label>
    <table class="table table-bordered table-condensed table-hovered table-striped">
      <thead>
        <tr>
          <th style="width: 2%"></th>
          <th>Step</th>
          <th>Date/Time</th>
        </tr>
      </thead>
      <tbody>
          <?php foreach(app::$content['games'] as $gd): ?>
          <tr>
            <td><input type="checkbox" name="gamedate[]" value="<?=$gd->gamedates_id?>" /></td>
            <td class="text-step<?=$gd->step?>"><?=$gd->step?></td>
            <td class="text-step<?=$gd->step?>"><?=date("D, jS \of F Y H:i", strtotime($gd->date)) . " CEST"?></td>
          </tr>
          <?php endforeach; ?>
      </tbody>
      <tfoot>
        
      </tfoot>
    </table>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <hr />
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <button class="btn btn-primary" id="sbmtgamereg">Submit Registration</button>
  </div>
</div>