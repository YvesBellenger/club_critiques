function loadMore(elt) {
    var offset = $(elt).data('offset');
    $.ajax({
        type: "POST",
        url: 'users/loadMore',
        data: {'offset': offset},
        async: false
    })
        .done(function(response){
            $('#user-list').append(response);
            $('#loadMore').data('offset', offset+8);
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Error : ' + errorThrown);
        });
}