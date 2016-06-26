var sbpaginationActive = false;

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
  }
);

function refreshShoutbox(start=0, end=50){
  $.post(
    "/ajax/shoutbox/posts/",
    {
      start: start,
      end: end,
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
          maxVisible: 10,
          firstLastUse: true,
      }).on("page", function(event, num){
           refreshShoutbox(((num-1)*50), 50);
      });
      sbpaginationActive = true;
    }

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