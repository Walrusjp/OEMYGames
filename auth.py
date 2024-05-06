from flask import Flask, request, jsonify
from flask_cors import CORS
import hashlib
import requests
import jwt
import datetime

app = Flask(__name__)
CORS(app)

@app.route('/auth', methods=['POST'])
def login():
    data = request.get_json()
    email = data['email']
    password = data['pass']

    if not email or not password:
        return jsonify({'error': 'Faltan datos'}), 400

    hashed_password = hashlib.md5(password.encode()).hexdigest()

    response = requests.get('https://oemygames-default-rtdb.firebaseio.com/usuarios_sistema.json')
    users = response.json()

    for user_id, user_data in users.items():
        if user_data['email'] == email and user_data['pass'] == hashed_password:
            token = jwt.encode({
                'user_id': user_id,
                'exp': datetime.datetime.utcnow() + datetime.timedelta(minutes=5)
            }, 'ARGON2', algorithm='HS256')

            return jsonify({'message': 'Credenciales correctas', 'token': token}), 200

    return jsonify({'error': 'Credenciales incorrectas'}), 401

if __name__ == '__main__':
    app.run(debug=True, port=8000)