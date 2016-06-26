<?php
/**
 * template_default_html_user_all_shoutbox_shoutbox:
 *
 */

 // @TODO: input form inside modal!
?>
<div class="row">
  <div class="col-md-12 text-center">
    <h3 class="text-primary">Shoutbox</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-2 col-md-offset-5 text-center">
    <button class="btn btn-primary" id="newpost" name="newpost">Post a message</button>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <hr />
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    Pagination
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="form-control" id="sbposts"></div>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    Pagination
  </div>
</div>

<!-- modal for posts -->
<div id="bmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="text-primary">New post:</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php if(app::$session == "admin"): ?>
            <label for="to">Message to:</label>
            <div class="row">
                <div class="col-md-2 text-left">
                    <div class="input-group">
                        <span class="input-group-addon beautiful">
                            <input name="to" type="radio" value="all" checked="checked" />
                            All
                        </span>
                        <span class="input-group-addon beautiful">
                            <input name="to" type="radio" value="admin" />
                            Admins
                        </span>
                    </div>
                </div>
            </div>
            <br />
            <?php endif; ?>
            <label for="nickname">Name:</label>
            <input name="nickname" id="nickname" type="text" class="form-control input-sm" value="<?=(app::$session == "admin")?$_SESSION['admin'].'" readonly="readonly':''?>" placeholder="Your pokerth nickname" />
            <br />
            <label for="message">Message:</label>
            <textarea name="message" id="message" class="form-control input-sm" rows="8" placeholder="... leave a message ..."></textarea>
            <br />
            <div class="text-center">
              <button class="btn btn-primary" name="psbx" id="psbx">Send</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>