var socket = io.connect('http://chat.club-critiques.dev:3000');

var username = $('#username').val();
var lastName = $('#lastName').val();
var firstName = $('#firstName').val();
var user_id = $('#user_id').val();
var lobby = $('#lobby').val();
var date_start = $('#lobby_date_start').val();
var date_end = $('#lobby_date_end').val();
var room = $('#room').val();
var user_note = $('#user_note').val();

socket.emit('new_user', {"username" : encode_utf8(username), "firstName": encode_utf8(firstName), "lastName": encode_utf8(lastName), "lobby" : lobby, "user_id": user_id, "lobby_date_start": date_start, "lobby_date_end": date_end, "room": room});

socket.on('new_user_room', function(data) {
    $('#chat').append('<p><em>' + data.username + ' a rejoint le salon !</em></p>');
    $('#list-users').empty();
    data.users.forEach(function(user) {
        $('#list-users').append('<tr id="user-'+user.user.user_id+'">' +
            '<td>'+ user.user.firstName + ' ' + user.user.lastName + ' - ' + user.user.username + '</td>' +
            '<td>' + user_note + '/4 </td>' +
            '<td>' +
            '<div class="liste-salon-item">' +
            '<span>' +
            '<a data-participant-id="' + user.user.user_id + '" data-lobby-id="' + user.user.lobby + '" onclick="reportUser(this)" href="javascript:void(0)">Signaler</a> | '+
            '<a target="_blank" href="http://club-critiques.dev/app_dev.php/user/'+user.user.user_id+'">Profil</a>' +
            '</span>' +
            '</div>' +
            '</td>' +
            '</tr>');
    });
});

socket.on('user_disconnect', function(data) {
    $('#user-'+data.user.user_id).remove();
    $('#chat').append('<p><em>' + data.user.username + ' a quitté le salon !</em></p>');

});

socket.on('end_lobby', function(message) {
    $('#chat').append('<p><em>' + message + '</em></p>');
});

socket.on('redirect', function() {
    setTimeout(function(){
        window.location = "/app_dev.php";
    }, 10000);
});

socket.on('message', function(data) {
    updateChat(data.username, data.message)
});

$('#form-chat').submit(function () {
    var message = $('#message').val();
    message = message;
    updateChatUser(message);
    socket.emit('message', message);
    $('#message').val('').focus();
    return false;
});

$('#end').click(function () {
    socket.emit('end_lobby');
});

function updateChat(username, message) {
    $('#chat').append('<p><strong>' + username + '</strong>: ' + message + '</p>');
    scroll_chat();
}

function updateChatUser(message) {
    $('#chat').append('<p class="user-sending"><strong> Moi </strong>: ' + xssFilters.inHTMLData(message) + '</p>');
    scroll_chat();
}

function reportUser(elt) {
    if (confirm('Êtes vous sur de vouloir signaler cet utilisateur ? Tout abus pourra être sanctionné.')) {
        $.ajax({
            type: "POST",
            url: '/app_dev.php/salon/user/report',
            data: {'participant_id': $(elt).data('participant-id'), 'lobby_id': $(elt).data('lobby-id')},
            async: false
        })
        .done(function(response){
            alert('Votre signalement a bien été transmis à l\'administration.');
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Error : ' + errorThrown);
        });
    }
}

function onFilterChange(elt) {
    var timer;
    if ($(elt).attr('id') == 'title') {
        if (timer) {
            clearTimeout(timer);
            timer = setTimeout( sendFiltersRequest, 1000 );
        } else {
            timer = setTimeout( sendFiltersRequest, 1000 );
        }
    } else {
        sendFiltersRequest();
    }
}

function sendFiltersRequest()
{
    $.ajax({
        type: "POST",
        url: '/app_dev.php/salons/filters',
        data: {'category_id': $('#category').val(), 'author_id': $('#author').val(), 'title': $('#title').val(), 'history': $('#history').val()},
        async: true
    })
    .done(function(response){
        $('#lobbies').html( response );
    })
    .fail(function(jqXHR, textStatus, errorThrown){
        alert('Error : ' + errorThrown);
    });
}

function inviteUser(elt) {
    if (confirm('Un email sera envoyé à cet utilisateur afin qu\'il puisse rejoindre vote salon. Voulez vous continuer ?')) {
        $.ajax({
            type: "POST",
            url: '/app_dev.php/salon/user/invitation',
            data: {'contact_id': $(elt).data('contact-id'), 'lobby_id': $(elt).data('lobby-id'), 'room_id': $(elt).data('room-id')},
            async: true
        })
        .done(function(response){
            alert('Un email a bien été envoyé.');
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            alert('Erreur lors de l\'envoi du message');
        });
    }
}

kickClient = function(client) {
    this.socket.emit('kick', client);
};

function encode_utf8(s) {
    return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
    return decodeURIComponent(escape(s));
}

function scroll_chat(){
    var objDiv = document.getElementById("chat");
    objDiv.scrollTop = objDiv.scrollHeight;
}