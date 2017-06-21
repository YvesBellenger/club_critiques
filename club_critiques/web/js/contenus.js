function onChangeCategory(elt) {
    $.ajax({
        type: "POST",
        url: 'contenus/filters',
        data: {'category_id': $(elt).val()},
        async: false
    })
        .done(function(response){
            $('#contents').html( response );
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Error : ' + errorThrown);
        });
}

function onChangeSubCategory(elt) {
    $.ajax({
        type: "POST",
        url: 'contenus/filters',
        data: {'sub_category_id': $(elt).val(), 'category_id': $('#category').val()},
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

function onRemoveContent(elt) {
    $.ajax({
        type: "POST",
        url: 'content/remove',
        data: {'content_id': $(elt).data('content-id'), 'type': $(elt).data('type')},
        async: false
    })
        .done(function(response){
            alert('le contenu a bien été supprimé');
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Error : ' + errorThrown);
        });
}
