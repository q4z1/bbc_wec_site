/**
 * base.js
 *
 */
var hideTimeout = 3000; // 3 seconds
var noticeTimeout = 10000; // 10 seconds
var noticeToPtr;
var hidePtr;
$(window).load
(
	function()
	{
    fetchNotice();
    
    $("#base").append(
      '<input type="hidden" name="fp" id="fp" value="' + fp() + '" />'
    );
		
    // back-to-top handler(s) and scroll-to-anchor handlers
		$('#backtop').click(function(event){
			event.preventDefault();
			$('html,body').animate({scrollTop:0},'slow');
		});
		$("ul.nav li a[href^='#']").on('click', function(e) {
			 e.preventDefault();
			 var hash = this.hash;
			 $('html, body').animate({
					 scrollTop: $(hash).offset().top
				 }, 300, function(){
  				 window.location.hash = hash;
				 });
		
		});
		if ( ($(window).height() + 100) < $(document).height() ) {
				$('#top-link-block').removeClass('hidden').affix({
						offset: {top:100}
				});
		}
  }
);

function fetchNotice(){
  window.clearTimeout(noticeToPtr);
  $.post(
    "/ajax/notice/"
  ).done(function(data){
    showModal(data);
    noticeToPtr = window.setTimeout(function(){fetchNotice()}, noticeTimeout);
  });
  
}

function fp()
{
    var sFP = "";
    sFP+="Resolution:"+window.screen.availWidth+"x"+window.screen.availHeight+"\n";
    sFP+="ColorDepth:"+screen.colorDepth+"\n";
    sFP+="UserAgent:"+navigator.userAgent+"\n";    
    sFP+="Timezone:"+(new Date()).getTimezoneOffset()+"\n";
    sFP+="Language:"+(navigator.language || navigator.userLanguage)+"\n";
    document.cookie="sFP";
    if (typeof navigator.cookieEnabled != "undefined" 
        && navigator.cookieEnabled == true
        && document.cookie.indexOf("sFP") != -1)
    sFP+="Cookies:true\n";
    else
    sFP+="Cookies:false\n";
    sFP+="Plugins:"+jQuery.map(navigator.plugins, function(oElement) 
		{ 
			return "\n"+oElement.name+"-"+oElement.version; 
		});
    return $.md5(sFP);
}

function showModal(html){
  window.clearTimeout(hidePtr);
  if(html == "stop"){
    window.clearTimeout(noticeTimeout);
    return;
  }
  else if(html == "None."){
    return;
  }
	if ($('#amodal').length > 0) {
			$('#amodal').remove();
	}
	if ($('#modal').length > 0) {
			$('#modal').remove();
	}
	$('.modal-backdrop').remove();
	
	$("body").append(html);
	$('#amodal').modal();
	hidePtr = window.setTimeout(function(){
		$('#amodal').modal("hide");
	}, hideTimeout);
}