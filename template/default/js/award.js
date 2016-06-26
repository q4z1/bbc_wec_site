$(window).load
(
	function()
	{
    if ($('button.reupload').length > 0) {
      $('button.reupload').each(function(i,item){
        $(item).click(function(event){
          alert("reupload clicked\nid="+$(this).attr('__awrd_id__'));
        });
      });
    }
    
    if ($('button.assign').length > 0) {
      $('button.assign').each(function(i,item){
        $(item).click(function(event){
          var id = $(this).attr('__awrd_id__');
          var name = $('button[__awrd_id__='+id+']').parent().parent().children().eq(2).text();
          //alert("id="+id+";name="+name);
          $('.awrdname').append(name);
          $('#pmodal').modal();
          $('#pmodal').on('hidden.bs.modal', function () {
              $('button#assign').unbind('click');
          });
          
          $('button#cancel').click(function(){
            $('.awrdname').empty();
            $('.pselection').empty();
          });
          
          $('button#assign').click(function(){
            assign_avatar(id);
          });
        });
      });
    }
    
    if ($('button.delete').length > 0) {
      $('button.delete').each(function(i,item){
        $(item).click(function(event){
          delete_award($(this).attr('__awrd_id__'));
        });
      });
    }
    
    if ($('select#player').length > 0) {
      $('select#player').change(function(){
        $('.pselection').append(
          '<div class="row p'+$('select#player').val()+'"><div class="col-md-3"><label>'
          + $('select#player option:selected').text() + "</label></div><div class='col-md-3'>"
          + '<input type="checkbox" value="'
          + $('select#player').val() + '" checked="checked" /></div></div>'
        );
        $('.p'+ $('select#player').val() + ' input[type=checkbox]').click(function(){
          if(!$(this).is(':checked')){
            $('.p'+ $(this).val()).remove();
          }
        });
        $('select#player option:eq(0)').prop('selected', true);
      });
    }
  }
);

function delete_award(id) {
	if ($('#modal').length > 0) {
    	$('#modal').remove();
      $('.modal-backdrop').remove();
  }
	var modal = "<div id='modal' class='modal fade' role='dialog'>"
		+ "<div class='modal-dialog modal-lg'><div class='modal-content'>"
		+ "<div class='modal-body'>"
		+ "Are you sure to delete award id " + id + "</div><div class='modal-footer'>"
		+ "<button type='button' class='btn btn-danger'"
		+ " data-dismiss='modal' id='cancel'>Cancel</button>"
		+ "<button type='button' class='btn btn-success'"
		+ " data-dismiss='modal' id='remove'>Delete</button>"
		+ "</div></div></div>";
		$(".middle").append(modal);
		$('#modal').modal();
		
		$('button#cancel').click(function(){
			$('button#remove').unbind('click');
		});
		
		$('button#remove').click(function(){
			$('button#cancel').unbind('click');
			$.post(
				"/ajax/award/delete",
				{ id: id}
			).done(function(data){
        showModal(data);
        $('button[__awrd_id__='+id+']').parent().parent().remove();
      });
		});
}

function assign_avatar(id) {
  console.log("assigning award id="+id);
  //$('button#assign').unbind('click');
  var players = new Array();
  $('input[type=checkbox]').each(function(i, item){
    players.push($(item).val());  
  });
  jObj = {
    "award_id": id,
    "players": players
  }
  $('.awrdname').empty();
  $('.pselection').empty();
  
  $.post(
    "/ajax/award/assign",
    JSON.stringify(jObj)
  ).done(function(data){showModal(data)});
}