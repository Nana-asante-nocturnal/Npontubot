<?php
$botName = "NpontuChat";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $botName; ?> - Chat UI</title>
  <style>
     body {
      font-family: Arial, sans-serif;
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: url('bg.png') no-repeat center center fixed;
      background-size: cover;
      position: relative;
    }

    .chat-popup {
      position: fixed;
      bottom: 20px;
      right: 20px;
    }

    .chat-button {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #007bff;
  color: white;
  border: none;
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      background-color: #000;
      color: #fff;
    }
    .navbar img {
      width: 50px;
      height: 50px;
      border-radius: 10px;
    }
    .navbar ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .navbar ul li {
      margin: 0 15px;
    }
    .navbar ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 16px;
    }
    .navbar ul li a:hover {
      color: #00ff00;
    }
    .navbar .cta-button {
      background-color: #fff;
      color: #097969;
      border: 1px solid transparent;
      border-radius: 5px;
      padding: 8px 16px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
    }
    .navbar .cta-button:hover {
      background-color: #000;
      border: 1px solid #fff;
      color: #097969;
    }
    .chat-container {
  display: none;
  width: 400px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  flex-direction: column;
  overflow: hidden;
  position: absolute;
  bottom: 60px;
  right: 0;
  margin-right: 10px;
  z-index: 1000;
}
    .chat-header {
      display: flex;
      align-items: center;
      padding: 10px;
      background-color: #007bff;
      color: #fff;
    }
    .chat-header img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    .chat-header span {
      margin-left: 10px;
    }
    .chat-content {
  padding: 15px;
  height: 300px;
  max-height: 500px;
  overflow-y: auto;
  background-color: #f9f9f9;
}
    .chat-bubble {
      max-width: 80%;
      padding: 10px;
      margin: 5px 0;
      border-radius: 10px;
      word-wrap: break-word;
    }
    .user-message {
      background-color: #007bff;
      color: #fff;
      align-self: flex-end;
    }
    .bot-message {
      background-color: #e0e0e0;
      color: #333;
      align-self: flex-start;
    }
    .chat-buttons-container {
      display: flex;
      justify-content: center;
      padding: 10px;
      background-color: #f5f5f5;
      border-bottom: 1px solid #ddd;
    }
    .chat-button {
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      cursor: pointer;
      font-size: 14px;
      margin: 0 5px;
    }
    .chat-button:hover {
      background-color: #0056b3;
    }
    .chat-input-container {
  display: flex;
  align-items: center;
  padding: 10px;
  border-top: 1px solid #ddd;
  background-color: #f5f5f5;
}
.chat-input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 20px;
}

    .voice-input-button {
      background: none;
      border: none;
      cursor: pointer;
      color: #007bff;
      font-size: 20px;
      margin-left: 10px;
    }
    .voice-input-button:disabled {
      color: #cccccc;
      cursor: not-allowed;
    }
    .voice-status {
      font-size: 12px;
      color: #666;
      margin-left: 10px;
    }

    .send-button {
      margin-left: 10px;
      background: none;
      border: none;
      cursor: pointer;
      color: #007bff;
      font-size: 20px;
    }
    .powered-by-text {
      text-align: center;
      color: #555;
      font-size: 12px;
      margin: 10px 0;
    }
  </style>

<script>
  function toggleChat() {
  const chatContainer = document.querySelector('.chat-container');
  if (!chatContainer) {
    console.error('Chat container element not found.');
    return;
  }

  if (chatContainer.style.display === 'none' || chatContainer.style.display === '') {
    chatContainer.style.display = 'block';
  } else {
    chatContainer.style.display = 'none';
  }
}
</script>


