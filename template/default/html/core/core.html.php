<?php
/**
 * template_default_html_core_core:
 *
 */
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?=app::$config->core->title?></title>
		<meta charset="utf-8"/>
		<link href="/favicon.ico" rel="shortcut icon" />
		<?php require(view::get_special("inc"))?>
	</head>
	<body>
		<div id="base">
			<div id="notice"></div>
			<div class="head">
				<?php for($i=0; $i < view::num_col('head'); $i++): ?>
				<?php require(view::get_col('head', $i))?>
				<?php endfor; ?>
			</div>
			<div class="middle row">
				<div class="col-md-12" id="maincol">
					<?php for($i=0; $i < view::num_col('maincol'); $i++): ?>
					<?php require(view::get_col('maincol', $i))?>
					<?php endfor; ?>
				</div>
			</div>
			<div class="foot row">
					<?php for($i=0; $i < view::num_col('foot'); $i++): ?>
					<?php require(view::get_col('foot', $i))?>
					<?php endfor; ?>
			</div>
			<div id="hidden_content"></div>
			<?php //if(cfg::$debug): require(view::get_special("debug")); endif;?>
		</div>
		<span id="top-link-block" class="hidden">
				<a href="#top" class="well well-sm" id="backtop">
					<i class="glyphicon glyphicon-chevron-up"></i> Back to Top
				</a>
		</span>
		<?php $ts = '?ts=' . time(); ?>
		<script type="text/javascript" src="<?=cfg::$web_root?>app.js<?=$ts?>"></script>
	</body>
</html>
