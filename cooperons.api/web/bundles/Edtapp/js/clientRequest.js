$(function() {
    /*
     * filtre categorie
     */

    $('.listitem').on('click', function() {
        $('ul.dropdown-menu li.active').removeClass('active');
        $(this).parent('li').addClass('active');
        $('#label').empty();
        $('#label').text($('ul.dropdown-menu li.active a').text());
        if ($(this).data('category') == "categ-0") {
            $('.span3, .service_ae').show();
        } else {
            $('.span3, .service_ae').hide()
            $('.' + $(this).data('category')).show();
        }
    });

    /*
     * trie date
     */
    function sortDescending(a, b) {
        var datetime1 = $(a).find(".year").text();
        var datetimepart1 = datetime1.split(' ');
        var date1 = datetimepart1[0];
        var time1 = datetimepart1[1];
        date1 = date1.split('-');
        time1 = time1.split(':');
        date1 = new Date(date1[0], date1[1] - 1, date1[2], time1[2], time1[1], time1[0]);
        var datetime2 = $(b).find(".year").text();
        var datetimepart2 = datetime2.split(' ');
        var date2 = datetimepart2[0];
        var time2 = datetimepart2[1];
        date2 = date2.split('-');
        time2 = time2.split(':');
        date2 = new Date(date2[0], date2[1] - 1, date2[2], time2[2], time2[1], time2[0]);

        return date1 < date2;
    }
    ;
    $('#testTriN').click(function() {
        $('.row-fluid .span3').sort(sortDescending).appendTo('.row-fluid');
    });


});