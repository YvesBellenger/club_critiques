var timer;
function timeOutInput(elt) {
    if (timer) {
        clearTimeout(timer);
        timer = setTimeout( onFiltersChange, 1000 );
    } else {
        timer = setTimeout( onFiltersChange, 1000 );
    }
}
function onFiltersChange(elt) {
    $.ajax({
        type: "POST",
        url: 'contenus/filters',
        data: {'sub_category_id': $('#sub-category').val(), 'category_id': $('#category').val(), 'author_id': $('#author').val(), 'title': $('#title').val(), 'publishedDate': $('#publishedDate').val()},
        async: false
    })
        .done(function(response){
            $('#contents').html( response );
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Error : ' + errorThrown);
        });
}

function onAddContent(elt) {
    $.ajax({
        type: "POST",
        url: 'content/add',
        data: {'content_id': $(elt).data('content-id'), 'type': $(elt).data('type')},
        async: false
    })
        .done(function(response){
            alert('le contenu a bien été ajouté');
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Error : ' + errorThrown);
        });
}

function loadMore(elt) {
    var offset = $(elt).data('offset');
    $.ajax({
        type: "POST",
        url: 'contenus/loadMore',
        data: {'offset': offset, 'filters':  {'sub_category_id': $('#sub-category').val(), 'category_id': $('#category').val(), 'author_id': $('#author').val(), 'title': $('#title').val(), 'publishedDate': $('#publishedDate').val()}},
        async: false
    })
    .done(function(response){
        $('#content-list').append(response);
        $('#loadMore').data('offset', offset+8);
    })
    .fail(function(jqXHR, textStatus, errorThrown){
        alert('Error : ' + errorThrown);
    });
}
