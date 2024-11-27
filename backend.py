
from flask import Flask, Blueprint, request, jsonify, abort
from flask_sqlalchemy import SQLAlchemy
from pymongo import MongoClient
import pika
import requests
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
from flask_httpauth import HTTPTokenAuth
import logging
import hmac  # Replacing safe_str_cmp for secure comparison
from redis import Redis  # For rate limiter storage

# Configuration
class Config:
    SECRET_KEY = "mine_secret_key"  # Replace with strong secret key 2$#&+@abikayuews&
    SQLALCHEMY_DATABASE_URI = "sqlite:///chatbot.db"  # SQLite database
    SQLALCHEMY_TRACK_MODIFICATIONS = False  # Suppress SQLAlchemy warnings
    RASA_SERVER_URL = "http://localhost:5005/model/parse"  # Rasa server URL
    API_KEY = "AIzaSyAt4h_9MZBmzagX-rDpRs2de5vFzw8jkTo"  # API key for authentication

# Logging Setup
logging.basicConfig(filename="chatbot.log", level=logging.INFO)

# Extensions
db = SQLAlchemy()  # Single instance of SQLAlchemy
limiter = Limiter(
    get_remote_address,
    storage_uri="redis://localhost:6379"  # Replace with localhost Redis URI
)
auth = HTTPTokenAuth(scheme="Bearer")

# MongoDB client
try:
    mongo_client = MongoClient("mongodb://localhost:27017/")
    mongo_db = mongo_client["chatbot"]
except Exception as e:
    logging.error(f"MongoDB connection failed: {e}")
    mongo_client, mongo_db = None, None

# RabbitMQ Integration
def connect_rabbitmq():
    try:
        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
        channel = connection.channel()
        channel.queue_declare(queue='chat_queue')  # Declare a queue
        return channel
    except pika.exceptions.AMQPConnectionError as e:
        logging.error(f"Failed to connect to RabbitMQ: {e}")
        return None

def publish_message(message):
    channel = connect_rabbitmq()
    if channel:
        channel.basic_publish(exchange='', routing_key='chat_queue', body=message)
        logging.info(f"Message published to RabbitMQ: {message}")

# Authentication
@auth.verify_token
def verify_token(token):
    if token == Config.API_KEY:  # Verify if the token matches the configured API key
        return True
    return False

# Chat Routes
bp = Blueprint('routes', __name__)

@bp.route('/api/v1/chat', methods=['POST'])
@auth.login_required
@limiter.limit("10 per minute")
def chat():
    data = request.get_json()
    user_message = data.get("message")

    # Validate Input
    if not user_message or len(user_message) > 200:
        abort(400, description="Invalid message format.")

    # Send to Rasa
    try:
        response = requests.post(Config.RASA_SERVER_URL, json={"text": user_message})
        response.raise_for_status()
        nlp_response = response.json()
    except requests.exceptions.RequestException as e:
        logging.error(f"Error contacting Rasa server: {e}")
        nlp_response = {"error": "Failed to process message"}

    # Save to MongoDB
    if mongo_db:
        try:
            chat_collection = mongo_db["chats"]
            chat_collection.insert_one({"user_message": user_message, "bot_response": nlp_response})
        except Exception as e:
            logging.error(f"Failed to save to MongoDB: {e}")

    # Publish to RabbitMQ
    publish_message(user_message)

    logging.info(f"Chat processed: {user_message} -> {nlp_response}")
    return jsonify({"response": nlp_response})

# Run Application
def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)

    # Initialize extensions with the app
    db.init_app(app)
    limiter.init_app(app)

    # Register Blueprints
    app.register_blueprint(bp)
    return app

# Ensure the app is created before running it
if __name__ == "__main__":
    app = create_app()  # This ensures the app is defined before running
    app.run(debug=True, host="0.0.0.0", port=8000)
