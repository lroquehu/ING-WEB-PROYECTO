<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red de Emprendedores Universitarios</title>
    <style>
        :root {
            --primary-color: #910202;
            --primary-dark: #510200;
            --primary-light: #b30303;
            --accent-color: #ffd700;
            --bg-white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: var(--bg-white);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="rgba(255,255,255,0.05)"><circle cx="50" cy="50" r="2"/></svg>') repeat;
            z-index: -1;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
            z-index: 10;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .network-container {
            position: relative;
            width: 90vw;
            height: 70vh;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        
        #network-canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        
        .network-stats {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 0.9rem;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .network-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        
        .network-btn {
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        
        .network-btn:hover {
            background: rgba(145, 2, 2, 0.8);
            transform: scale(1.1);
        }
        
        .info-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 15px;
            border-radius: 10px;
            max-width: 300px;
            backdrop-filter: blur(5px);
        }
        
        .info-panel h3 {
            margin-bottom: 10px;
            color: var(--accent-color);
        }
        
        .info-panel p {
            font-size: 0.9rem;
            line-height: 1.4;
            opacity: 0.9;
        }
        
        .legend {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .footer {
            margin-top: 2rem;
            text-align: center;
            opacity: 0.8;
            font-size: 0.9rem;
            z-index: 10;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .header p {
                font-size: 1rem;
            }
            
            .info-panel {
                max-width: 200px;
                padding: 10px;
            }
            
            .info-panel h3 {
                font-size: 1rem;
            }
            
            .info-panel p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Red de Emprendedores Universitarios</h1>
        <p>Conectando talento y oportunidades en tiempo real</p>
    </div>
    
    <div class="network-container">
        <canvas id="network-canvas"></canvas>
        
        <div class="info-panel">
            <h3>Red Viva de Conexiones</h3>
            <p>Cada nodo representa un emprendedor universitario. Las conexiones muestran colaboraciones e intercambios en la plataforma.</p>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #ffd700;"></div>
                    <span>Productos</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #ff6b6b;"></div>
                    <span>Servicios</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #ffffff;"></div>
                    <span>Colaboraciones</span>
                </div>
            </div>
        </div>
        
        <div class="network-stats">
            <i class="fas fa-users"></i> 
            <span id="node-count">50</span> emprendedores conectados
        </div>
        
        <div class="network-controls">
            <button class="network-btn" id="reset-view" title="Restablecer vista">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button class="network-btn" id="toggle-animation" title="Pausar/Reanudar animación">
                <i class="fas fa-pause"></i>
            </button>
            <button class="network-btn" id="add-node" title="Agregar emprendedor">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    
    <div class="footer">
        <p>UniEmprende - Plataforma Universitaria de Emprendimiento</p>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('network-canvas');
            const ctx = canvas.getContext('2d');
            const nodeCountElement = document.getElementById('node-count');
            const resetButton = document.getElementById('reset-view');
            const toggleButton = document.getElementById('toggle-animation');
            const addNodeButton = document.getElementById('add-node');
            
            // Ajustar tamaño del canvas
            function resizeCanvas() {
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
            }
            
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            
            // Configuración de la red
            const config = {
                nodeCount: 50,
                maxConnections: 4,
                nodeRadius: 8,
                connectionDistance: 180,
                repulsionForce: 0.1,
                centerForce: 0.008,
                animationSpeed: 1,
                isAnimating: true,
                dataTransmissionSpeed: 0.02
            };
            
            // Colores adaptados al estilo de la plataforma
            const colors = [
                '#FFD700', // Amarillo/dorado - Productos
                '#FF6B6B', // Rojo claro - Servicios
                '#FFFFFF'  // Blanco - Colaboraciones
            ];
            
            // Datos de ejemplo para emprendedores
            const entrepreneurTypes = ['producto', 'servicio', 'colaboracion'];
            const universities = ['UNI', 'San Marcos', 'Católica', 'ULima', 'Pacifico', 'UPN', 'UPC', 'Científica'];
            
            // Clase Nodo
            class Node {
                constructor(id) {
                    this.id = id;
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.vx = (Math.random() - 0.5) * 2;
                    this.vy = (Math.random() - 0.5) * 2;
                    this.radius = config.nodeRadius;
                    this.type = entrepreneurTypes[Math.floor(Math.random() * entrepreneurTypes.length)];
                    this.color = this.getColorByType();
                    this.connections = [];
                    this.pulse = Math.random() * Math.PI * 2;
                    this.university = universities[Math.floor(Math.random() * universities.length)];
                    this.dataPackets = [];
                    this.isTransmitting = false;
                }
                
                getColorByType() {
                    switch(this.type) {
                        case 'producto': return colors[0];
                        case 'servicio': return colors[1];
                        case 'colaboracion': return colors[2];
                        default: return colors[0];
                    }
                }
                
                update() {
                    // Aplicar fuerzas de repulsión con otros nodos
                    for (let node of nodes) {
                        if (node !== this) {
                            const dx = this.x - node.x;
                            const dy = this.y - node.y;
                            const distance = Math.sqrt(dx * dx + dy * dy);
                            
                            if (distance < 120) {
                                const force = config.repulsionForce / (distance * distance);
                                this.vx += (dx / distance) * force;
                                this.vy += (dy / distance) * force;
                            }
                        }
                    }
                    
                    // Fuerza hacia el centro
                    const centerX = canvas.width / 2;
                    const centerY = canvas.height / 2;
                    const dx = centerX - this.x;
                    const dy = centerY - this.y;
                    this.vx += dx * config.centerForce;
                    this.vy += dy * config.centerForce;
                    
                    // Actualizar posición
                    this.x += this.vx * config.animationSpeed;
                    this.y += this.vy * config.animationSpeed;
                    
                    // Rebote en los bordes
                    if (this.x < this.radius || this.x > canvas.width - this.radius) {
                        this.vx *= -0.8;
                        this.x = Math.max(this.radius, Math.min(canvas.width - this.radius, this.x));
                    }
                    if (this.y < this.radius || this.y > canvas.height - this.radius) {
                        this.vy *= -0.8;
                        this.y = Math.max(this.radius, Math.min(canvas.height - this.radius, this.y));
                    }
                    
                    // Amortiguación
                    this.vx *= 0.99;
                    this.vy *= 0.99;
                    
                    // Actualizar pulso
                    this.pulse += 0.05;
                    
                    // Transmisión de datos ocasional
                    if (Math.random() < 0.005 && this.connections.length > 0 && !this.isTransmitting) {
                        this.transmitData();
                    }
                    
                    // Actualizar paquetes de datos
                    this.updateDataPackets();
                }
                
                transmitData() {
                    this.isTransmitting = true;
                    const targetId = this.connections[Math.floor(Math.random() * this.connections.length)];
                    const target = nodes[targetId];
                    
                    if (target) {
                        this.dataPackets.push({
                            targetX: target.x,
                            targetY: target.y,
                            progress: 0,
                            color: this.color
                        });
                        
                        // Simular finalización de transmisión
                        setTimeout(() => {
                            this.isTransmitting = false;
                        }, 1000);
                    }
                }
                
                updateDataPackets() {
                    for (let i = this.dataPackets.length - 1; i >= 0; i--) {
                        const packet = this.dataPackets[i];
                        packet.progress += config.dataTransmissionSpeed;
                        
                        if (packet.progress >= 1) {
                            this.dataPackets.splice(i, 1);
                        }
                    }
                }
                
                draw() {
                    // Dibujar conexiones primero
                    for (let connection of this.connections) {
                        const target = nodes[connection];
                        if (target) {
                            const dx = target.x - this.x;
                            const dy = target.y - this.y;
                            const distance = Math.sqrt(dx * dx + dy * dy);
                            
                            if (distance < config.connectionDistance) {
                                // La opacidad disminuye con la distancia
                                const opacity = 1 - (distance / config.connectionDistance);
                                ctx.strokeStyle = `rgba(255, 255, 255, ${opacity * 0.3})`;
                                ctx.lineWidth = 1;
                                ctx.beginPath();
                                ctx.moveTo(this.x, this.y);
                                ctx.lineTo(target.x, target.y);
                                ctx.stroke();
                            }
                        }
                    }
                    
                    // Dibujar paquetes de datos en tránsito
                    for (let packet of this.dataPackets) {
                        const x = this.x + (packet.targetX - this.x) * packet.progress;
                        const y = this.y + (packet.targetY - this.y) * packet.progress;
                        
                        ctx.beginPath();
                        ctx.arc(x, y, 3, 0, Math.PI * 2);
                        ctx.fillStyle = packet.color;
                        ctx.fill();
                        
                        // Efecto de brillo
                        ctx.beginPath();
                        ctx.arc(x, y, 5, 0, Math.PI * 2);
                        ctx.fillStyle = `rgba(255, 255, 255, ${0.5 * (1 - packet.progress)})`;
                        ctx.fill();
                    }
                    
                    // Dibujar el nodo
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    
                    // Efecto de pulso
                    const pulseSize = Math.sin(this.pulse) * 0.5 + 1;
                    const currentRadius = this.radius * pulseSize;
                    
                    // Gradiente para el nodo (inspirado en la imagen de referencia)
                    const gradient = ctx.createRadialGradient(
                        this.x, this.y, 0,
                        this.x, this.y, currentRadius * 1.5
                    );
                    
                    if (this.type === 'producto') {
                        // Gradiente amarillo/dorado
                        gradient.addColorStop(0, '#FFD700');
                        gradient.addColorStop(0.7, '#FFA500');
                        gradient.addColorStop(1, 'rgba(255, 215, 0, 0.3)');
                    } else if (this.type === 'servicio') {
                        // Gradiente rojo
                        gradient.addColorStop(0, '#FF6B6B');
                        gradient.addColorStop(0.7, '#FF4757');
                        gradient.addColorStop(1, 'rgba(255, 107, 107, 0.3)');
                    } else {
                        // Gradiente blanco/azulado para colaboraciones
                        gradient.addColorStop(0, '#FFFFFF');
                        gradient.addColorStop(0.7, '#E8E8E8');
                        gradient.addColorStop(1, 'rgba(255, 255, 255, 0.3)');
                    }
                    
                    ctx.fillStyle = gradient;
                    ctx.fill();
                    
                    // Borde del nodo
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.8)';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                    
                    // Efecto de brillo interno (como en la imagen de referencia)
                    if (this.isTransmitting) {
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, currentRadius * 2, 0, Math.PI * 2);
                        const glowGradient = ctx.createRadialGradient(
                            this.x, this.y, currentRadius,
                            this.x, this.y, currentRadius * 2
                        );
                        glowGradient.addColorStop(0, this.color);
                        glowGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
                        ctx.fillStyle = glowGradient;
                        ctx.fill();
                    }
                }
            }
            
            // Crear nodos
            const nodes = [];
            for (let i = 0; i < config.nodeCount; i++) {
                nodes.push(new Node(i));
            }
            
            // Crear conexiones aleatorias
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];
                const maxConnections = Math.floor(Math.random() * config.maxConnections) + 1;
                
                for (let j = 0; j < maxConnections; j++) {
                    const randomIndex = Math.floor(Math.random() * nodes.length);
                    if (randomIndex !== i && !node.connections.includes(randomIndex)) {
                        node.connections.push(randomIndex);
                    }
                }
            }
            
            // Animación
            function animate() {
                if (!config.isAnimating) return;
                
                // Limpiar canvas con fondo semitransparente para efecto de rastro
                ctx.fillStyle = 'rgba(81, 2, 0, 0.05)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                // Actualizar y dibujar nodos
                for (let node of nodes) {
                    node.update();
                    node.draw();
                }
                
                requestAnimationFrame(animate);
            }
            
            // Iniciar animación
            animate();
            
            // Interacción con el canvas
            let mouseX = 0;
            let mouseY = 0;
            let isMouseDown = false;
            
            canvas.addEventListener('mousemove', function(e) {
                const rect = canvas.getBoundingClientRect();
                mouseX = e.clientX - rect.left;
                mouseY = e.clientY - rect.top;
                
                if (isMouseDown) {
                    // Empujar nodos cercanos al cursor
                    for (let node of nodes) {
                        const dx = node.x - mouseX;
                        const dy = node.y - mouseY;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        if (distance < 100) {
                            const force = 50 / (distance * distance);
                            node.vx += (dx / distance) * force;
                            node.vy += (dy / distance) * force;
                        }
                    }
                }
            });
            
            canvas.addEventListener('mousedown', function() {
                isMouseDown = true;
            });
            
            canvas.addEventListener('mouseup', function() {
                isMouseDown = false;
            });
            
            canvas.addEventListener('mouseleave', function() {
                isMouseDown = false;
            });
            
            // Controles
            resetButton.addEventListener('click', function() {
                for (let node of nodes) {
                    node.x = Math.random() * canvas.width;
                    node.y = Math.random() * canvas.height;
                    node.vx = (Math.random() - 0.5) * 2;
                    node.vy = (Math.random() - 0.5) * 2;
                }
            });
            
            toggleButton.addEventListener('click', function() {
                config.isAnimating = !config.isAnimating;
                toggleButton.innerHTML = config.isAnimating ? 
                    '<i class="fas fa-pause"></i>' : 
                    '<i class="fas fa-play"></i>';
                
                if (config.isAnimating) {
                    animate();
                }
            });
            
            addNodeButton.addEventListener('click', function() {
                if (nodes.length < 100) { // Límite máximo de nodos
                    const newNode = new Node(nodes.length);
                    nodes.push(newNode);
                    
                    // Conectar con algunos nodos existentes
                    const connections = Math.floor(Math.random() * 3) + 1;
                    for (let i = 0; i < connections; i++) {
                        const randomIndex = Math.floor(Math.random() * nodes.length - 1);
                        if (randomIndex >= 0) {
                            newNode.connections.push(randomIndex);
                        }
                    }
                    
                    config.nodeCount = nodes.length;
                    nodeCountElement.textContent = config.nodeCount;
                }
            });
            
            // Actualizar contador de nodos
            nodeCountElement.textContent = config.nodeCount;
        });
    </script>
</body>
</html>