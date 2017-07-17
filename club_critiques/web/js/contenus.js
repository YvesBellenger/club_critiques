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
        data: {'sub_category_id': $('#sub-category').val(), 'category_id': $('#category').val(), 'author_id': $('#author').val(), 'title': $('#title').val(), 'publishedDate': $('#orderBy').val()},
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

/*function sharedContent(elt) {
    $.ajax({
        type: "POST",
        url: 'content/shared/add',
        data: {'content_id': $(elt).data('content-id'), 'type': $(elt).data('type')},
        async: false
    })
        .done(function(response){
            alert('le contenu a bien été ajouté');
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Error : ' + errorThrown);
        });
}*/


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

function toShareMail(elt){
    var content_title = $(elt).data('content-title');
    var content_authors = $(elt).data('content-authors');
    var user_prenom = $(elt).data('user-prenom');
    var user_nom = $(elt).data('user-nom');
    var contact_subject_input = $('#contact_subject');
    var contact_message_input = $('#contact_message');

    var subject = "Je souhaite vous emprunter " + content_title;
    var message = "Bonjour, \n \n";

    message += "Voyant que vous mettez à disposition ce livre, je souhaiterais vous emprunter " + content_title + " de "+content_authors+".\n\n";
    message += 'Merci. \n\n';
    message += 'Cordialement, \n';
    message += user_prenom + " " + user_nom;


    contact_subject_input.val(subject);
    contact_message_input.val(message);
    $(window).scrollTop( $("#contact-form").offset().top);
}

function wantedMail(elt){
    var content_title = $(elt).data('content-title');
    var content_authors = $(elt).data('content-authors');
    var user_prenom = $(elt).data('user-prenom');
    var user_nom = $(elt).data('user-nom');
    var contact_subject_input = $('#contact_subject');
    var contact_message_input = $('#contact_message');

    var subject = "Je souhaite vous prêter " + content_title;
    var message = "Bonjour, \n \n";
    message += "Voyant que vous mettez à disposition ce livre, je souhaiterais vous emprunter " + content_title + " de "+content_authors+".\n\n";
    message += 'Merci. \n\n';
    message += 'Cordialement, \n';
    message += user_prenom + " " + user_nom;


    contact_subject_input.val(subject);
    contact_message_input.val(message);
    $(window).scrollTop( $("#contact-form").offset().top);
}

function scroll_form(){
    var objDiv = document.getElementById("contact-form");
    objDiv.scrollTop = objDiv.scrollHeight;

}