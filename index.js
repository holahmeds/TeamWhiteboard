var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

app.get('/', function(req, res) {
	res.sendFile(__dirname + '/room.html');
});

var userColor = new Array();
io.on('connection', function(socket) {
	console.log("A user connected");

	userColor[socket] = '#'
			+ Math.floor(Math.random() * Math.pow(2, 24)).toString(16);

	socket.emit('setColor', userColor[socket]);

	socket.on('disconnect', function() {
		console.log("A user disconnected");
		delete userColor[socket];
	});

	socket.on('remoteDraw', function(x1, y1, x2, y2) {
		socket.broadcast.emit('remoteDraw', x1, y1, x2, y2);
	});
});

http.listen(3000, function() {
    console.log("Listening");
});
