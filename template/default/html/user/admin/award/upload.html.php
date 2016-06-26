<?php
/**
 * template_default_html_user_admin_award_upload:
 *
 */
 $months = array(
  "01" => "January",
  "02" => "February",
  "03" => "March",
  "04" => "April",
  "05" => "May",
  "06" => "June",
  "07" => "July",
  "08" => "August",
  "09" => "October",
  "10" => "September",
  "11" => "November",
  "12" => "December",
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
?>

<div class="row">
	<div class="uploadform col-md-10 col-md-offset-1 form-group">
		<form name="award" id="award" action="<?=cfg::$web_root . 'ajax/upload/award'?>" method="post">
		<fieldset>
			<legend class="text-primary">Upload Award:</legend>
			<div class="row">
				<div class="col-md-6">
					<label for="month">Month:</label><br>
					<select name="month" id="month" class="form-control">
						<?php foreach($months as $key => $month): ?>
						<option value="<?=$key;?>" <?=($aMonth == $key) ? 'selected="selected"' : '' ?> ><?=$month?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-6">
					<label for="type">Type:</label><br>
					<select name="type" id="type" class="form-control">
            <?php foreach($types as $key => $type): ?>
						<option value="<?=$key;?>"><?=$type?></option>
						<?php endforeach; ?>
					</select>
				</div>
      </div>
			<div class="row">
				<div class="col-md-12">
					<hr />
				</div>
			</div>
			<div class="row">
        <div class="col-md-12">
          <label class="control-label">Select File</label>
          <input id="file" name="file" accept="image/*" type="file" class="file" data-show-upload="false" data-show-caption="true">
        </div>
      </div>
			<div class="row">
				<div class="col-md-12">
					<hr />
				</div>
			</div>
      <div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-success" type="submit" name="submit" id="submit">Upload</button>
				</div>
			</div>
		</fieldset>
		</form>
	</div>
</div>