document.addEventListener("DOMContentLoaded", () => {
    const productForm = document.getElementById("product-form-ventas");
    const productDetails = document.getElementById("product-details-ventas");
    const showProductsButton = document.getElementById("show-products-ventas");

    productForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const productName = document.getElementById("product-name-ventas").value;
        const productCategory = document.getElementById("product-category-ventas").value;

        // Consultar producto
        const response = await fetch(`/PRUEBA/productos/${productCategory}`);
        const data = await response.json();

        if (data.status === "success") {
            const product = data.data[productName];
            if (product) {
                const detailsHTML = `
                    <h4>ID: ${product.ID}</h4>
                    <p>Nombre: ${product.Nombre}</p>
                    <p>Desarrollador: ${product.Desarrollador}</p>
                    <p>Consola: ${product.Consola}</p>
                    <p>Fecha: ${product.Fecha}</p>
                    <p>Precio: ${product.Precio}</p>
                    <p>Descuento: ${product.Descuento}</p>
                `;
                productDetails.innerHTML = detailsHTML;
            } else {
                productDetails.innerHTML = `<p>Error: Producto no encontrado</p>`;
            }
        } else {
            productDetails.innerHTML = `<p>Error: ${data.message}</p>`;
        }
    });

    // Evento para el botón "Mostrar Productos"
    showProductsButton.addEventListener("click", async () => {
        const response = await fetch("/PRUEBA/productos/");
        const data = await response.json();

        if (data.status === "success") {
            const productListItems = Object.values(data.data).map(product => `<li>${product}</li>`);
            productDetails.innerHTML = productListItems.join("");
        } else {
            productDetails.innerHTML = `<p>Error: ${data.message}</p>`;
        }
    });
});
