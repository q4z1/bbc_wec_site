<?php
/**
 * template_default_html_user_admin_award_edit:
 *
 */

$months = array(
  1 => "January",
  2 => "February",
  3 => "March",
  4 => "April",
  5 => "May",
  6 => "June",
  7 => "July",
  8 => "August",
  9 => "October",
  10 => "September",
  11 => "November",
  12 => "December",
 );
 $aMonth = date("m");
 
 $types = array(
  "gold1st" => "Gold 1st",
	"gold2nd" => "Gold 2nd",
	"gold3rd" => "Gold 3rd",
  "silver1st" => "Silver 1st",
	"silver2nd" => "Silver 2nd",
	"silver3rd" => "Silver 3rd",
  "bronze1st" => "Bronze 1st",
  "bronze2nd" => "Bronze 2nd",
	"bronze3rd" => "Bronze 3rd",
  "rank1st" => "Ranking 1st",
  "rank2nd" => "Ranking 2nd",
  "rank3rd" => "Ranking 3rd",
	"top20" => "Top 20",
  "admin" => "Admin",
 );
 
 $awrds = app::$content['awards'];
 $plyrs = app::$content['players'];
 $aid = "award" . date("Y") . "_id";
 $pid = "player" . date("Y") . "_id";
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary">Awards <?=date("Y")?>:</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped awards">
      <thead>
        <tr>
          <th>Month</th>
          <th>Type</th>
          <th>Filename</th>
          <th>Preview</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if(count($awrds) > 0): ?>
        <?php foreach($awrds as $awrd): ?>
        <tr>
          <td><?=$months[$awrd->month]?></td>
          <td><?=$types[$awrd->type]?></td>
          <td><?=$awrd->filename?></td>
          <td class="text-center"><img style="width: 150px;" src="/res/award/?type=<?=$awrd->type?>&month=<?=$awrd->month?>" /></td>
          <td class="text-center">
            <!-- @XXX: necessary?
            <button class="btn btn-warning reupload" type="submit" name="reupload" id="reupload" __awrd_id__="<?=$awrd->$aid?>">Re-Upload</button><br />-->
            <button class="btn btn-success assign" type="submit" name="assign" __awrd_id__="<?=$awrd->$aid?>">Assign</button><br />
            <button class="btn btn-danger delete" type="submit" name="delete" __awrd_id__="<?=$awrd->$aid?>">Delete</button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
          <td colspan="5">No awards available!</td>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div id="pmodal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-primary">Assign the award <span class="awrdname text-success"></span> to the following player(s):</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <label for="player">Player:</label><br />
            <select name="player" id="player" class="form-control">
              <option value="0">---</option>
              <?php foreach($plyrs as $plyr): ?>
              <option value="<?=$plyr->$pid?>"><?=$plyr->playername?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="cold-md-10 col-md-offset-1">
            <hr />
          </div>
        </div>
        <div class="row">
          <div class="cold-md-10 col-md-offset-1">
            <h5 class="text-primary">Your selection:</h5>
          </div>
        </div>
        <div class="row">
          <div class="cold-md-10 col-md-offset-1 pselection">

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancel">Cancel</button>
        <button type="button" class="btn btn-success" data-dismiss="modal" id="assign">Assign</button>
      </div>
    </div>
  </div>
</div>
  
</div>