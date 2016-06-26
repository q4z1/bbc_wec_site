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
<?php $ts = '?ts=20150204'; ?>
<?php foreach(app::$inc->css as $filename): ?>
<link rel="stylesheet" type="text/css" href="<?=cfg::$web_root?>res/css/<?=cfg::$template."/".$filename.'.css'.$ts?>">
<?php endforeach ?>
