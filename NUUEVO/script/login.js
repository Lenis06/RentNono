//LOGICA DE VENTANAS FLOTANTES (INICIO DE SESION Y REGISTRO DE USUARIOS)

    const abrirLogin = document.getElementById('abrirLogin');
    const cerrarLogin = document.getElementById('cerrarLogin');
    const modalFondoLogin = document.getElementById('modalFondoLogin');

    const abrirRegistroPropietario = document.getElementById('abrirRegistroPropietario');
    const cerrarRegistroPropietario = document.getElementById('cerrarRegistroPropietario');
    const modalFondoRegistroPropietario = document.getElementById('modalFondoRegistroPropietario');

    const abrirRegistroVisitante = document.getElementById('abrirRegistroVisitante');
    const cerrarRegistroVisitante = document.getElementById('cerrarRegistroVisitante');
    const modalFondoRegistroVisitante = document.getElementById('modalFondoRegistroVisitante');

    // Abrir y cerrar Login
    abrirLogin.onclick = () => modalFondoLogin.style.display = 'flex';
    cerrarLogin.onclick = () => modalFondoLogin.style.display = 'none';

    // Abrir Registro Propietario y cerrar Login
    abrirRegistroPropietario.onclick = () => {
        modalFondoLogin.style.display = 'none';   // Cierra Login
        modalFondoRegistroPropietario.style.display = 'flex'; // Abre Registro
    };
    cerrarRegistroPropietario.onclick = () => modalFondoRegistroPropietario.style.display = 'none';

    // Abrir Registro Visitante y cerrar Login
    abrirRegistroVisitante.onclick = () => {
        modalFondoLogin.style.display = 'none';   // Cierra Login
        modalFondoRegistroVisitante.style.display = 'flex'; // Abre Registro
    };
    cerrarRegistroVisitante.onclick = () => modalFondoRegistroVisitante.style.display = 'none';

    // Cerrar modales al hacer click fuera
    window.addEventListener('click', (e) => {
        if(e.target === modalFondoLogin) modalFondoLogin.style.display = 'none';
        if(e.target === modalFondoRegistroPropietario) modalFondoRegistroPropietario.style.display = 'none';
        if(e.target === modalFondoRegistroVisitante) modalFondoRegistroVisitante.style.display = 'none';
    });
    fetch("publicaciones.php?ajax=1&" + query)

    // script/user-menu.js

// Manejo del menú desplegable de usuario
document.addEventListener('DOMContentLoaded', function() {
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userDropdown) {
        const dropdownBtn = userDropdown.querySelector('.user-dropdown-btn');
        const dropdownMenu = userDropdown.querySelector('.user-dropdown-menu');
        
        // Alternar menú al hacer clic
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
        
        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
        
        // Cerrar menú al hacer clic en un elemento
        dropdownMenu.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                setTimeout(() => {
                    userDropdown.classList.remove('active');
                }, 300);
            }
        });
        
        // Cerrar menú con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userDropdown.classList.remove('active');
            }
        });
    }
});

// Función para cambiar contraseña
function cambiarContrasena() {
    const modal = document.createElement('div');
    modal.className = 'modal-fondo';
    modal.innerHTML = `
        <div class="modal-contenido" style="max-width: 400px;">
            <span class="cerrar" onclick="this.parentElement.parentElement.remove()">&times;</span>
            <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
            
            <form id="formCambiarPassword" onsubmit="return cambiarPasswordSubmit(event)">
                <div class="input-group">
                    <div class="input-container">
                        <input type="password" id="currentPassword" placeholder="Contraseña actual" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="input-container">
                        <input type="password" id="newPassword" placeholder="Nueva contraseña" required minlength="6">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="input-container">
                        <input type="password" id="confirmPassword" placeholder="Confirmar nueva contraseña" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-registrar">
                        <i class="fas fa-save"></i> Cambiar Contraseña
                    </button>
                    <button type="button" class="btn-volver" onclick="this.closest('.modal-fondo').remove()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
}

function cambiarPasswordSubmit(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        alert('Las contraseñas no coinciden');
        return false;
    }
    
    if (newPassword.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
    
    // Aquí iría la llamada AJAX para cambiar la contraseña
    alert('Función de cambio de contraseña en desarrollo');
    
    // Cerrar modal
    e.target.closest('.modal-fondo').remove();
    return false;
}

// Función para confirmar eliminación de cuenta
function confirmarEliminarCuenta() {
    const modal = document.createElement('div');
    modal.className = 'modal-fondo';
    modal.innerHTML = `
        <div class="modal-contenido" style="max-width: 500px; text-align: center;">
            <span class="cerrar" onclick="this.parentElement.parentElement.remove()">&times;</span>
            
            <div style="font-size: 60px; color: #dc3545; margin: 20px 0;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h3>¿Eliminar cuenta permanentemente?</h3>
            
            <div style="background: #fff5f5; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left;">
                <p><strong>Esta acción es irreversible y eliminará:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Tu perfil de usuario</li>
                    <li>Tus publicaciones (si eres propietario)</li>
                    <li>Tus favoritos (si eres visitante)</li>
                    <li>Todos tus datos personales</li>
                </ul>
                
                <p style="margin-top: 15px;">
                    <strong>¿Estás seguro de querer eliminar tu cuenta?</strong>
                </p>
            </div>
            
            <div class="form-buttons" style="justify-content: center; gap: 15px;">
                <button onclick="eliminarCuentaDefinitivamente()" 
                        class="btn-registrar" 
                        style="background: #dc3545; border-color: #dc3545;">
                    <i class="fas fa-trash"></i> Sí, eliminar cuenta
                </button>
                
                <button onclick="this.closest('.modal-fondo').remove()" 
                        class="btn-volver">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; color: #666;">
                <i class="fas fa-info-circle"></i> Si prefieres, puedes 
                <a href="#" onclick="desactivarCuentaTemporalmente()" style="color: #82b16d;">desactivar tu cuenta temporalmente</a>
            </p>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
}

function eliminarCuentaDefinitivamente() {
    // AJAX para eliminar cuenta
    fetch('database/eliminar_cuenta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            confirmar: true,
            tipo: 'definitivo'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Tu cuenta ha sido eliminada. Redirigiendo...');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1500);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la cuenta');
    });
}

function desactivarCuentaTemporalmente() {
    // AJAX para desactivar cuenta
    fetch('database/eliminar_cuenta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            confirmar: true,
            tipo: 'temporal'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Tu cuenta ha sido desactivada. Podrás reactivarla iniciando sesión nuevamente.');
            setTimeout(() => {
                window.location.href = 'database/logout.php';
            }, 1500);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al desactivar la cuenta');
    });
}