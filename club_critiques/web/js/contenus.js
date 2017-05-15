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
