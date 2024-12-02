

from flask import Flask, Blueprint, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from pymongo import MongoClient
import pika
import requests

# Configuration (directly added here)
class Config:
    SECRET_KEY = "your_secret_key"  # Replace with a strong secret key
    SQLALCHEMY_DATABASE_URI = "sqlite:///chatbot.db"  # SQLite database
    SQLALCHEMY_TRACK_MODIFICATIONS = False  # Suppress SQLAlchemy warnings

# SQLAlchemy for SQL database
db = SQLAlchemy()

# MongoDB client for NoSQL
try:
    mongo_client = MongoClient("mongodb://localhost:27017/")
    mongo_db = mongo_client["chatbot"]
except Exception as e:
    print(f"MongoDB connection failed: {e}")
    mongo_client, mongo_db = None, None

# RabbitMQ Service
def connect_rabbitmq():
    try:
        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
        channel = connection.channel()
        channel.queue_declare(queue='chat_queue')  # Declare a queue
        return channel
    except pika.exceptions.AMQPConnectionError as e:
        print(f"Failed to connect to RabbitMQ: {e}")
        return None

def publish_message(message):
    channel = connect_rabbitmq()
    if channel:
        channel.basic_publish(exchange='', routing_key='chat_queue', body=message)
        print(f" [x] Sent '{message}'")

# Rasa Integration
RASA_SERVER_URL = "http://localhost:5005/model/parse"

def process_message(user_message):
    try:
        response = requests.post(RASA_SERVER_URL, json={"text": user_message})
        response.raise_for_status()  # Raise exception for HTTP errors
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Error contacting Rasa server: {e}")
        return {"error": "Failed to process message"}

# Routes Blueprint
bp = Blueprint('routes', __name__)

@bp.route('/api/v1/chat', methods=['POST'])
def chat():
    data = request.get_json()
    user_message = data.get("message")

    # Process message with Rasa
    nlp_response = process_message(user_message)

    if mongo_db:
        # Save to MongoDB
        chat_collection = mongo_db["chats"]
        chat_collection.insert_one({"user_message": user_message, "bot_response": nlp_response})

    # Publish to RabbitMQ
    publish_message(user_message)

    return jsonify({"response": nlp_response})

# App Factory
def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)  # Load the configuration from the Config class

    # Initialize SQLAlchemy with the Flask app
    db.init_app(app)

    # Register Blueprints
    app.register_blueprint(bp)

    return app

# Run Script
if __name__ == "__main__":
    app = create_app()
    app.run(debug=True, host="0.0.0.0", port=5000)

# Unit Test
import pytest
from unittest.mock import patch

@pytest.fixture
def client():
    app = create_app()
    app.testing = True
    return app.test_client()

@patch('builtins.print')  # Mock print for testing logs
@patch('__main__.process_message', return_value={"intent": "greet", "confidence": 0.9})
@patch('__main__.publish_message')
@patch('__main__.mongo_db.chats.insert_one')
def test_chat_endpoint(mock_mongo, mock_publish, mock_nlp, mock_print, client):
    response = client.post('/api/v1/chat', json={"message": "Hello"})
    assert response.status_code == 200
    assert "response" in response.json
    mock_nlp.assert_called_once_with("Hello")
    mock_publish.assert_called_once_with("Hello")
    mock_mongo.assert_called_once()
