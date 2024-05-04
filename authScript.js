document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const loginResult = document.getElementById("login-result");

    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        try {
            const response = await fetch("/PRUEBA/autenticacion", {
                method: "POST",
                headers: {
                    "user": username,
                    "pass": password
                }
            });

            const data = await response.json();
            loginResult.textContent = data.message;

            if (data.status === "success") {
                // Redireccionar a la interfaz de usuario correspondiente según el rol
                if (username === "ventas") {
                    window.location.href = "/ventas.html";
                } else if (username === "almacen") {
                    window.location.href = "/almacen.html";
                }
            }
        } catch (error) {
            console.error("Error during login:", error);
            loginResult.textContent = "Error during login. Please try again.";
        }
    });
});
