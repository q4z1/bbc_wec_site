<?php
/**
 * application_view_browser_error_ajax
 *
 * Darstellung der Fehlermeldung bei fehlerhaften Ajax-Aufrufen
 *
 * @TODO: Darstellung muss noch angepasst werden!
 */
?>
<div class="default_error">
	Der Aufruf der Seite <?=app::$request['uri']?> verursachte einen Fehler:<br />
	<span class="error"><?=app::$content['error']?></span>
</div>