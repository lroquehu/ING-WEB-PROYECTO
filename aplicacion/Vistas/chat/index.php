<?php include 'aplicacion/Vistas/plantillas/header.php'; ?>
    <style>
        /* assets/css/chat.css */

        :root {
            --chat-bg: #f0f2f5;
            --chat-container-bg: #ffffff;
            --sent-bubble-bg: #dcf8c6;
            --received-bubble-bg: #ffffff;
            --chat-header-bg: var(--primary);
            --chat-text-primary: #000000;
            --chat-text-secondary: #667781;
            --chat-icon-color: #8696a0;
            --unread-badge-bg: var(--success);
        }

        .chat-container {
            max-width: 800px; 
            margin: 4rem auto 2rem auto;
            background: var(--chat-container-bg);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .page-header-with-back {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid #e9e9e9;
        }

        .back-arrow {
            font-size: 1.5rem;
            color: var(--text-light);
            margin-right: 1.5rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-arrow:hover {
            color: var(--primary-color);
        }

        .chat-title {
            font-size: 1.8rem;
            color: var(--primary);
            margin: 0;
        }

        .chat-alert.error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            margin: 1rem;
            border-radius: 4px;
        }

        /* Lista de Conversaciones */
        .list-container {
            height: calc(100vh - 150px);
        }

        .conversations-list {
            overflow-y: auto;
            flex-grow: 1;
        }

        /* Wrapper para animación */
        .conversation-wrapper {
            position: relative;
            border-bottom: 1px solid #e9e9e9;
            transition: all 0.3s ease;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            color: inherit;
            width: 100%;
        }

        .conversation-item:hover {
            background-color: #fafafa;
        }

        .conversation-item.unread {
            font-weight: bold;
        }

        .user-avatar {
            font-size: 2.5rem;
            color: var(--chat-icon-color);
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-avatar img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .conversation-details {
            flex-grow: 1;
            overflow: hidden;
            padding-right: 10px;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .user-name {
            font-size: 1.1rem;
            color: var(--chat-text-primary);
        }

        .conversation-time {
            font-size: 0.8rem;
            color: var(--chat-text-secondary);
            white-space: nowrap;
            margin-left: 10px;
        }

        .last-message {
            color: var(--chat-text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }

        .unread-count {
            background-color: var(--unread-badge-bg);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            margin-left: 0.5rem;
            flex-shrink: 0;
        }

        .no-conversations {
            text-align: center;
            padding: 3rem;
            color: var(--chat-text-secondary);
        }

        /* Botón de eliminar */
        .conversation-actions {
            margin-left: 10px;
            opacity: 0; 
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
        }

        .conversation-wrapper:hover .conversation-actions {
            opacity: 1;
        }

        .btn-delete-chat {
            background: none;
            border: none;
            color: #dc3545; 
            cursor: pointer;
            padding: 8px;
            font-size: 1.1rem;
            border-radius: 50%;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .btn-delete-chat:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        /* --- NUEVO: ESTILOS DEL MODAL PERSONALIZADO --- */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .custom-modal-overlay.visible {
            opacity: 1;
            visibility: visible;
        }

        .custom-modal-box {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 450px;
            text-align: center;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .custom-modal-overlay.visible .custom-modal-box {
            transform: translateY(0);
        }

        .custom-modal-buttons {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        /* Botones del modal */
        .btn-modal {
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .btn-modal-cancel {
            background-color: #f8f9fa;
            border-color: #ddd;
            color: #333;
        }
        .btn-modal-cancel:hover {
            background-color: #e2e6ea;
        }

        .btn-modal-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-modal-delete:hover {
            background-color: #c82333;
        }

        @media (max-width: 768px){
            .chat-container{
                margin: 0rem auto 2rem auto;
            }
            .conversation-actions {
                opacity: 1; /* Siempre visible en móviles */
            }
        }

    </style>

<div class="chat-container list-container">
    <div class="page-header-with-back">
        <a href="javascript:history.back()" class="back-arrow" aria-label="Volver a la página anterior"><i class="fas fa-arrow-left"></i></a>
        <h1 class="chat-title">Mis Mensajes</h1>
    </div>

    <?php if (isset($_SESSION['error_chat'])): ?>
        <div class="chat-alert error"><?php echo $_SESSION['error_chat']; unset($_SESSION['error_chat']); ?></div>
    <?php endif; ?>

    <div class="conversations-list" id="lista-conversaciones">
        <?php if (empty($datosVista['conversaciones'])): ?>
            <p class="no-conversations">No tienes ninguna conversación activa.</p>
        <?php else: ?>
            <?php foreach ($datosVista['conversaciones'] as $conv): ?>
                
                <div class="conversation-wrapper" id="chat-row-<?php echo $conv['id_conversacion']; ?>">
                    <a href="<?php echo BASE_URL . 'chat/ver/' . $conv['id_conversacion']; ?>" class="conversation-item <?php echo ($conv['no_leidos'] > 0) ? 'unread' : ''; ?>">
                        
                        <div class="user-avatar">
                            <?php if(!empty($conv['foto_perfil'])): ?>
                                <img src="<?php echo BASE_URL . $conv['foto_perfil']; ?>" alt="Foto">
                            <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="conversation-details">
                            <div class="conversation-header">
                                <span class="user-name"><?php echo htmlspecialchars($conv['nombres'] . ' ' . $conv['apellidos']); ?></span>
                                <span class="conversation-time"><?php echo date('d/m/y H:i', strtotime($conv['fecha_ultimo_mensaje'] ?? $conv['fecha_actualizacion'])); ?></span>
                            </div>
                            <p class="last-message">
                                <?php
                                if (isset($conv['ultimo_mensaje_estado']) && $conv['ultimo_mensaje_estado'] == 1): ?>
                                    <span style="font-style: italic; color: var(--chat-text-secondary);">
                                        <i class="fas fa-ban"></i> Mensaje eliminado
                                    </span>
                                <?php else:
                                    echo htmlspecialchars(substr($conv['ultimo_mensaje'] ?? 'Inicia la conversación...', 0, 50));
                                    if (strlen($conv['ultimo_mensaje'] ?? '') > 50) echo '...';
                                endif; ?>
                            </p>
                        </div>
                        
                        <?php if ($conv['no_leidos'] > 0): ?>
                            <div class="unread-count"><?php echo $conv['no_leidos']; ?></div>
                        <?php endif; ?>

                        <div class="conversation-actions">
                            <button type="button" class="btn-delete-chat" 
                                    onclick="abrirModalEliminar(event, <?php echo $conv['id_conversacion']; ?>)" 
                                    title="Eliminar conversación">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                    </a>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="modal-eliminar-chat" class="custom-modal-overlay">
    <div class="custom-modal-box">
        <h3 style="font-size: 1.4rem; color: #333; margin-bottom: 1rem;">Eliminar conversación</h3>
        <p style="color: #666; line-height: 1.6;">¿Estás seguro de que quieres eliminar esta conversación? Desaparecerá de tu lista, pero el otro usuario aún podrá verla.</p>
        <div class="custom-modal-buttons">
            <button id="btn-cancelar" class="btn-modal btn-modal-cancel">Cancelar</button>
            <button id="btn-confirmar" class="btn-modal btn-modal-delete">Eliminar</button>
        </div>
    </div>
</div>

<script>
// Variable global para almacenar el ID del chat a eliminar
let idChatParaEliminar = null;
const modal = document.getElementById('modal-eliminar-chat');
const btnCancelar = document.getElementById('btn-cancelar');
const btnConfirmar = document.getElementById('btn-confirmar');

// Función que se llama al hacer clic en el basurero
function abrirModalEliminar(event, idConversacion) {
    // Evita navegar al chat
    event.preventDefault(); 
    event.stopPropagation();

    // Guardamos el ID y mostramos el modal
    idChatParaEliminar = idConversacion;
    modal.classList.add('visible');
}

// Cerrar modal al cancelar
btnCancelar.addEventListener('click', function() {
    modal.classList.remove('visible');
    idChatParaEliminar = null;
});

// Cerrar modal si se hace clic fuera de la caja
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        modal.classList.remove('visible');
        idChatParaEliminar = null;
    }
});

// Lógica de eliminación al confirmar
btnConfirmar.addEventListener('click', function() {
    if (!idChatParaEliminar) return;

    // Realizar la petición AJAX
    fetch('<?php echo BASE_URL; ?>chat/eliminarConversacion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id_conversacion: idChatParaEliminar })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ocultar modal
            modal.classList.remove('visible');

            // Eliminar visualmente la fila con animación
            const row = document.getElementById('chat-row-' + idChatParaEliminar);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                
                setTimeout(() => {
                    row.remove();
                    
                    // Verificar si quedan conversaciones
                    const lista = document.getElementById('lista-conversaciones');
                    const elementosRestantes = lista.querySelectorAll('.conversation-wrapper');
                    
                    if (elementosRestantes.length === 0) {
                        lista.innerHTML = '<p class="no-conversations">No tienes ninguna conversación activa.</p>';
                    }
                }, 300);
            }
        } else {
            alert('Error: ' + (data.error || 'No se pudo eliminar la conversación.'));
            modal.classList.remove('visible');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al intentar eliminar la conversación.');
        modal.classList.remove('visible');
    })
    .finally(() => {
        idChatParaEliminar = null;
    });
});
</script>

<?php include 'aplicacion/Vistas/plantillas/footer.php'; ?>