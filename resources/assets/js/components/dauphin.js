    /** Dauphin - Premium Responsive Bootstrap Admin Template **/
    
    //Toggle navbar if width < 1024px 
    if ($(window).width() < 768) {
        $("body").removeClass("left-nav-minimized");
    } else if ($(window).width() < 1024) {
        $("body").addClass("left-nav-minimized");
    } else {
        $("body").removeClass("left-nav-minimized");
    }

    //Document ready functions
    $(document).ready(function () {
        //Toggle navbar if width < 1024px 
        $(window).resize(function() {
            if ($(window).width() < 768) {
                $("body").removeClass("left-nav-minimized");
            } else if ($(window).width() < 1024) {
                $("body").addClass("left-nav-minimized");
            } else {
                $("body").removeClass("left-nav-minimized");
            }
        });

        // Remove transition restriction (fix IE rendering bug)
        $('body').removeClass('hold-transition');

        // Call breadcrumb active element
        $('body').delay(200).queue(function(){
            breadcrumb();
            $(this).dequeue();
        });

        // Toggle left menu
        $('.left-nav-toggle').click(function(event){
            event.preventDefault();
            $("body").toggleClass("left-nav-minimized");
        });

        // Hide expanded nav-item
        $('.nav-item').on('show.bs.collapse', function () {
            $('.nav-item.in').not(this).collapse('hide');
        });

        // Hide expanded nav-item-secondary
        $('.nav-item-secondary').on('show.bs.collapse', function () {
            $('.nav-item-secondary.in').collapse('hide');
        });

        // Sidebar time
        $('.time').click(function(){
            $(this).toggleClass('non-visible');
        });

        function breadcrumb(){
            $('.breadcrumb li:last-child a').css({'opacity' : '1', 'margin-left' : "0px"});
        }
    });
