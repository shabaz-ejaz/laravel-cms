

$(function () {
    $.ajaxSetup({
        headers: {
            'X-XSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });


    $('.select2').select2({placeholder: "Please select an option"});

});
