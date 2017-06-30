function receiveMessage(e) {
    socket.emit('new_user', {"username" : e.data['username'], "lobby" : e.data['lobby']});
}

var socket = io.connect('http://chat.club-critiques.dev:3000');
window.addEventListener('message', receiveMessage);

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

function connectToRoomOne(elt){
    var socket = io.connect('http://chat.club-critiques.dev');
}