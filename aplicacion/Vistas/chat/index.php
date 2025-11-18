<?php include 'aplicacion/Vistas/plantillas/encabezado.php'; ?>
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

<div class="chat-container list-container">
    <h1 class="chat-title">Mis Mensajes</h1>

    <?php if (isset($_SESSION['error_chat'])): ?>
        <div class="chat-alert error"><?php echo $_SESSION['error_chat']; unset($_SESSION['error_chat']); ?></div>
    <?php endif; ?>

    <div class="conversations-list">
        <?php if (empty($datosVista['conversaciones'])): ?>
            <p class="no-conversations">No tienes ninguna conversación activa.</p>
        <?php else: ?>
            <?php foreach ($datosVista['conversaciones'] as $conv): ?>
                <a href="<?php echo BASE_URL . 'chat/ver/' . $conv['id_conversacion']; ?>" class="conversation-item <?php echo ($conv['no_leidos'] > 0) ? 'unread' : ''; ?>">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="conversation-details">
                        <div class="conversation-header">
                            <span class="user-name"><?php echo htmlspecialchars($conv['nombres'] . ' ' . $conv['apellidos']); ?></span>
                            <span class="conversation-time"><?php echo date('d/m/y H:i', strtotime($conv['fecha_ultimo_mensaje'] ?? $conv['fecha_actualizacion'])); ?></span>
                        </div>
                        <p class="last-message">
                            <?php echo htmlspecialchars(substr($conv['ultimo_mensaje'] ?? 'Inicia la conversación...', 0, 50)); ?>
                            <?php if (strlen($conv['ultimo_mensaje'] ?? '') > 50) echo '...'; ?>
                        </p>
                    </div>
                    <?php if ($conv['no_leidos'] > 0): ?>
                        <div class="unread-count"><?php echo $conv['no_leidos']; ?></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'aplicacion/Vistas/plantillas/pie.php'; ?>
