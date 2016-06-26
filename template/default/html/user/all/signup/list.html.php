<?php
/**
 * template_default_html_user_all_signup_list
 *
 */
$cup_dates = json_decode(app::$settings["dates"]);
$i = intval(date("m"));
$list = app::$content['signups'];
$subs = app::$content['subs'];
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1 text-center">
		<h3 class="text-primary">SignUps for: <span class="text-success"><?=date("l, F jS Y", strtotime($cup_dates->$i))?> Cup</span></h3>
	</div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped signups">
      <thead>
        <tr>
          <th>Player</th>
        </tr>
      </thead>
      <tbody>
				<?php if(count($list) > 0): ?>
				<?php foreach($list as $i =>$sup): ?>
				<tr>
					<!--<td><strong><?=printf("%'.02d.</strong> ", ($i+1)) . $sup->playername?></td>-->
					<td><?=$sup->playername?></td>
				</tr>
				<?php endforeach; ?>
				<?php if(count($subs) > 0): ?>
				<tr>
					<td class="text-center">
						<div class="row">
							<div class="col-md-8 col-md-offset-2">
								<h4 class="text-info">Substitutes:</h4>
								<p>
								<?php foreach($subs as $i => $sub): ?>
									<?=$sub->playername?><?=($i<count($subs)-1)?', ': ''?>
								<?php endforeach; ?>
								</p>
							</div>
						</div>
					</td>
				</tr>
				<?php endif; ?>
				<?php else: ?>
				<tr>
					<td class="text-center text-info">No Signups found!</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
