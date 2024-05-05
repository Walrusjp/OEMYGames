from flask import Flask, request, jsonify
import hashlib
import requests

app = Flask(__name__)

@app.route('/login', methods=['POST'])
def login():

    data = request.get_json()
    email = data['email']
    password = data['pass']
    print(email)
    print(password)

    if not email or not password:
        return jsonify({'error': 'Faltan datos'}), 400

    hashed_password = hashlib.md5(password.encode()).hexdigest()

    response = requests.get('https://oemygames-default-rtdb.firebaseio.com/usuarios_sistema.json')
    users = response.json()

    for user_id, user_data in users.items():
        if user_data['email'] == email and user_data['pass'] == hashed_password:
            # No es token, es placeholder, falta token real
            token = 'token_seguro'
            
            # Enviar token al segundo microservicio, cambiar ruta por la real
            requests.post('http://localhost:xxxx/verification', data={'token': token})
            
            return jsonify({'message': 'Credenciales correctas', 'token': token}), 200

    return jsonify({'error': 'Credenciales incorrectas'}), 401

if __name__ == '__main__':
    app.run(debug=True, port=8000)