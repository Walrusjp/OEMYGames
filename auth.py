from flask import Flask, request, jsonify
import hashlib
import requests

app = Flask(__name__)

# Ruta para la autenticación
@app.route('/auth', methods=['POST'])
def login():
    data = request.get_json()
    email = data.get('email')
    password = data.get('password')

    if not email or not password:
        return jsonify({'error': 'Faltan datos'}), 400

    hashed_password = hashlib.md5(password.encode()).hexdigest()

    response = requests.get('https://oemygames-default-rtdb.firebaseio.com/usuarios_sistema.json')
    users = response.json()

    for user_id, user_data in users.items():
        if user_data.get('email') == email and user_data.get('pass') == hashed_password:
            # Simulación de token seguro
            token = 'token_seguro'
            # Redirigir a la ruta de verificación con el token
            return jsonify({'message': 'Credenciales correctas', 'token': token}), 200

    return jsonify({'error': 'Credenciales incorrectas'}), 401

# Ruta para la verificación del token
@app.route('/verification', methods=['POST'])
def verify_token():
    data = request.get_json()
    token = data.get('token')

    return jsonify({'message': 'Token verificado correctamente'}), 200

if __name__ == '__main__':
    app.run(debug=True, port=8000)
