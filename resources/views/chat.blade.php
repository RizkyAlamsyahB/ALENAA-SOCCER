<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Community Chat - SportVue</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #9E0620;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .chat-box {
            height: 500px;
            overflow-y: scroll;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            padding: 15px;
        }

        .message {
            margin-bottom: 15px;
        }

        .message .sender {
            font-weight: 600;
            color: var(--primary-color);
        }

        .message .text {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
        }

        .message .time {
            font-size: 0.8rem;
            color: #aaa;
        }
    </style>
</head>

<body>
    @include('partials.navbar')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-secondary">Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page">Community Chat</li>
            </ol>
        </div>
    </nav>

    <!-- Community Chat Section -->
    <div class="container py-5">
        <h3 class="text-center mb-4">Community Chat</h3>
        <div class="row">
            <!-- Chat Box -->
            <div class="col-md-8 mx-auto">
                <div class="chat-box" id="chat-box">
                    <!-- Chat messages will be appended here -->
                </div>
                <div class="input-group mt-3">
                    <input type="text" id="chat-input" class="form-control" placeholder="Type your message..." />
                    <button class="btn btn-danger" id="send-button"><i class="fas fa-paper-plane"></i> Send</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    {{-- @include('partials.footer') --}}

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const chatBox = document.getElementById('chat-box');
        const chatInput = document.getElementById('chat-input');
        const sendButton = document.getElementById('send-button');

        // Simulate chat messages
        const addMessage = (sender, text, time = new Date()) => {
            const message = document.createElement('div');
            message.classList.add('message');

            const messageHTML = `
                <div class="sender">${sender}</div>
                <div class="text">${text}</div>
                <div class="time">${time.toLocaleTimeString()}</div>
            `;

            message.innerHTML = messageHTML;
            chatBox.appendChild(message);

            // Auto-scroll to the bottom of the chat box
            chatBox.scrollTop = chatBox.scrollHeight;
        };

        // Send message event
        sendButton.addEventListener('click', () => {
            const messageText = chatInput.value.trim();
            if (messageText) {
                addMessage('You', messageText);
                chatInput.value = '';

                // Simulate a response from another user
                setTimeout(() => {
                    addMessage('Opponent Finder Bot', 'Looking for an opponent...');
                }, 1000);
            }
        });
    </script>
</body>

</html>
