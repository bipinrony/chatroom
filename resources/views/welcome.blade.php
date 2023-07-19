<!DOCTYPE html>

<head>
    <title>Pusher Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('0a64c8bce1f073ae57b3', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('chat-room');
        channel.bind('my-event', function(data) {
            console.log(data.message);
            refreshChat();
        });
    </script>

    <style>
        #chatroom {
            padding: 30px;
            border: 1px solid lightgrey;
            background: white;
        }

        .date {
            color: #b3b3b3;
        }

        #messages {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid lightgrey;
        }
    </style>
</head>

<body>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-6">
                <div id="chatroom" class="rounded">
                    <h1>CHAT-ROOM</h1>

                    <div id="messages">
                        <ul class="list-unstyled" id="list">
                        </ul>

                        <button class="btn btn-danger" id="refresh">Refresh Chat</button>
                    </div>

                    <form action="#" id="comment-form">

                        <div class="form-group">
                            <label for="your-message">Your comment</label>
                            <textarea type="text" name="content" id="your-message" class="form-control" placeholder="Here is my message.."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="your-name">From</label>
                            <input type="text" name="content" id="your-name" class="form-control" placeholder="Bob">
                        </div>
                        <input type="submit" value="Send" class="btn btn-primary" id="submit">
                    </form>



                </div>
            </div>
        </div>
    </div>

    <script>
        const batch = 533;
        // using endpoint ':channel/messages'
        const baseUrl = "{{ url('api/messages') }}";

        // selecting the elements
        const refreshBtn = document.getElementById("refresh");
        const listOfMessages = document.getElementById("list");
        const comment = document.getElementById("your-message");
        const sender = document.getElementById("your-name");
        const submitBtn = document.getElementById("submit");

        // http GET request to refresh the list of comments
        const refreshChat = () => {
            fetch(baseUrl)
                .then(response => response.json())
                .then((data) => {
                    // to clean the list and avoid repetition
                    listOfMessages.innerHTML = "";
                    // digging into the json
                    const messages = data.messages;
                    messages.forEach((message) => {
                        const content = message.message;
                        const author = message.username;
                        const minutesAgo = Math.round((new Date() - new Date(message.datetime)) / 60000);
                        const fullMessage =
                            `<li>${content} (posted <span class="date">${minutesAgo} minutes ago</span>) by ${author}</li>`;
                        listOfMessages.insertAdjacentHTML("afterbegin", fullMessage);
                    });
                });
        };

        refreshBtn.addEventListener("click", refreshChat);

        // http POST request to write messages, send them to the API and display them in the chat
        const postMessage = () => {
            const myMessage = {
                username: sender.value,
                message: comment.value
            };
            console.log(myMessage);
            fetch("{{ url('api/send-message') }}", {
                    method: "POST",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(myMessage)
                })
                // parse response as a json
                .then(response => response.json())
                .then((data) => {
                    refreshChat();
                });
        };

        submitBtn.addEventListener("click", (event) => {
            // avoid the default behavior of page loading
            event.preventDefault();
            postMessage();
        });

        // refresh the app automatically
        document.addEventListener("DOMContentLoaded", refreshChat);
    </script>

</body>
