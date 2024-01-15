$(document).ready(function () {
    $('.card-header').on('click', function(event){
        event.preventDefault();
        // create accordion variables
        var accordion = $(this);
        var accordionContent = accordion.next('.collapse');

        // toggle accordion link open class
        accordion.toggleClass("open");
        // toggle accordion content
        accordionContent.slideToggle(250);

    });
});

$( ".premium-top" ).click(function() {
  $( ".drop_prem" ).toggle();
});
