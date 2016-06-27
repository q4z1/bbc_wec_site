<?php
/**
 * template_default_html_core_leftcol_navi:
 *
 */
?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bbc-navbar-all" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="bbc-navbar-all">
      <ul class="nav navbar-nav">
        <li><a href="/">Home</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Upload game<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/admin/upload/">BBC Step 1-4</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Custom</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Registration <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/main/register/">Register for BBC</a></li>
            <li><a href="/main/games/">Scheduled BBC games</a></li>
          </ul>
        </li>
        <li><a href="/main/shoutbox/" title ="Shoutbox">Shoutbox</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin Tools <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">Create a BBC game date</a></li>
          </ul>
        </li>
				<li><a href="/main/logout/" title ="Login">Logout</a></li>
			</ul>
		</div>
	</div>
</nav>