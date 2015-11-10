var io = require('socket.io')(3000);
var socketioJwt   = require("socketio-jwt");

io.use(socketioJwt.authorize({
  secret: 'secret_key',
  handshake: true
}));

var userColor = new Array();
io.on('connection', function(socket) {
	console.log(socket.decoded_token.user + " connected");

	userColor[socket] = '#'
			+ Math.floor(Math.random() * Math.pow(2, 24)).toString(16);

	socket.emit('setColor', userColor[socket]);

	socket.on('disconnect', function() {
		console.log(socket.decoded_token.user + " disconnected");
		delete userColor[socket];
	});

	socket.on('remoteDraw', function(x1, y1, x2, y2) {
		socket.broadcast.emit('remoteDraw', x1, y1, x2, y2);
	});
});
