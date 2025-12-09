<?php include 'aplicacion/Vistas/plantillas/header.php'; ?>
<style>
    /* assets/css/chat.css */

    :root {
        --chat-bg: #f0f2f5;
        --chat-container-bg: #ffffff; 
        --sent-bubble-bg: #dcf8c6;
        --received-bubble-bg: #ffffff;
        --chat-header-bg: var(--primary-color);
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

    .chat-title {
        padding: 1.5rem;
        font-size: 1.8rem;
        color: var(--primary);
        border-bottom: 1px solid var(--gray-dark);
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

    .conversation-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-dark);
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        color: inherit;
        position: relative;
    }

    .conversation-item:hover {
        background-color: var(--gray);
    }

    .conversation-item.unread {
        font-weight: bold;
    }

    .user-avatar {
        font-size: 2.5rem;
        color: var(--chat-icon-color);
        margin-right: 1rem;
        /* Flexbox para centrar contenido (icono o imagen) */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ESTILO NUEVO PARA LA FOTO DE PERFIL */
    .user-avatar img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #fff; /* Fondo blanco por si la imagen es transparente */
    }

    .conversation-details {
        flex-grow: 1;
        overflow: hidden;
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
        margin-left: 1rem;
    }

    .no-conversations {
        text-align: center;
        padding: 3rem;
        color: var(--chat-text-secondary);
    }

    /* Vista de Conversación */
    .conversation-container {
        height: calc(100vh - 120px);
    }

    .chat-header {
        display: flex;
        align-items: center;
        padding: 0.8rem 1rem;
        background-color: var(--chat-header-bg);
        color: white;
    }

    .back-button {
        color: white;
        font-size: 1.2rem;
        margin-right: 1rem;
        text-decoration: none;
    }

    .chat-header .user-avatar {
        margin-right: 0.8rem;
        color: white;
    }

    .chat-with-user {
        font-size: 1.2rem;
        margin: 0;
    }

    .user-status {
        font-size: 0.85rem;
        opacity: 0.8;
        margin: 0;
    }

    .chat-messages {
        flex-grow: 1;
        padding: 1rem;
        background-color: var(--chat-bg);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .message-wrapper {
        display: flex;
        margin-bottom: 0.75rem;
        max-width: 70%;
    }

    .message-wrapper.sent {
        align-self: flex-end;
    }

    .message-wrapper.received {
        align-self: flex-start;
    }

    .message {
        padding: 0.6rem 0.9rem;
        border-radius: 12px;
        position: relative;
    }

    .message-wrapper.sent .message {
        background-color: var(--sent-bubble-bg);
        border-bottom-right-radius: 2px;
    }

    .message-wrapper.received .message {
        background-color: var(--received-bubble-bg);
        box-shadow: 0 1px 1px rgba(0,0,0,0.05);
        border-bottom-left-radius: 2px;
    }

    .message-content {
        margin: 0;
        margin-bottom: 0.25rem;
        word-wrap: break-word;
        color: var(--chat-text-primary);
    }

    .message-time {
        font-size: 0.75rem;
        color: var(--chat-text-secondary);
        float: right;
        margin-left: 1rem;
    }

    .chat-input-area {
        padding: 0.8rem 1rem;
        background-color: var(--chat-bg);
        border-top: 1px solid var(--gray-dark);
    }

    #message-form {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    #message-input {
        flex-grow: 1;
        padding: 0.8rem 1rem;
        border: none;
        border-radius: 20px;
        font-size: 1rem;
    }

    #message-input:focus {
        outline: none;
    }

    #send-button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #send-button:hover {
        background-color: var(--primary-dark);
    }

    #send-button:disabled {
        background-color: var(--chat-icon-color);
        cursor: not-allowed;
    }

    /* Estilos para eliminar mensaje */
    .message-wrapper.sent .message {
        position: relative;
    }

    .btn-delete-msg {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 12px;
        cursor: pointer;
        display: none; 
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        transition: background-color 0.2s;
    }

    .message-wrapper.sent:hover .btn-delete-msg {
        display: flex; 
    }

    .btn-delete-msg:hover {
        background: #c0392b;
    }

    .message-deleted {
        color: var(--chat-text-secondary);
        font-style: italic;
    }

    /* Modal de Confirmación Personalizado */
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
    }

    .custom-modal-buttons {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    #custom-confirm-cancel:hover {
        background-color: #f0f0f0;
        border-color: #bbb;
    }

    @media (max-width:768px){
        .chat-container{
            margin: 2rem auto 2rem auto;
        }
    }
</style>

