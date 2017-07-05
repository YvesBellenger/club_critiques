var socket = io.connect('http://chat.club-critiques.dev:3000');

var username = $('#username').val();
var lastName = $('#lastName').val();
var firstName = $('#firstName').val();
var user_id = $('#user_id').val();
var lobby = $('#lobby').val();
var date_start = $('#lobby_date_start').val();
var date_end = $('#lobby_date_end').val();
var room = $('#room').val();

socket.emit('new_user', {"username" : username, "firstName": firstName, "lastName": lastName, "lobby" : lobby, "user_id": user_id, "lobby_date_start": date_start, "lobby_date_end": date_end, "room": room});

socket.on('new_user_room', function(username, firstname, lastname, user_id) {
    $('#chat').append('<p><em>' + username + ' a rejoint le salon !</em></p>');
    $('#list-users').append('<tr id="user-"'+user_id+'>' +
                                '<td>'+ firstname + ' ' + lastname + ' - ' + username + '</td>' +
                                '<td>' +
                                    '<div class="liste-salon-item">' +
                                        '<span>' +
                                                '<a href="">Signaler</a>'+
                                                '<a target="_blank" href="">Profil</a>' +
                                        '</span>' +
                                    '</div>' +
                                '</td>' +
                            '</tr>');
});

socket.on('end_lobby', function(message) {
    $('#chat').append('<p><em>' + message + '</em></p>');
    $('#message').disable();
    $('#send').disable();
});

socket.on('redirect', function() {
   document.location('/');
});

socket.on('message', function(data) {
    updateChat(data.username, data.message)
});

$('#form-chat').submit(function () {
    var message = $('#message').val();
    updateChat('Moi', message);
    socket.emit('message', message);
    $('#message').val('').focus();
    return false;
});

$('#end').click(function () {
    socket.emit('end_lobby');
});

function updateChat(username, message) {
    $('#chat').append('<p><strong>' + username + '</strong> ' + message + '</p>');
}