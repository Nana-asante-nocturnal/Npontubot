from flask import Flask, Blueprint, request, jsonify, abort
from flask_sqlalchemy import SQLAlchemy
from flask_httpauth import HTTPTokenAuth
import logging
import requests

class Config:
    SECRET_KEY = "mine_secret_key"
    SQLALCHEMY_DATABASE_URI = "sqlite:///chatbot.db"
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    RASA_SERVER_URL = "http://localhost:5005/model/parse"
    API_KEY = "AIzaSyAt4h_9MZBmzagX-rDpRs2de5vFzw8jkTo"

logging.basicConfig(filename="chatbot.log", level=logging.INFO)

db = SQLAlchemy()
auth = HTTPTokenAuth(scheme="Bearer")

def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)
    
    db.init_app(app)
    
    # Remove global limiter
    from flask_limiter import Limiter
    from flask_limiter.util import get_remote_address
    
    limiter = Limiter(
        app=app, 
        key_func=get_remote_address,
        default_limits=["100 per day", "30 per hour"]
    )

    @auth.verify_token
    def verify_token(token):
        return token == Config.API_KEY

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
            nlp_response = response.json()
        except Exception as e:
            logging.error(f"Error processing message: {e}")
            nlp_response = {"error": "Failed to process message"}
        
        logging.info(f"Chat processed: {user_message}")
        return jsonify({"response": nlp_response})

    app.register_blueprint(bp)
    return app

if __name__ == "__main__":
    app = create_app()
    app.run(debug=True, host="0.0.0.0", port=8000)
