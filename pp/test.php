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
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), 
                  url('bg.png') no-repeat center center fixed; /* Background image with 75% opacity overlay */
      background-size: cover; /* Ensure the image covers the entire viewport */
    }
    .chat-container {
      width: 360px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
      display: flex;
      flex-direction: column;
      overflow: hidden;
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
      margin-left: 10px; /* Added spacing for bot name */
    }
    .chat-content {
      flex: 1;
      padding: 15px;
      overflow-y: auto;
      background-color: #f9f9f9;
      min-height: 300px; /* Set a minimum height */
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }
    .chat-bubble {
      max-width: 80%;
      padding: 10px;
      margin: 5px 0;
      border-radius: 10px;
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
      outline: none;
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
      color: #555; /* Subtle black color */
      font-size: 12px;
      margin: 10px 0;
    }
  </style>
</head>
<body>
  <div class="chat-container">
    <div class="chat-header">
      <img src="picture.png" alt="Profile">
      <span><?php echo $botName; ?></span>
    </div>
    <div class="chat-content">
      <!-- Chat messages dynamically added here -->
    </div>
    <div class="chat-input-container">
      <input type="text" class="chat-input" placeholder="Type a message...">
      <button class="send-button">&#9993;</button> <!-- Paper plane icon -->
    </div>
    <div class="powered-by-text">Powered by Npontu</div>
  </div>
</body>
</html>