</head>
<body>
  <!-- Navigation Bar -->
  <div class="navbar">
    <img src="npontuLogo.png" alt="Npontu Technologies Logo">
    <ul>
      <li><a href="#">Home</a></li>
      <li><a href="#">Company</a></li>
      <li><a href="#">Services</a></li>
      <li><a href="#">Resources</a></li>
      <li><a href="#">Partners</a></li>
      <li><a href="#">Blog</a></li>
      <li><a href="#">Jobs & Careers</a></li>
    </ul>
    <a href="#" class="cta-button">Let’s Talk</a>
  </div>

 <!-- Chat Popup Button -->
 <div class="chat-popup">
    <button class="chat-button" onclick="toggleChat()">💬</button>
  </div>
    
  <!-- Chat UI -->
  <div class="chat-container">
    <div class="chat-header">
      <img src="picture.png" alt="Profile">
      <span><?php echo $botName; ?></span>
    </div>
    <div class="chat-buttons-container">
      <button class="chat-button" onclick="handleButtonClick('FAQ')">FAQ</button>
      <button class="chat-button" onclick="handleButtonClick('Help')">Help</button>
    </div>
    <div class="chat-content">
      <div class="chat-bubble bot-message">
        Welcome! Please select an option above to get started:
      </div>
    </div>
    <div class="chat-input-container">
      <input type="text" class="chat-input" placeholder="Type a message...">
      <button class="send-button">&#9993;</button>
      <button class="voice-input-button" aria-label="Voice Input">🎤</button>
    <span class="voice-status" aria-live="polite"></span>
    </div>
    <div class="powered-by-text">Powered by Npontu</div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const input = document.querySelector('.chat-input');
      const sendButton = document.querySelector('.send-button');
      const voiceButton = document.querySelector('.voice-input-button');
      const voiceStatus = document.querySelector('.voice-status');
      const chatContent = document.querySelector('.chat-content');
      const chatContainer = document.getElementById('chatContainer');


      // Speech Recognition Setup
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      let recognition = null;

      // Check if browser supports speech recognition
      if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US'; // You can change this to support other languages

        recognition.onstart = () => {
          voiceStatus.textContent = 'Listening...';
          voiceButton.disabled = true;
        };

        recognition.onresult = (event) => {
          const speechResult = event.results[0][0].transcript;
          input.value = speechResult;
          voiceStatus.textContent = '';
          voiceButton.disabled = false;
        };

        recognition.onerror = (event) => {
          voiceStatus.textContent = 'Error occurred in recognition: ' + event.error;
          voiceButton.disabled = false;
        };

        recognition.onend = () => {
          voiceStatus.textContent = '';
          voiceButton.disabled = false;
        };
      } else {
        // Disable voice input if not supported
        voiceButton.disabled = true;
        voiceStatus.textContent = 'Voice input not supported';
      }

      // Voice input button click handler
      voiceButton.addEventListener('click', () => {
        if (recognition) {
          try {
            recognition.start();
          } catch (error) {
            voiceStatus.textContent = 'Error starting voice recognition: ' + error;
          }
        }
      });

      const addMessage = (message, isUser) => {
        const bubble = document.createElement('div');
        bubble.classList.add('chat-bubble');
        bubble.classList.add(isUser ? 'user-message' : 'bot-message');
        bubble.textContent = message;

        chatContent.appendChild(bubble);
        chatContent.scrollTop = chatContent.scrollHeight;
      };

      const handleButtonClick = (option) => {
        addMessage(`You selected: ${option}`, true);
        addMessage(`Let me assist you with ${option}...`, false);
      };

      const sendMessage = async () => {
        const message = input.value.trim();
        if (!message) return;

        // Add user message
        addMessage(message, true);
        input.value = '';

        // Show loading indicator
        const loadingBubble = document.createElement('div');
        loadingBubble.classList.add('chat-bubble', 'bot-message');
        loadingBubble.textContent = '...';
        chatContent.appendChild(loadingBubble);

        try {
          // Example API request - replace with your actual logic
          const response = await fetch("https://example.com/api", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message }),
          });

          const data = await response.json();

          chatContent.removeChild(loadingBubble);
          addMessage(data.reply || "Sorry, I couldn't understand that.", false);
        } catch (error) {
          // Handle errors
          chatContent.removeChild(loadingBubble);
          addMessage("An error occurred. Please try again later.", false);
          console.error("Error:", error);
        }
      };

      // Event listeners for sending messages
      sendButton.addEventListener('click', sendMessage);
      input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          sendMessage();
        }
      });

      



      // Expose handleButtonClick to global scope for inline onclick
      window.handleButtonClick = handleButtonClick;
    });
  </script>


