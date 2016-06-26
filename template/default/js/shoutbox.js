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
  }
);

function refreshShoutbox(start=0, end=50){
  $.post(
    "/ajax/shoutbox/posts/",
    {
      start: start,
      end: end
    }
  ).done(function(data){
    var obj = $.parseJSON(data);
    var posts = '<ul class="list-group">';
    $(obj).each(function(i,el){
      nickclass = ' sbpnicknormal';
      if(el.status == 2){
        nickclass = ' sbpnickadmin';
      }
      msgclass = ' sbpmsgnormal';
      if(el.status == 3){
        msgclass = ' sbpmsgadmin';
      }
      posts += '<li class="list-group-item">'
        + '<span class="sbpid">#' + el.shoutbox_id + "</span>&nbsp;|&nbsp;"
        + '<span class="sbpdate">' + el.created + "</span>&nbsp;|&nbsp;"
        + '<span class="sbpnick' + nickclass + '">' + el.playername + "</span>:"
        + '<p class="sbpmsg' + msgclass + '">' + el.msg.replace("\n", "</p><p>") + "</p>";
    });
    posts += "</ul>";
    $('#sbposts').html(posts);
  });
}