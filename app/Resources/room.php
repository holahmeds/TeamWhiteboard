<?php
    use Firebase\JWT\JWT;

    // TODO move this to a config file
    $key = "secret_key";

    $token = array(
        'user' => $user,
        'roomID' => $roomID
        );

    $jwt = JWT::encode($token, $key);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />

        <title>Room</title>

        <style>
            body {
                margin: 0;
                border-width: 0;
                padding: 0;
            }
        </style>

        <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
        <script src="http://code.jquery.com/jquery-1.11.1.js"></script>
    </head>

    <body>
        <canvas id="canvas" width="1000px" height="500px" style="background: #CCCCCC;">
            Browser does not support canvas.
        </canvas>
        <div><p>Room Id: <?php echo $roomID ?></p></div>
        <div><p>Username: <?php echo $user ?></p></div>
        <div><p>JWT: <?php echo $jwt ?></p></div>
    </body>

    <script>
        var lastX,
            lastY;
        var leftMouseDown = false,
            rightMouseDown = false;
        var LEFT_MOUSE = 0,
            MIDDLE_MOUSE = 1,
            RIGHT_MOUSE = 2;
        
        var socket = io('http://localhost:3000', { 'query': 'token=<?php echo $jwt; ?>' });
        
        var ctx = $('#canvas')[0].getContext('2d');
        ctx.lineWidth = 4;
        var color = '#000000';
        
        socket.on('setColor', function(col) {
        	color = col;
        });
        
        socket.on('remoteDraw', function(x1, y1, x2, y2) {
        	ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.strokeStyle = '#000000';
            ctx.stroke();
        });

        function handleStart(evt) {
            evt.preventDefault();
            
            switch (evt.button) {
            case LEFT_MOUSE:
                leftMouseDown = true;
                break;
            case RIGHT_MOUSE:
                rightMouseDown = true;
                break;
            }

            if (leftMouseDown) {
                ctx.beginPath();
                // a circle at the start
                ctx.arc(evt.clientX, evt.clientY, 4, 0, 2 * Math.PI, false);
                ctx.fillStyle = color;
                ctx.fill();
            }

            lastX = evt.clientX;
            lastY = evt.clientY;
        }

        function handleMove(evt) {
            if (leftMouseDown) {
                evt.preventDefault();
                
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(evt.clientX, evt.clientY);
                ctx.strokeStyle = color;
                ctx.stroke();

                socket.emit('remoteDraw', lastX, lastY, evt.clientX, evt.clientY);
                
                lastX = evt.clientX;
                lastY = evt.clientY;
            }
        }

        function handleEnd(evt) {
            evt.preventDefault();
            
            if (leftMouseDown) {
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(evt.clientX, evt.clientY);
                // and a square at the end
                ctx.fillStyle = color;
                ctx.fillRect(evt.clientX - 4, evt.clientY - 4, 8, 8);
            }
            
            switch (evt.button) {
            case LEFT_MOUSE:
                leftMouseDown = false;
                break;
            case RIGHT_MOUSE:
                rightMouseDown = false;
                break;
            }
        }

        var el = $("#canvas");
        el.mousedown(handleStart);
        el.mouseup(handleEnd);
        el.mousemove(handleMove);
        console.log("Added Event Listeners");
    </script>

</html>
