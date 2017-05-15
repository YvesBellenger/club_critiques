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
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            alert('Error : ' + errorThrown);
        });
}
