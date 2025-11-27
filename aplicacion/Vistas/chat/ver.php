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
        margin: 8rem auto 2rem auto; /* Aumentado el margen superior para que no lo tape el header */
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
        display: none; /* Oculto por defecto */
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        transition: background-color 0.2s;
    }

    .message-wrapper.sent:hover .btn-delete-msg {
        display: flex; /* Se muestra en el hover del mensaje propio */
    }

    .btn-delete-msg:hover {
        background: #c0392b;
    }

    .message-deleted {
        color: var(--chat-text-secondary);
        font-style: italic;
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
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="chat-header-info">
            <h2 class="chat-with-user"><?php echo htmlspecialchars($datosVista['otro_usuario']['nombres'] . ' ' . $datosVista['otro_usuario']['apellidos']); ?></h2>
            <p class="user-status">En línea</p> <!-- Esto es estático, se podría hacer dinámico -->
        </div>
    </div>

    <div class="chat-messages" id="chat-messages">
        <?php foreach ($datosVista['mensajes'] as $mensaje): ?>
            <?php $esMio = $mensaje['id_remitente'] == $datosVista['id_usuario_actual']; ?>
            <div class="message-wrapper <?php echo $esMio ? 'sent' : 'received'; ?>" id="mensaje-<?php echo $mensaje['id_mensaje']; ?>">
                <div class="message">
                    <?php if (isset($mensaje['estado']) && $mensaje['estado'] == 1): ?>
                        <p class="message-content message-deleted"><i class="fas fa-ban"></i> Se ha eliminado este mensaje</p>
                    <?php else: ?>
                        <p class="message-content"><?php echo htmlspecialchars($mensaje['contenido']); ?></p>
                        <?php if ($esMio): ?>
                            <button class="btn-delete-msg" data-id="<?php echo $mensaje['id_mensaje']; ?>" title="Eliminar mensaje">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <span class="message-time"><?php echo date('H:i', strtotime($mensaje['fecha_envio'])); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="chat-input-area">
        <form id="message-form" autocomplete="off">
            <input type="hidden" name="id_conversacion" id="id_conversacion" value="<?php echo $datosVista['conversacion']['id_conversacion']; ?>">
            <input type="text" name="contenido" id="message-input" placeholder="Escribe un mensaje...">
            <button type="submit" id="send-button"><i class="fas fa-paper-plane"></i></button>
        </form>
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

    // Función para hacer scroll hasta el final
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Scroll inicial
    scrollToBottom();

    // Enviar mensaje
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const contenido = messageInput.value.trim();
        if (contenido === '') return;

        const formData = new FormData();
        formData.append('id_conversacion', idConversacion);
        formData.append('contenido', contenido);

        // Deshabilitar input y botón para evitar envíos múltiples
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
                // Opcional: mostrar un error al usuario
            }
        })
        .catch(error => console.error('Error en la petición fetch:', error))
        .finally(() => {
            // Rehabilitar input y botón
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        });
    });

    // --- Lógica para mostrar/ocultar botón de eliminar (Desktop y Móvil) ---
    let longPressTimer;
    let isLongPress = false;

    // Función para ocultar todos los botones de eliminar abiertos
    function hideAllDeleteButtons() {
        document.querySelectorAll('.message-wrapper.show-delete-btn').forEach(wrapper => {
            wrapper.classList.remove('show-delete-btn');
        });
    }

    // Eventos para ESCRITORIO (hover)
    chatMessages.addEventListener('mouseover', function(e) {
        const messageWrapper = e.target.closest('.message-wrapper.sent');
        if (messageWrapper) {
            hideAllDeleteButtons(); // Oculta otros antes de mostrar el nuevo
            messageWrapper.classList.add('show-delete-btn');
        }
    });

    chatMessages.addEventListener('mouseout', function(e) {
        const messageWrapper = e.target.closest('.message-wrapper.sent');
        if (messageWrapper && !messageWrapper.contains(e.relatedTarget)) {
            messageWrapper.classList.remove('show-delete-btn');
        }
    });

    // Eventos para MÓVIL (mantener presionado)
    chatMessages.addEventListener('touchstart', function(e) {
        const messageWrapper = e.target.closest('.message-wrapper.sent');
        if (messageWrapper) {
            isLongPress = false;
            longPressTimer = setTimeout(() => {
                hideAllDeleteButtons();
                messageWrapper.classList.add('show-delete-btn');
                isLongPress = true; // Marcamos que fue un long press
            }, 500); // 500ms para considerar "mantener presionado"
        }
    });

    chatMessages.addEventListener('touchend', function() {
        clearTimeout(longPressTimer); // Cancelar el timer si se levanta el dedo antes
    });

    chatMessages.addEventListener('touchmove', function() {
        clearTimeout(longPressTimer); // Cancelar si el usuario empieza a deslizar (scroll)
    });

    // Ocultar el botón si se toca en cualquier otro lugar de la pantalla
    document.body.addEventListener('click', function(e) {
        if (!e.target.closest('.message-wrapper.sent')) {
            hideAllDeleteButtons();
        }
    }, true); // Usar 'capture' para que se ejecute antes que otros clics

    // Delegación de eventos para el botón de eliminar
    chatMessages.addEventListener('click', function(e) {
        const deleteButton = e.target.closest('.btn-delete-msg');
        if (deleteButton) {
            const messageId = deleteButton.dataset.id;
            if (confirm('¿Estás seguro de que quieres eliminar este mensaje? Esta acción no se puede deshacer.')) {
                handleDeleteMessage(messageId);
            }
        }

        // Si fue un long press, evitamos que el clic haga otra cosa
        if (isLongPress) {
            e.preventDefault();
            isLongPress = false;
        }
    });

    // Función para manejar la eliminación de un mensaje
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
                // Si la respuesta no es 2xx, la convertimos en un error para el .catch()
                return response.json().then(err => { throw new Error(err.error || 'Error del servidor') });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Actualizar la UI para mostrar que el mensaje fue eliminado
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

    // Función para añadir un mensaje al DOM
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

        // Si el mensaje es del usuario actual, añadir el botón de eliminar
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

    // Polling para nuevos mensajes cada 3 segundos
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
