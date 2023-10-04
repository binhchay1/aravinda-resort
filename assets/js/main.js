$(".carousel").swipe({

  swipe: function(event, direction, distance, duration, fingerCount, fingerData) {

    if (direction == 'left') $(this).carousel('next');
    if (direction == 'right') $(this).carousel('prev');

  },
  allowPageScroll:"vertical"

});
$(function () {
	$('.date-picker').datepicker({
		format: "D-M-Y",
        
	});
    $("body").on("contextmenu", function(e) {
        e.preventDefault();
    });
    $('.form-booking .booking-body').attr('href', 'https://aravinda-resort.com/booking-engine');
});

// $('#mainNav  .nav-row-2 .dropdown').click(function(){
// 	$('#mainNav').toggleClass('open');
// });
// $('#mainNav .nav-row-2 .dropdown').on('hide.bs.dropdown', function () {
//   $('#mainNav').removeClass('open');
// });

$('.bot-nav-mobile-li .language .language-btn').click(function(){
	$('.bot-nav-mobile-li .menu-language').toggle();
});
$('.date-go').datepicker({
    beforeShowDay: $(this).hasClass('noWeekends') ? $.datepicker.noWeekends : null,
    changeYear: true,
    changeMonth: true,
    yearRange: '-0y-0m_:+5y',
    minDate: new Date(2019, 4 -1, 15),
    currentText: 'aujourd\'hui',
    showOtherMonths: true,
    showButtonPanel: true,
    firstDay: 1,
    duration: 0,
    dateFormat: 'dd-mm-yy',
    onClose: function(selectedDate) {
        if (selectedDate != '') {
            selectedDate = selectedDate.split('-')[2] + '-' + selectedDate.split('-')[1] + '-' + selectedDate.split('-')[0];
            var dateObject = new Date(selectedDate);
            dateObject.setDate(dateObject.getDate() + 1);
            console.log(dateObject);
            $(".date-come").datepicker("option", "minDate", dateObject);
        }
    }
});
$('.date-come').datepicker({
    beforeShowDay: $(this).hasClass('noWeekends') ? $.datepicker.noWeekends : null,
    changeYear: true,
    changeMonth: true,
    yearRange: '-0y-0m_:+5y',
    minDate: new Date(2019, 4 -1, 16),
    currentText: 'aujourd\'hui',
    showOtherMonths: true,
    showButtonPanel: true,
    firstDay: 1,
    duration: 0,
    dateFormat: 'dd-mm-yy',
    onClose: function(selectedDate) {
        if (selectedDate != '') {
            selectedDate = selectedDate.split('-')[2] + '-' + selectedDate.split('-')[1] + '-' + selectedDate.split('-')[0];
            var dateObject = new Date(selectedDate);
            dateObject.setDate(dateObject.getDate() - 1);
            $(".date-go").datepicker("option", "maxDate", dateObject);
        }
    }
});
$('#mainNav .nav > .dropdown > a .arrow').click(function(){
    // $('.navbar-nav > li > .dropdown-menu').toggle();
    $(this).closest('.dropdown').find('.dropdown-menu').toggle();
    $(this).toggleClass('active');
    return false;
})
