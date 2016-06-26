<?php
/**
 * template_default_html_admin_upload_final
 */


?>
<div class="row">
	<div class="uploadform col-md-10 col-md-offset-1 form-group">
		<form name="default" id="default" action="<?=cfg::$web_root . 'ajax/upload/default'?>" method="post">
			<div class="row">
				<div class="col-md-12">
					<legend class="text-primary">Upload BBC game:</legend>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label for="step">Step (1-4):</label><br>
					<select name="step" id="step" class="form-control">
						<option value="s1" selected="selected">Step 1</option>
						<option value="s2">Step 2</option>
						<option value="s3">Step 3</option>
						<option value="s4">Step 4</option>
					</select>
					<br />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label for="logurl">Link from Log-File Analysis:</label>
					<input name="logurl" id="logurl" type="text" class="form-control" placeholder="http://pokerth.net/log-file-analysis.html?ID=1234567890abcdef&UniqueGameID=1" />
					<br />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					<button class="btn btn-primary" name="udef" id="udef">
						<span class="glyphicon glyphicon-upload"></span> Upload
					</button>
				</div>
			</div>
		</form>
	</div>
</div>