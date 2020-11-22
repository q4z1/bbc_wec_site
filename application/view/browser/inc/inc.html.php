<?php
/**
 * application_view_browser_inc
 *
 * <meta> und <base> Angabe
 * includierte JavaScript- und CSS-Dateien
 */
 $base = (cfg::$web_root == '/') ? '.' : cfg::$web_root;
?>
<base href="<?=$base?>">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php $ts = '?ts=' . time(); ?>
<link rel="stylesheet" type="text/css" href="<?=cfg::$web_root?>app.css<?=$ts?>">
<link rel="stylesheet" type="text/css" href="<?=cfg::$web_root?>theme.css<?=$ts?>">