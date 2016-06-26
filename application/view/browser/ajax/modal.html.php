<?php
	/*
	 * view_browser_ajax_modal
	 */
?>
<div id="amodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3><?=app::$content['modal']["heading"]?></h3>
      </div>
      <div class="modal-body">
        <?=app::$content['modal']["content"]?>
      </div>
    </div>
  </div>
</div>
