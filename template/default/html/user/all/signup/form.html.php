<?php
/**
 * template_default_html_user_all_signup_form:
 *
 */
$cup_dates = json_decode(app::$settings["dates"]);
$i = intval(date("m"));
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-left form-group">
		<form name="singup" id="signup" action="<?=cfg::$web_root . 'ajax/signup/'?>" method="post">
		<fieldset>
			<legend class="text-primary">Registration for: <span class="text-success"><?=date("l, F jS Y", strtotime($cup_dates->$i))?> Cup</span></legend>
      <div class="row">
				<div class="col-md-12">
          <label for="playername">Playername:</label>
          <input type="text" name="playername" id="playername" class="form-control" />
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <br />
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-right">
          <button class="btn btn-success" type="submit" name="submit" id="submit">Register</button>
        </div>
      </div>
    </fieldset>
    </form>
  </div>
</div>