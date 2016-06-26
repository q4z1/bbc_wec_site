$(window).load
(
	function()
	{
    if ($('button.editset').length > 0) {
      $('button.editset').each(function(i,item){
        $(item).click(function(event){
          edit_setting($(this).attr('__set_type__'));
        });
      });
    }
  }
);

function edit_setting(type) {
  alert("edit " + type + " clicked!");
}