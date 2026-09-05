            const urlParams = new URLSearchParams(window.location.search);
            const nombre = urlParams.get('usuario');
            const direccion = urlParams.get('direccion');
            const telefono = urlParams.get('telefono');
            const pedido = [];

            document.getElementById('nombre').textContent += nombre;
            document.getElementById('direccion').textContent += direccion;
            document.getElementById('telefono').textContent += telefono;


            if (urlParams.get('tipo-pizza-nap') === 'napolitana') {
                pedido.push(`Napolitana: ${urlParams.get('cantidad-pizza-nap')}`);
            }
            if (urlParams.get('tipo-pizza-jq') === 'jamon-queso') {
                pedido.push(`Jamon y queso: ${urlParams.get('cantidad-pizza-jq')}`);
            }
            if (urlParams.get('tipo-pizza-muz') === 'muzzarela') {
                pedido.push(`Muzzarela: ${urlParams.get('cantidad-pizza-muz')}`);
            }

            document.getElementById('pedido').textContent += pedido.join(', ');