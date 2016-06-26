<?php
	/*
	 * application_view_blank
	 *
	 * Darstellung von nicht gesetzten views und ggf. Ausgabe einer Fehlermeldung
	 */
?>
<?php if(cfg::$debug && !is_null(view::$missing)): ?>
	<?php if(cfg::$proc == "browser"): ?>
	<div class="view_error">
		<?=view::$missing?>
	</div>
	<?php elseif(cfg::$proc == "cli"): ?>
	<?=view::$missing?>
	<?php /* @TODO: ggf. noch weitere $proc modi hinzufügen */ ?>
	<?php endif; ?>
<?php endif; ?>