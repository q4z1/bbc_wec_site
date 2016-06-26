<?php
/**
 * template_default_html_user_admin_settings_list
 *
 */
 
$list = app::$settings;
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1 text-center">
		<h3 class="text-primary">Settings</h3>
	</div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped signups">
      <thead>
        <tr>
					<th>Type</th>
          <th>Value</th>
					<th>Action</th>
        </tr>
      </thead>
      <tbody>
				<?php if(count($list) > 0): ?>
				<?php foreach($list as $type => $value): ?>
				<tr>
					<td><strong><?=$type?></strong></td>
					<td>
            <?if($type == "dates"): ?>
						<table class="table table-hover table-bordered table-striped">
							<tbody>
            <?php $dates = json_decode($value) ?>
						<?php foreach($dates as $i => $date): ?>
								<tr>
									<td><?=date("F", strtotime(date("Y-$i-01")))?></td>
									<td><?=$date?></td>
								</tr>
						<?php endforeach; ?>
							</tbody>
						</table>
						<?php elseif($type == "forum_links"): ?>
						<table class="table table-hover table-bordered table-striped">
							<tbody>
            <?php $links = json_decode($value) ?>
						<?php foreach($links as $i => $link): ?>
								<tr>
									<td><?=date("F", strtotime(date("Y-$i-01")))?></td>
									<td><a href="<?=$link?>" target="_blank"><?=$link?></a></td>
								</tr>
						<?php endforeach; ?>
							</tbody>
						</table>
						<?php elseif($type == "footer"): ?>
						<div class="text-center"><?=$value?></div>
						<?php elseif($type == "points"): ?>
						<table class="table table-hover table-bordered table-striped">
							<thead>
								<tr>
									<th>Table</th>
									<th>1.</th>
									<th>2.</th>
									<th>3.</th>
									<th>4.</th>
									<th>5.</th>
									<th>6.</th>
									<th>7.</th>
									<th>8.</th>
									<th>9.</th>
									<th>10.</th>
								</tr>
							</thead>
							<tbody>
            <?php $points = json_decode($value) ?>
						<?php foreach($points as $typ => $pts): ?>
						<?php if($typ == "first"): ?>
								<tr>
									<td>1st Round</td>
									<?php foreach($pts as $pt): ?>
									<td><?=$pt?></td>
									<?php endforeach; ?>
								</tr>
						<?php else: ?>
						<?php foreach($pts as $table => $pt): ?>
								<tr>
									<td><?=ucfirst($table)?></td>
									<?php foreach($pt as $p): ?>
									<td><?=$p?></td>
									<?php endforeach; ?>
								</tr>
						<?php endforeach; ?>
						<?php endif; ?>
						<?php endforeach; ?>
							</tbody>
						</table>
            <?php else: ?>
            <?=$value?>
            <?php endif; ?>
          </td>
					<td class="text-center">
						<button class="btn btn-info editset" type="submit" name="editset" __set_type__="<?=$type?>">Edit</button>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php else: ?>
				<tr>
					<td colspan="6" class="text-center text-info">No Settings found!</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
