from flask import Flask, Blueprint, request, jsonify, abort
from flask_sqlalchemy import SQLAlchemy
from pymongo import MongoClient
import pika
import requests
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
from flask_httpauth import HTTPTokenAuth
import logging
import hmac
from redis import Redis

# Configuration
class Config:
    SECRET_KEY = "mine_secret_key"  # Replace with strong secret key
    SQLALCHEMY_DATABASE_URI = "sqlite:///chatbot.db"
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    RASA_SERVER_URL = "http://localhost:5005/model/parse"
    API_KEY = "AIzaSyAt4h_9MZBmzagX-rDpRs2de5vFzw8jkTo"

# Logging Setup
logging.basicConfig(filename="chatbot.log", level=logging.INFO)

# Extensions
db = SQLAlchemy()

# Redis Configuration for Python Anywhere
redis_client = Redis(host='localhost', port=6379, db=0)

# Limiter with Redis Backend
limiter = Limiter(
    key_func=get_remote_address,
    storage_uri="redis://localhost:6379",
    storage_client=redis_client,
    default_limits=["100 per day", "30 per hour"]
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
        channel.queue_declare(queue='chat_queue')
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
    return token == Config.API_KEY

# Chat Routes
bp = Blueprint('routes', __name__)

@bp.route('/api/v1/chat', methods=['POST'])
@auth.login_required
@limiter.limit("10 per minute")
def chat():
    data = request.get_json()
    user_message = data.get("message")
    
    if not user_message or len(user_message) > 200:
        abort(400, description="Invalid message format.")
    
    try:
        response = requests.post(Config.RASA_SERVER_URL, json={"text": user_message})
        response.raise_for_status()
        nlp_response = response.json()
    except requests.exceptions.RequestException as e:
        logging.error(f"Error contacting Rasa server: {e}")
        nlp_response = {"error": "Failed to process message"}
    
    if mongo_db:
        try:
            chat_collection = mongo_db["chats"]
            chat_collection.insert_one({"user_message": user_message, "bot_response": nlp_response})
        except Exception as e:
            logging.error(f"Failed to save to MongoDB: {e}")
    
    publish_message(user_message)
    logging.info(f"Chat processed: {user_message} -> {nlp_response}")
    return jsonify({"response": nlp_response})

# Run Application
def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)
    db.init_app(app)
    limiter.init_app(app)
    app.register_blueprint(bp)
    return app

if __name__ == "__main__":
    app = create_app()
    app.run(debug=True, host="0.0.0.0", port=8000)
