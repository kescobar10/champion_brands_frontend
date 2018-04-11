jQuery(document).ready(function() {
  //Preloader
  $(window).on('load', function () {
        if ($("loader-wrapper").length > 0)
        {
            $("loader-wrapper").fadeOut("slow");
        }
    });
});
