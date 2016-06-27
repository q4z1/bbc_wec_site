<?php
/**
 * template_default_html_user_all_games_games:
 *
 */
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <h3 class="text-primary">Scheduled BBC Games:</h3>
  </div>
</div>
<div class="row games">
  <div class="col-md-10 col-md-offset-1">
    <!-- navbar for selecting scheduled step games -->
    <nav class="navbar navbar-bordered">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#games-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <p class="navbar-text text-warning" id="gamestep"><?=app::$content['stepgame']?></p>
        </div>
        <div class="collapse navbar-collapse" id="games-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown active">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Step 1 Tables<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">future game</a></li>
                <li><a href="#">future game</a></li>
                <li><a href="#">future game</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">next game</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">last game</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Step 2 Tables<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">future game</a></li>
                <li><a href="#">future game</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">next game</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">last game</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Step 3 Tables<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">next game</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">last game</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Step 4 Tables<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">next game</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">last game</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center" id="stepgame">
    <h4 class="text-primary">Registered Players:</h4>
  </div>
</div>
