from flask import Flask, Blueprint, request, jsonify, abort
from flask_sqlalchemy import SQLAlchemy
from flask_httpauth import HTTPTokenAuth
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
import logging
import requests

# Configuration
class Config:
    SECRET_KEY = "mine_secret_key"
    SQLALCHEMY_DATABASE_URI = "sqlite:///chatbot.db"
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    RASA_SERVER_URL = "http://localhost:5005/model/parse"
    API_KEY = "AIzaSyAt4h_9MZBmzagX-rDpRs2de5vFzw8jkTo"  # Replace with a secure key in production


# Logging setup
logging.basicConfig(filename="chatbot.log", level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")

# Extensions
db = SQLAlchemy()
auth = HTTPTokenAuth(scheme="Bearer")
limiter = Limiter(
    key_func=get_remote_address,  # Use IP address for rate-limiting
    default_limits=["100 per day", "30 per hour"]  # Global rate limits
)


def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)

    # Initialize SQLAlchemy
    db.init_app(app)

    # Attach Flask-Limiter
    limiter.init_app(app)

    # Token Authentication
    @auth.verify_token
    def verify_token(token):
        if token == Config.API_KEY:
            return True
        return False

    # Blueprint for chat routes
    bp = Blueprint("routes", __name__)

    @bp.route("/api/v1/chat", methods=["POST"])
    @auth.login_required
    @limiter.limit("10 per minute")  # Route-specific limit
    def chat():
        # Parse JSON request
        data = request.get_json()
        if not data or "message" not in data:
            abort(400, description="Invalid request payload.")

        user_message = data["message"]

        # Input validation
        if not user_message or len(user_message) > 200:
            abort(400, description="Invalid message format. Must be 1-200 characters.")

        try:
            # Send message to Rasa server
            response = requests.post(Config.RASA_SERVER_URL, json={"text": user_message}, timeout=5)
            response.raise_for_status()  # Raise an error for non-2xx responses
            nlp_response = response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Error contacting Rasa server: {e}")
            abort(500, description="Failed to process message due to internal error.")

        logging.info(f"User message: {user_message} | Rasa response: {nlp_response}")
        return jsonify({"response": nlp_response})

    # Register blueprint
    app.register_blueprint(bp)

    return app


# Entry point
if __name__ == "__main__":
    app = create_app()
    app.run(debug=True, host="0.0.0.0", port=8000)
