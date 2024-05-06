from flask import Flask, request, jsonify
from flask_cors import CORS
import jwt

app = Flask(__name__)
CORS(app)

@app.route('/verify', methods=['GET'])
def verify():
    auth_header = request.headers.get('Authorization')
    if not auth_header:
        return jsonify({'error': 'Token faltante'}), 401

    parts = auth_header.split()

    if parts[0].lower() != 'bearer':
        return jsonify({'error': 'Header de autorización inválido, debe comenzar con "Bearer "'}), 401
    elif len(parts) == 1:
        return jsonify({'error': 'Token faltante'}), 401
    elif len(parts) > 2:
        return jsonify({'error': 'Header de autorización inválido, debe ser "Bearer token"'}), 401

    token = parts[1]

    try:
        payload = jwt.decode(token, 'ARGON2', algorithms=['HS256'])
        user_id = payload['user_id']
        return jsonify({'message': 'Token válido', 'user': user_id}), 200
    except jwt.ExpiredSignatureError:
        return jsonify({'error': 'Token expirado'}), 401
    except jwt.InvalidTokenError:
        return jsonify({'error': 'Token inválido'}), 401

if __name__ == '__main__':
    app.run(debug=True, port=6000)