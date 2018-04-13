jQuery(document).ready(function() {
  //Preloader
  $(window).on('load', function () {
        if ($("loader-wrapper").length > 0)
        {
            $("loader-wrapper").fadeOut("slow");
        }

        var screen = $(window);
        if (screen.width() > 700)
        {
            $('#menuCheckbox').attr('checked', true);
        }
        else {
            $('#menuCheckbox').attr('checked', false);
        }
    });
});

jQuery(document).resize(function() {
    var screen = $(window);
    if (screen.width() > 700)
    {
        $('#menuCheckbox').attr('checked', true);
    }
    else {
        $('#menuCheckbox').attr('checked', false);
    }
});
