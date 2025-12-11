<?php
    include("database/registro.php");
    include("database/inicio_sesion.php");
 
    // Configuración para mensajes
    $mensaje = '';
    $error_mensaje = '';
    
    if (isset($_GET['registro']) && $_GET['registro'] === 'ok') {
        $mensaje = '¡Registro completado exitosamente!';
    }
    
    $error = isset($_GET['error']) ? $_GET['error'] : "";
    
    if ($error == "no_google") {
        $error_mensaje = "Debes iniciar sesión con Google para continuar";
    } elseif ($error == "usuario_inactivo") {
        $error_mensaje = "Tu cuenta está inactiva. Contacta al administrador";
    } elseif ($error == "completar_registro") {
        $error_mensaje = "Por favor, completa tu información de registro";
    } elseif ($error == "google_not_configured") {
        $error_mensaje = "Sistema en mantenimiento. Pronto estará disponible.";
    }
    
    // Configuración de Google - TUS CREDENCIALES YA ESTÁN CONFIGURADAS
    $google_client_id = '24939222054-j2nhbalkqbqk0hivb51kidq5duacpglk.apps.googleusercontent.com';
    $google_client_secret = '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';
    $google_redirect_uri = 'http://localhost/RentNono/database/google_callback.php';
    
    // IMPORTANTE: Verificar si el callback.php existe
    $google_callback_path = 'database/google_callback.php';
    $callback_exists = file_exists($google_callback_path);
    
    // Siempre mostrar el botón de Google ya que las credenciales están configuradas
    $google_configured = true;
    
    if ($google_configured && $callback_exists) {
        $google_auth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $google_client_id,
            'redirect_uri' => $google_redirect_uri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ]);
    } else {
        // Si no existe el callback, mostrar error específico
        if (!$callback_exists) {
            $error_mensaje = "Error: Archivo google_callback.php no encontrado. Verifica la ruta.";
        }
    }
    
?>

