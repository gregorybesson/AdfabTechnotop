$(function()
{
	$('#rules').on('click', function ()
	{
        $('.modal-body', '#rules-popin').load($(this).attr('href') + ' #terms', function ()
        {
            $('#rules-popin').modal();
        });
		return false;
	});
});
