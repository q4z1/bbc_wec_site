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
      <?php if(array_key_exists("footer", app::$content['modal'])): ?>
      <div class="modal-footer">
        <?=app::$content['modal']["footer"]?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
