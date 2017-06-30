var socket = io.connect('http://chat.club-critiques.dev:3000');

var username = $('#username').val();
var user_id = $('#user_id').val();
var lobby = $('#lobby').val();

socket.emit('new_user', {"username" : username, "lobby" : lobby, "user_id": user_id});

socket.on('new_user', function(username) {
    $('#chat').append('<p><em>' + username + ' a rejoint le salon !</em></p>');
});

socket.on('new_user_room', function(username) {
    $('#chat').append('<p><em>' + username + ' a rejoint la room !</em></p>');
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