<link rel="stylesheet" href="estilos/login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Ventana Login -->
<div id="modalFondoLogin" class="modal-fondo">
    <div class="modal-contenido">
        <span id="cerrarLogin" class="cerrar">&times;</span>
        <h2>BIENVENIDO A RENTNONO</h2>

        <?php if ($error_mensaje): ?>
            <div class="error-login">
                <?= htmlspecialchars($error_mensaje) ?>
            </div>
            <script>
                window.addEventListener("DOMContentLoaded", function() {
                    document.getElementById("modalFondoLogin").style.display = "flex";
                });
            </script>
        <?php endif; ?>
        
        <?php if ($mensaje): ?>
            <div class="mensaje-exito" style="display: block; margin: 10px 0;">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <div class="login-options">
            <?php if ($google_configured && $callback_exists): ?>
                <div class="google-login-btn" onclick="window.location.href='<?= $google_auth_url ?>'">
                    <div class="google-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="24px" height="24px">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                        </svg>
                    </div>
                    <span>Continuar con Google</span>
                </div>
                
                <div class="login-divider">
                    <span>o</span>
                </div>
                
                <p class="login-info">
                    <i class="fas fa-info-circle"></i> Al iniciar sesión con Google, podrás completar tu perfil como Visitante o Propietario.
                </p>
            <?php else: ?>
                <div class="error-login">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $error_mensaje ?: 'Error en la configuración de Google Login' ?>
                </div>
                
                <!-- Botón temporal para desarrollo -->
                <div class="dev-login">
                    <h4>Para desarrollo (temporal):</h4>
                    <button onclick="simulateGoogleLogin()" class="btn-dev">
                        <i class="fas fa-user-circle"></i> Simular Login de Google
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Ventana Completar Registro -->
<div id="modalFondoCompletarRegistro" class="modal-fondo">
    <div class="modal-contenido modal-wide">
        <span id="cerrarCompletarRegistro" class="cerrar">&times;</span>
        <h2>COMPLETAR TU REGISTRO</h2>
        <p class="mensaje-info">Hola <span id="nombreUsuario"></span>, por favor selecciona cómo quieres usar RentNono:</p>
        
        <div class="tipo-registro-options">
            <div class="tipo-option" id="optionVisitante">
                <div class="tipo-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Visitante</h3>
                <p>Buscar y alquilar propiedades</p>
                <ul class="beneficios">
                    <li><i class="fas fa-check"></i> Explorar propiedades disponibles</li>
                    <li><i class="fas fa-check"></i> Guardar favoritos</li>
                    <li><i class="fas fa-check"></i> Contactar propietarios</li>
                    <li><i class="fas fa-check"></i> Recibir notificaciones</li>
                </ul>
                <button class="btn-seleccionar" data-tipo="visitante">
                    <i class="fas fa-user"></i> Seleccionar como Visitante
                </button>
            </div>
            
            <div class="tipo-option" id="optionPropietario">
                <div class="tipo-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h3>Propietario</h3>
                <p>Publicar y administrar propiedades</p>
                <ul class="beneficios">
                    <li><i class="fas fa-check"></i> Publicar propiedades para alquilar</li>
                    <li><i class="fas fa-check"></i> Administrar tus publicaciones</li>
                    <li><i class="fas fa-check"></i> Recibir consultas de interesados</li>
                    <li><i class="fas fa-check"></i> Estadísticas de visitas</li>
                </ul>
                <button class="btn-seleccionar" data-tipo="propietario">
                    <i class="fas fa-user-tie"></i> Seleccionar como Propietario
                </button>
            </div>
        </div>
        
        <div id="formContainer" style="display: none;">
            <h3 id="formTitulo"></h3>
            <form method="POST" action="database/completar_registro.php" id="formRegistro">
                <input type="hidden" name="google_id" id="googleId">
                <input type="hidden" name="nombre" id="nombreCompleto">
                <input type="hidden" name="correo" id="correoElectronico">
                <input type="hidden" name="tipo" id="tipoUsuario">
                
                <div id="visitanteFields" class="form-fields">
                    <div class="input-group">
                        <div class="input-container">
                            <input type="tel" name="telefono" placeholder="Teléfono (opcional)" id="telefonoVisitante">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                    </div>
                </div>
                
                <div id="propietarioFields" class="form-fields" style="display: none;">
                    <div class="input-group">
                        <div class="input-container">
                            <select name="sexo" id="sexoPropietario" required>
                                <option value="" disabled selected>Selecciona tu sexo</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="otro">Otro</option>
                                <option value="prefiero_no_decirlo">Prefiero no decirlo</option>
                            </select>
                            <i class="fa-solid fa-venus-mars"></i>
                        </div>

                        <div class="input-container">
                            <input type="text" name="dni" placeholder="DNI (opcional)" maxlength="8" pattern="[0-9]{7,8}" inputmode="numeric" id="dniPropietario">
                            <i class="fa-solid fa-id-card"></i>
                        </div>

                        <div class="input-container">
                            <input type="tel" name="telefono" placeholder="Teléfono" required maxlength="13" inputmode="numeric" id="telefonoPropietario">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                    </div>
                </div>
                
                <div class="terminos-container">
                    <input type="checkbox" id="terminos" name="terminos" required>
                    <label for="terminos">
                        Acepto los <a href="#" target="_blank">Términos y Condiciones</a> y la <a href="#" target="_blank">Política de Privacidad</a>
                    </label>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-registrar">
                        <i class="fa-solid fa-check-circle"></i> Completar Registro
                    </button>
                    
                    <button type="button" id="volverSeleccion" class="btn-volver">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Elementos del modal de completar registro
const modalFondoCompletarRegistro = document.getElementById('modalFondoCompletarRegistro');
const cerrarCompletarRegistro = document.getElementById('cerrarCompletarRegistro');
const volverSeleccion = document.getElementById('volverSeleccion');
const formContainer = document.getElementById('formContainer');
const tipoRegistroOptions = document.querySelector('.tipo-registro-options');
const mensajeInfo = document.querySelector('.mensaje-info');

// Variables para guardar datos de Google
let googleUserData = {};

// Función para simular login de Google (para desarrollo)
function simulateGoogleLogin() {
    const testUser = {
        google_id: 'test_google_' + Date.now(),
        nombre: 'Usuario de Prueba',
        correo: 'test' + Date.now() + '@gmail.com',
        foto: 'https://via.placeholder.com/150'
    };
    
    mostrarCompletarRegistro(testUser);
}

