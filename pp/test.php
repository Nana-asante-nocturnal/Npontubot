<?php
$botName = "NpontuChat";
$features = [
    ['icon' => '💬', 'name' => 'Chat'],
    ['icon' => '❓', 'name' => 'Help'],
    ['icon' => '🧠', 'name' => 'Learn'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $botName; ?> - ChatBot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #040615;
            color: #b3e600;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .container {
            background-color: #0a1128;
            border-radius: 20px;
            padding: 40px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        .ai-avatar {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #b3e600, #8ca500);
            border-radius: 50%;
            margin: 0 auto 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 60px;
            color: #040615;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .chat-input-container {
            display: flex;
            margin: 20px 0;
        }
        .chat-input {
            flex-grow: 1;
            padding: 15px;
            background-color: #0c1a3a;
            border: none;
            border-radius: 30px 0 0 30px;
            color: #b3e600;
        }
        .send-button {
            background-color: #b3e600;
            color: #040615;
            border: none;
            padding: 15px 30px;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .send-button:hover {
            background-color: #8ca500;
        }
        .features {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .feature {
            background-color: #0c1a3a;
            padding: 15px;
            border-radius: 15px;
            width: 22%;
            transition: transform 0.3s;
        }
        .feature:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 30px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ai-avatar">AI</div>
        
        <h1>Welcome to <?php echo $botName; ?>!</h1>
        <p>Your intelligent AI companion</p>
        
        <form class="chat-input-container">
            <input type="text" class="chat-input" placeholder="Type your message here...">
            <button type="submit" class="send-button">Send</button>
        </form>
        
        <div class="features">
            <?php foreach($features as $feature): ?>
                <div class="feature">
                    <div class="feature-icon"><?php echo $feature['icon']; ?></div>
                    <div><?php echo $feature['name']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>