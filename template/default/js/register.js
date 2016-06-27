$(window).load
(
	function()
	{
    // @TODO: implement ajax reg process
    if($('#sbmtgamereg').length > 0){
      $('#sbmtgamereg').click(function(){
        // @TODO: perform ajax reg process
        dates = "";
        $('input[name="gamedate"]:checked').each(function() {
          dates += $(this).val() + ",";
        });
        dates = dates.substring(0, dates.length - 1);
        playername = $('#playername').val();
        if(playername == "" || dates == ""){
          bootbox.alert('<h4 class="text-danger">No Playername or selected game(s) given!</h4>');
        }else{
          $.post(
            "/ajax/register/games/",
            {
              playername: playername,
              dates: dates,
              fp: $('#fp').val()
            }
          ).done(function(data){
            
            if($('#amodal').length > 0){
              $('#amodal').remove();
              $('.modal-backdrop').remove();
            }
            $("body").append(data);
            $('input[name="gamedate"]').each(function() {
              $(this).prop('checked', false);
            });
            if($('#playername').prop("readonly") !== true){
              $('#playername').val('');
            }
            
            $('#amodal').modal();
          });
        }
      });
    }
  }
);