// Función para mostrar modal de completar registro
function mostrarCompletarRegistro(userData) {
    googleUserData = userData;
    
    // Actualizar datos en los formularios
    document.getElementById('nombreUsuario').textContent = userData.nombre;
    document.getElementById('googleId').value = userData.google_id;
    document.getElementById('nombreCompleto').value = userData.nombre;
    document.getElementById('correoElectronico').value = userData.correo;
    
    // Mostrar modal
    document.getElementById('modalFondoLogin').style.display = 'none';
    modalFondoCompletarRegistro.style.display = 'flex';
}

// Cerrar modal de completar registro
cerrarCompletarRegistro.onclick = () => {
    modalFondoCompletarRegistro.style.display = 'none';
    document.getElementById('modalFondoLogin').style.display = 'flex';
};

// Botones de selección
document.querySelectorAll('.btn-seleccionar').forEach(btn => {
    btn.addEventListener('click', function() {
        const tipo = this.getAttribute('data-tipo');
        seleccionarTipo(tipo);
    });
});

// Función para seleccionar tipo
function seleccionarTipo(tipo) {
    // Ocultar opciones y mostrar formulario
    tipoRegistroOptions.style.display = 'none';
    mensajeInfo.style.display = 'none';
    formContainer.style.display = 'block';
    
    // Actualizar título y campos según tipo
    document.getElementById('tipoUsuario').value = tipo;
    
    if (tipo === 'visitante') {
        document.getElementById('formTitulo').textContent = 'Registro como Visitante';
        document.getElementById('visitanteFields').style.display = 'block';
        document.getElementById('propietarioFields').style.display = 'none';
    } else {
        document.getElementById('formTitulo').textContent = 'Registro como Propietario';
        document.getElementById('visitanteFields').style.display = 'none';
        document.getElementById('propietarioFields').style.display = 'block';
    }
}

// Volver a selección de tipo
volverSeleccion.onclick = () => {
    formContainer.style.display = 'none';
    tipoRegistroOptions.style.display = 'flex';
    mensajeInfo.style.display = 'block';
};

// Cerrar modales al hacer click fuera
window.addEventListener('click', (e) => {
    if(e.target === modalFondoCompletarRegistro) {
        modalFondoCompletarRegistro.style.display = 'none';
        document.getElementById('modalFondoLogin').style.display = 'flex';
    }
});

// Formatear teléfono automáticamente
function formatearTelefono(input) {
    let valor = input.value.replace(/\D/g, "");
    if (valor.length > 4 && valor.length <= 6) {
        valor = valor.replace(/(\d{4})(\d+)/, "$1 $2");
    } else if (valor.length > 6) {
        valor = valor.replace(/(\d{4})(\d{2})(\d{0,4})/, "$1 $2-$3");
    }
    input.value = valor.trim();
}

// Eventos de formato en teléfono
document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener("input", function() {
        formatearTelefono(this);
    });
});

// Validación del formulario
document.getElementById('formRegistro').addEventListener('submit', function(e) {
    let valido = true;
    
    // Validar teléfono si es requerido (para propietario)
    const tipo = document.getElementById('tipoUsuario').value;
    
    if (tipo === 'propietario') {
        const telefono = document.getElementById('telefonoPropietario');
        const valor = telefono.value.trim();
        
        if (!valor || !/^\d{4}\s\d{2}-\d{4}$/.test(valor)) {
            alert('Por favor, ingresa un teléfono válido (ej: 3825 40-7398).');
            telefono.focus();
            valido = false;
        }
    }
    
    // Validar términos y condiciones
    const terminos = document.getElementById('terminos');
    if (!terminos.checked) {
        alert('Debes aceptar los Términos y Condiciones para continuar.');
        terminos.focus();
        valido = false;
    }

    if (!valido) {
        e.preventDefault();
    }
});

// Mostrar automáticamente el login si hay error
window.addEventListener("DOMContentLoaded", function() {
    <?php if ($error_mensaje && !isset($_GET['completar_registro'])): ?>
        document.getElementById("modalFondoLogin").style.display = "flex";
    <?php endif; ?>
    
    // Verificar si hay datos de Google en la URL (simulación)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('completar_registro') === 'true') {
        // Datos de prueba para desarrollo
        const testData = {
            google_id: 'google_' + Math.random().toString(36).substr(2, 9),
            nombre: 'Usuario Google',
            correo: 'usuario@gmail.com',
            foto: 'https://via.placeholder.com/150'
        };
        mostrarCompletarRegistro(testData);
    }
});
</script>