<div class="chat-container conversation-container">
    <div class="chat-header">
        <a href="<?php echo BASE_URL; ?>chat" class="back-button"><i class="fas fa-arrow-left"></i></a>
        
        <div class="user-avatar">
            <?php if (!empty($datosVista['otro_usuario']['foto_perfil'])): ?>
                <img src="<?php echo BASE_URL . htmlspecialchars($datosVista['otro_usuario']['foto_perfil']); ?>" alt="Perfil">
            <?php else: ?>
                <i class="fas fa-user-circle"></i>
            <?php endif; ?>
        </div>
        <div class="chat-header-info">
            <h2 class="chat-with-user"><?php echo htmlspecialchars($datosVista['otro_usuario']['nombres'] . ' ' . $datosVista['otro_usuario']['apellidos']); ?></h2>
            <p class="user-status">
                <?php
                    $estado_usuario = 'desconectado'; 
                    $ultima_conexion_str = 'hace mucho tiempo';

                    if (!empty($datosVista['otro_usuario']['fecha_ultima_conexion'])) {
                        $ahora = new DateTime();
                        $ultima_conexion = new DateTime($datosVista['otro_usuario']['fecha_ultima_conexion']);
                        $diferencia_minutos = ($ahora->getTimestamp() - $ultima_conexion->getTimestamp()) / 60;

                        if ($diferencia_minutos < 5) {
                            $estado_usuario = 'en_linea';
                        } else {
                            if ($ultima_conexion->format('Y-m-d') == $ahora->format('Y-m-d')) {
                                $ultima_conexion_str = 'Últ. vez hoy a las ' . $ultima_conexion->format('H:i');
                            } else {
                                $ultima_conexion_str = 'Últ. vez el ' . $ultima_conexion->format('d/m/Y \a \l\a\s H:i');
                            }
                        }
                    }
                    echo $estado_usuario == 'en_linea' ? 'En línea' : htmlspecialchars($ultima_conexion_str);
                ?>
            </p>
        </div>
    </div>

    <div class="chat-messages" id="chat-messages">
        <?php foreach ($datosVista['mensajes'] as $mensaje):
            if (isset($mensaje['es_sistema']) && $mensaje['es_sistema'] == 1): ?>
                <div class="mensaje-sistema">
                    <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($mensaje['contenido']); ?>
                </div>
            <?php else:
                $esMio = $mensaje['id_remitente'] == $datosVista['id_usuario_actual']; ?>
                <div class="message-wrapper <?php echo $esMio ? 'sent' : 'received'; ?>" id="mensaje-<?php echo $mensaje['id_mensaje']; ?>">
                    <div class="message">
                        <?php if (isset($mensaje['estado']) && $mensaje['estado'] == 1): ?>
                            <p class="message-content message-deleted"><i class="fas fa-ban"></i> Se ha eliminado este mensaje</p>
                        <?php else: ?>
                            <p class="message-content"><?php echo htmlspecialchars($mensaje['contenido']); ?></p>
                            <?php if ($esMio): ?>
                                <button class="btn-delete-msg" data-id="<?php echo $mensaje['id_mensaje']; ?>" title="Eliminar mensaje"><i class="fas fa-trash-alt"></i></button>
                            <?php endif; ?>
                        <?php endif; ?>
                        <span class="message-time"><?php echo date('H:i', strtotime($mensaje['fecha_envio'])); ?></span>
                    </div>
                </div>
            <?php endif; endforeach; ?>
    </div>

    <div class="chat-input-area">
        <form id="message-form" autocomplete="off">
            <input type="hidden" name="id_conversacion" id="id_conversacion" value="<?php echo $datosVista['conversacion']['id_conversacion']; ?>">
            <input type="text" name="contenido" id="message-input" placeholder="Escribe un mensaje...">
            <button type="submit" id="send-button"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<div id="custom-confirm-modal" class="custom-modal-overlay">
    <div class="custom-modal-box">
        <h3 style="font-size: 1.4rem; color: #333; margin-bottom: 1rem;">Confirmar Eliminación</h3>
        <p style="color: #666; line-height: 1.6;">¿Estás seguro de que quieres eliminar este mensaje? Esta acción no se puede deshacer.</p>
        <div class="custom-modal-buttons">
            <button id="custom-confirm-cancel" class="btn btn-outline" style="border-color: #ccc; color: #333;">Cancelar</button>
            <button id="custom-confirm-ok" class="btn btn-primary" style="background-color: #d32f2f; border-color: #d32f2f;">Eliminar</button>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const idConversacion = document.getElementById('id_conversacion').value;
    const idUsuarioActual = <?php echo $datosVista['id_usuario_actual']; ?>;

    const confirmModal = document.getElementById('custom-confirm-modal');
    const btnConfirmCancel = document.getElementById('custom-confirm-cancel');
    const btnConfirmOk = document.getElementById('custom-confirm-ok');
    let messageIdToDelete = null;

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    scrollToBottom();

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const contenido = messageInput.value.trim();
        if (contenido === '') return;

        const formData = new FormData();
        formData.append('id_conversacion', idConversacion);
        formData.append('contenido', contenido);

        messageInput.disabled = true;
        sendButton.disabled = true;

        fetch('<?php echo BASE_URL; ?>chat/enviar', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.mensaje) {
                appendMessage(data.mensaje, 'sent');
                messageInput.value = '';
            } else {
                console.error('Error al enviar mensaje:', data.error);
            }
        })
        .catch(error => console.error('Error en la petición fetch:', error))
        .finally(() => {
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        });
    });

    let longPressTimer;
    let isLongPress = false;

    function hideAllDeleteButtons() {
        document.querySelectorAll('.message-wrapper.show-delete-btn').forEach(wrapper => {
            wrapper.classList.remove('show-delete-btn');
        });
    }

    chatMessages.addEventListener('mouseover', function(e) {
        const messageWrapper = e.target.closest('.message-wrapper.sent');
        if (messageWrapper) {
            hideAllDeleteButtons();
            messageWrapper.classList.add('show-delete-btn');
        }
    });

    chatMessages.addEventListener('mouseout', function(e) {
        const messageWrapper = e.target.closest('.message-wrapper.sent');
        if (messageWrapper && !messageWrapper.contains(e.relatedTarget)) {
            messageWrapper.classList.remove('show-delete-btn');
        }
    });

    chatMessages.addEventListener('touchstart', function(e) {
        const messageWrapper = e.target.closest('.message-wrapper.sent');
        if (messageWrapper) {
            isLongPress = false;
            longPressTimer = setTimeout(() => {
                hideAllDeleteButtons();
                messageWrapper.classList.add('show-delete-btn');
                isLongPress = true;
            }, 500);
        }
    });

    chatMessages.addEventListener('touchend', function() {
        clearTimeout(longPressTimer);
    });

    chatMessages.addEventListener('touchmove', function() {
        clearTimeout(longPressTimer);
    });

    document.body.addEventListener('click', function(e) {
        if (!e.target.closest('.message-wrapper.sent')) {
            hideAllDeleteButtons();
        }
    }, true);

    chatMessages.addEventListener('click', function(e) {
        const deleteButton = e.target.closest('.btn-delete-msg');
        if (deleteButton) {
            messageIdToDelete = deleteButton.dataset.id;
            confirmModal.classList.add('visible');
            return;
        }
        if (isLongPress) {
            e.preventDefault();
            isLongPress = false;
        }
    });

    btnConfirmCancel.addEventListener('click', () => {
        confirmModal.classList.remove('visible');
        messageIdToDelete = null;
    });

    btnConfirmOk.addEventListener('click', () => {
        if (messageIdToDelete) {
            handleDeleteMessage(messageIdToDelete);
        }
        confirmModal.classList.remove('visible');
    });

    confirmModal.addEventListener('click', function(e) {
        if (e.target === this) { confirmModal.classList.remove('visible'); }
    });

    function handleDeleteMessage(messageId) {
        fetch('<?php echo BASE_URL; ?>chat/eliminarMensaje', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id_mensaje: messageId })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.error || 'Error del servidor') });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const messageWrapper = document.getElementById('mensaje-' + data.id_mensaje);
                if (messageWrapper) {
                    const messageDiv = messageWrapper.querySelector('.message');
                    if (messageDiv) {
                        const timeSpanHTML = messageDiv.querySelector('.message-time').outerHTML;
                        messageDiv.innerHTML = `
                            <p class="message-content message-deleted"><i class="fas fa-ban"></i> Se ha eliminado este mensaje</p>
                            ${timeSpanHTML}
                        `;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error en la petición de eliminación:', error);
            alert('Error: ' + error.message);
        });
    }

    function appendMessage(mensaje, typeClass) {
        const messageWrapper = document.createElement('div');
        messageWrapper.className = `message-wrapper ${typeClass}`;
        messageWrapper.id = `mensaje-${mensaje.id_mensaje}`;

        const messageDiv = document.createElement('div');
        messageDiv.className = 'message';

        const contentP = document.createElement('p');
        contentP.className = 'message-content';
        contentP.textContent = mensaje.contenido;

        const timeSpan = document.createElement('span');
        timeSpan.className = 'message-time';
        const messageDate = new Date(mensaje.fecha_envio);
        timeSpan.textContent = `${String(messageDate.getHours()).padStart(2, '0')}:${String(messageDate.getMinutes()).padStart(2, '0')}`;

        messageDiv.appendChild(contentP);
        messageDiv.appendChild(timeSpan);
        messageWrapper.appendChild(messageDiv);

        if (typeClass === 'sent') {
            const deleteButton = document.createElement('button');
            deleteButton.className = 'btn-delete-msg';
            deleteButton.dataset.id = mensaje.id_mensaje;
            deleteButton.title = 'Eliminar mensaje';
            deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
            messageDiv.appendChild(deleteButton);
        }

        chatMessages.appendChild(messageWrapper);

        scrollToBottom();
    }

    setInterval(function() {
        fetch(`<?php echo BASE_URL; ?>chat/obtenerNuevos?id_conversacion=${idConversacion}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.mensajes.length > 0) {
                    data.mensajes.forEach(mensaje => {
                        appendMessage(mensaje, 'received');
                    });
                }
            })
            .catch(error => console.error('Error en polling:', error));
    }, 3000);
});
</script>

<?php include 'aplicacion/Vistas/plantillas/footer.php'; ?>