$(document).ready(function() {
    $('.my-custom-select').select2(
    	{
    		theme: 'bootstrap4'
    	}
    );
    $(window).on('resize', function(){
        $(window).on('load, resize', function(){
            setTimeout(function() {
                $('.my-custom-select').select2(
                	{
                		theme: 'bootstrap4'
                	}
            ), 200);
        });
    });

    function getParameter(p)
    {
        var url = window.location.search.substring(1);
        var varUrl = url.split('&');
        for (var i = 0; i < varUrl.length; i++)
        {
            var parameter = varUrl[i].split('=');
            if (parameter[0] == p)
            {
                return parameter[1];
            }
        }
    }


    $('#navbarSupportedContent a[href="#'+getParameter('pills')+'"]').tab('show');
});
