$(window).load
(
	function()
	{
    if ($('button.validatesup').length > 0) {
      $('button.validatesup').each(function(i,item){
        $(item).click(function(event){
          validate_signup($(this).attr('__sup_id__'));
        });
      });
    }

		if ($('.removesup').length > 0) {
      $('.removesup').each(function(i,item){
        $(item).click(function(event){
          delete_signup($(this).attr('__sup_id__'));
        });
      });
    }
  }
);

function validate_signup(id) {
  $.post(
    "/ajax/signup/validate",
    { id: id}
  ).done(function(data){
    showModal(data);
    $('button.validatesup[__sup_id__='+id+']').parent().parent().children().eq(4).text("Yes");
    $('button.validatesup[__sup_id__='+id+']').remove();
  });

}


function delete_signup(id) {
	if ($('#modal').length > 0) {
    	$('#modal').remove();
      $('.modal-backdrop').remove();
  }
	var modal = "<div id='modal' class='modal fade' role='dialog'>"
		+ "<div class='modal-dialog modal-lg'><div class='modal-content'>"
		+ "<div class='modal-body'>"
		+ "Are you sure to delete singup id " + id + "</div><div class='modal-footer'>"
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
				"/ajax/signup/delete",
				{ id: id}
			).done(function(data){
        showModal(data);
        $('button[__sup_id__='+id+']').parent().parent().remove();
      });
		});
}