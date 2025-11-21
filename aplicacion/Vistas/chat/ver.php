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
        margin: 2rem auto;
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
        background-color: var(--primary);
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
            <div class="message-wrapper <?php echo ($mensaje['id_remitente'] == $datosVista['id_usuario_actual']) ? 'sent' : 'received'; ?>">
                <div class="message">
                    <p class="message-content"><?php echo htmlspecialchars($mensaje['contenido']); ?></p>
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

    // Función para añadir un mensaje al DOM
    function appendMessage(mensaje, typeClass) {
        const messageWrapper = document.createElement('div');
        messageWrapper.className = `message-wrapper ${typeClass}`;

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
