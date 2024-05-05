document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const loginResult = document.getElementById("login-result");

    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const email = document.getElementById("email").value; 
        const password = document.getElementById("password").value;

        try {
            const response = await fetch("/auth", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json" 
                },
                body: JSON.stringify({ 
                    email: email,
                    password: password  
                })
            });

            const data = await response.json();
            loginResult.textContent = data.message;

            if (response.ok) {
                // Redirigir al usuario después de una autenticación exitosa
                window.location.href = "/home.html";
            }
        } catch (error) {
            console.error("Error durante el inicio de sesión:", error);
            loginResult.textContent = "Error durante el inicio de sesión. Por favor, inténtalo de nuevo.";
        }
    });
});
