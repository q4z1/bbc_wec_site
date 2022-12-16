var sbpaginationActive = false;
var sbRefreshTimeout = null;
var page = 1;

$(window).load
(
	function()
	{
    refreshShoutbox();
    
    if($('#newpost').length > 0){
      $('#newpost').click(function(){
          if(!$('#nickname').prop('readonly')){
            $('#nickname').val('');
          }
          $('#message').val('');
          $('#bmodal').modal('show');
      });
    }
    
    if($('#psbx').length > 0){
      $('#psbx').click(function(){
        $('#bmodal').modal('hide');
          var to = null;
          var nickname = null;
          var msg = null;
          if($('input[name=to]').length > 0){
            to = $('input[name=to]:checked').val();
          }
          nickname = $('#nickname').val();
          var msg = $('#message').val();
          if($('#charlimit').length > 0 && msg.length > $('#charlimit').val()){
            msg = msg.substring(0, $('#charlimit').val());
          }
          $.post(
            "/ajax/shoutbox/post/",
            {
              to: to,
              nickname: nickname,
              msg: msg,
              fp: $('#fp').val()
            }
          ).done(function(data){
            showModal(data);
            refreshShoutbox();
          });
      });
    }
    
		$('input#msgid').keyup(function(e) {
			if (e.which == 13)
			{
				getMsg();
			}
    });

		$('#sbgetmsg').click(function(){
      getMsg();
		});
    
    if($('#charlimit').length > 0){
      $("#message").keyup(function(){
        if($(this).val().length > $('#charlimit').val()){
          $(this).val($(this).val().substring(0, $('#charlimit').val()));
          bootbox.alert("Maximal number of characters reached!");
        }
      });
    }
    
  }
);

function refreshShoutbox(start=0, num=50){
  clearTimeout(sbRefreshTimeout);
  $.post(
    "/ajax/shoutbox/posts/",
    {
      start: start,
      num: num,
      t: $.now()
    }
  ).done(function(data){
    var obj = $.parseJSON(data);
    var numPosts = obj.num;
    var posts = '<ul class="list-group">';
    $(obj.posts).each(function(i,el){
      nickclass = ' sbpnicknormal text-primary';
      if(el.status > 1){
        nickclass = ' sbpnickadmin text-danger';
      }
      msgclass = ' sbpmsgnormal';
      if(el.status == 3){
        msgclass = ' sbpmsgadmin';
      }
      posts += '<li class="list-group-item">'
        + '<span class="sbpid">#' + el.shoutbox_id + "</span>&nbsp;|&nbsp;"
        + '<span class="sbpdate">' + el.created + "</span>&nbsp;|&nbsp;"
        + '<span class="sbpnick' + nickclass + '">' + el.playername + "</span>:"
        + '<div class="sbpmsg' + msgclass + '">'
        + "<p>" + el.msg.replace(/(?:\r\n|\r|\n)/g, '<br />') + "</p>"
        + "</div>";
    });
    posts += "</ul>";
    $('#sbposts').html(posts);
    if(!sbpaginationActive){
      $('.sbpagination').bootpag({
        total: Math.ceil(numPosts / 50),
        maxVisible: 3,
        firstLastUse: true,
      }).on("page", function(event, p){
        // @XXX: bug in bootpag? - small workaround in order to avoid double requests & refresh-timeouts
        if(page == p){
          return;
        }
        page = p;
        
        refreshShoutbox(((p-1)*50), 50);
      });
      sbpaginationActive = true;
    }
    sbRefreshTimeout = setTimeout(function(){refreshShoutbox(((page-1)*50), 50)}, 15000);
  });
}

function getMsg(){
  if ($('input#msgid').val() == "" || isNaN($('input#msgid').val())) {
    $('input#msgid').val('');
    return;
  }
  if ($('#amodal').length > 0) {
      $('#amodal').remove();
  }
  $.post(
    "/ajax/shoutbox/getmsg/",
    { msgid: $('input#msgid').val(),
      t: $.now()
  }).done(function(data){
      $('body').append(data);
      $('#amodal').modal('show');
  });
}