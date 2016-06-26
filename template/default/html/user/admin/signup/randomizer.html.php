<?php
/**
 * template_default_html_user_admin_signup_randomizer
 *
 */
$list = app::$content['signups'];
$ts = time();
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1 text-center">
		<h3 class="text-primary">Random seeding - generated <?=$ts?> / <?=date("Y-m-d H:i:s", $ts)?></h3>
	</div>
</div>
<div class="row">
  <div class="col-md-6 col-md-offset-3">
				<?php if(count($list) > 0): ?>
        <pre class="text-left">
<?php $i=1; ?>
<?php foreach($list as $sup): ?>
<?=$sup->playername?>
<?=($i<(count($list)))?"\n":""?>
<?php $i++; ?>
<?php endforeach; ?>
        </pre>
				<?php else: ?>
				<h3 class="text-warning text-center">No Signups found!</h3>
				<?php endif; ?>
	</div>
</div>
