<?php require_once 'aplicacion/Vistas/plantillas/header.php'; ?>

<style>
    /* --- Layout Principal (Reemplazo de Bootstrap Grid) --- */
    .checkout-wrapper {
        display: grid;
        grid-template-columns: 1fr 380px; /* Contenido principal | Sidebar */
        gap: 2rem;
        padding: 6rem 0;
        max-width: 1200px;
        margin: 0 auto;
        align-items: start;
    }

    /* --- Tarjetas (Estilo visual de tu app) --- */
    .checkout-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
    }

    .checkout-title {
        font-size: 1.5rem;
        color: var(--secondary-color);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--primary-color);
        font-weight: 700;
    }

    /* --- Formulario --- */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 0.5rem;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    /* Estilos de Inputs (Igual a tu Login) */
    .custom-input, 
    .mp-container { /* Contenedor para iframes de MP */
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-size: 0.95rem;
        background: #fafafa;
        transition: all 0.3s ease;
        height: 48px; /* Altura fija para consistencia */
        box-sizing: border-box;
    }

    .custom-input:focus,
    .mp-container.mp-focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
    }

    /* --- Sidebar (Resumen) --- */
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-size: 1rem;
        color: var(--text-light);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
        font-weight: 700;
        font-size: 1.25rem;
        color: var(--primary-color);
    }

    .secure-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 1.5rem;
        padding: 1rem;
        background: var(--bg-light);
        border-radius: 8px;
    }

    /* --- Botón de Pago --- */
    .btn-pay {
        width: 100%;
        padding: 1rem;
        margin-top: 1.5rem;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(145, 2, 2, 0.3);
    }

    .btn-pay:hover:not(:disabled) {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-pay:disabled {
        background: #ccc;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Barra de progreso */
    #progressBar {
        width: 100%;
        height: 4px;
        background-color: #f0f0f0;
        border-radius: 2px;
        margin-top: 1rem;
        overflow: hidden;
        display: none;
    }
    
    #progressBar div {
        height: 100%;
        background-color: var(--primary-color);
        width: 0;
        transition: width 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .checkout-wrapper {
            grid-template-columns: 1fr;
            padding: 3rem 0;
        }
        
        .checkout-wrapper > div:last-child {
            order: -1; /* Mover resumen arriba en móviles */
        }
    }
    
    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .form-group.full-width {
            grid-column: span 1;
        }
    }

    /* --- Modal de Éxito --- */
    .success-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .success-modal-overlay.visible {
        opacity: 1;
        visibility: visible;
    }
    .success-modal-content {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        text-align: center;
        max-width: 400px;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }
    .success-modal-overlay.visible .success-modal-content {
        transform: scale(1);
    }
    .success-modal-icon {
        font-size: 4rem;
        color: var(--success-color);
        margin-bottom: 1rem;
    }
    .success-modal-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }
    .success-modal-text { color: var(--text-light); margin-bottom: 2rem; }
</style>

<div class="container">
    <div class="checkout-wrapper fade-in">
        
        <div class="checkout-card">
            <h2 class="checkout-title"><i class="far fa-credit-card"></i> Datos de la Tarjeta</h2>
            
            <form id="form-checkout">
                <div class="form-grid">
                    
                    <div class="form-group full-width">
                        <label class="form-label">Número de tarjeta</label>
                        <div id="form-checkout__cardNumber" class="mp-container"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Vencimiento (MM/YY)</label>
                        <div id="form-checkout__expirationDate" class="mp-container"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CVV / Código seguridad</label>
                        <div id="form-checkout__securityCode" class="mp-container"></div>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Nombre del titular</label>
                        <input type="text" id="form-checkout__cardholderName" class="custom-input" placeholder="Como aparece en la tarjeta" />
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Email del recibo</label>
                        <input type="email" id="form-checkout__cardholderEmail" class="custom-input" placeholder="ejemplo@correo.com" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo Documento</label>
                        <select id="form-checkout__identificationType" class="custom-input"></select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número Documento</label>
                        <input type="text" id="form-checkout__identificationNumber" class="custom-input" placeholder="DNI / Otro" />
                    </div>

                    <div style="display:none;">
                        <select id="form-checkout__issuer"></select>
                        <select id="form-checkout__installments"></select>
                    </div>

                </div>

                <div id="progressBar"><div></div></div>

                <button type="submit" id="form-checkout__submit" class="btn-pay">
                    <i class="fas fa-lock"></i> Pagar S/ <?php echo number_format($monto, 2); ?>
                </button>
            </form>
        </div>

        <div class="checkout-card">
            <h2 class="checkout-title">Resumen del Pedido</h2>
            
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <i class="fas fa-shopping-bag" style="font-size: 3rem; color: var(--border-color);"></i>
            </div>

            <div class="summary-item">
                <span>Producto</span>
                <strong style="color: var(--text-dark); max-width: 150px; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?php echo htmlspecialchars($titulo); ?>
                </strong>
            </div>

            <div class="summary-item">
                <span>Precio</span>
                <span>S/ <?php echo number_format($monto, 2); ?></span>
            </div>

            <div class="summary-total">
                <span>Total a Pagar</span>
                <span>S/ <?php echo number_format($monto, 2); ?></span>
            </div>

            <div class="secure-badge">
                <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: var(--success-color);"></i>
                <div>
                    <strong>Pago Seguro</strong><br>
                    Tus datos están protegidos con encriptación SSL de Mercado Pago.
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal de Éxito -->
<div id="successModal" class="success-modal-overlay">
    <div class="success-modal-content">
        <div class="success-modal-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 class="success-modal-title">¡Pago Exitoso!</h3>
        <p class="success-modal-text">Tu compra se ha realizado correctamente. Gracias por confiar en nosotros.</p>
        <a href="/perfil/mis-compras" class="btn-pay" style="text-decoration: none;">Ver mis compras</a>
    </div>
</div>


<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
    // --- Configuración Inicial ---
    const mpPublicKey = "<?php echo $mpPublicKey; ?>";
    const mp = new MercadoPago(mpPublicKey); // <--- ¡RECUERDA PONER TU KEY!

    const montoReal = "<?php echo $monto; ?>";
    const tituloProducto = "<?php echo $titulo; ?>";
    const idPublicacion = "<?php echo $id_publicacion; ?>";

    // Inicializamos cardForm
    const cardForm = mp.cardForm({
        amount: montoReal,
        iframe: true,
        form: {
            id: "form-checkout",
            cardNumber: { id: "form-checkout__cardNumber", placeholder: "0000 0000 0000 0000" },
            expirationDate: { id: "form-checkout__expirationDate", placeholder: "MM/YY" },
            securityCode: { id: "form-checkout__securityCode", placeholder: "123" },
            cardholderName: { id: "form-checkout__cardholderName", placeholder: "Titular de la tarjeta" },
            issuer: { id: "form-checkout__issuer", placeholder: "Banco emisor" },
            installments: { id: "form-checkout__installments", placeholder: "Cuotas" },
            identificationType: { id: "form-checkout__identificationType", placeholder: "Tipo Doc" },
            identificationNumber: { id: "form-checkout__identificationNumber", placeholder: "Número" },
            cardholderEmail: { id: "form-checkout__cardholderEmail", placeholder: "E-mail" },
        },
        callbacks: {
            onFormMounted: error => {
                if (error) return console.warn("Error montando formulario:", error);
            },
            
            // Efecto visual de foco en los iframes (para que se parezcan a tus inputs)
            onFocus: (e) => {
                const divId = e.field; 
                // e.field devuelve el nombre del campo, ej: "cardNumber"
                // Mapeamos al ID del div contenedor
                const map = {
                    cardNumber: "form-checkout__cardNumber",
                    expirationDate: "form-checkout__expirationDate",
                    securityCode: "form-checkout__securityCode"
                };
                const el = document.getElementById(map[divId]);
                if(el) el.classList.add('mp-focus');
            },
            onBlur: (e) => {
                const divId = e.field;
                const map = {
                    cardNumber: "form-checkout__cardNumber",
                    expirationDate: "form-checkout__expirationDate",
                    securityCode: "form-checkout__securityCode"
                };
                const el = document.getElementById(map[divId]);
                if(el) el.classList.remove('mp-focus');
            },

            onSubmit: event => {
                event.preventDefault();

                const btn = document.getElementById("form-checkout__submit");
                const progress = document.querySelector("#progressBar div");
                document.getElementById("progressBar").style.display = 'block';

                // UI Loading
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                progress.style.width = "50%";

                const {
                    paymentMethodId,
                    issuerId,
                    cardholderEmail,
                    amount,
                    token,
                    // installments, // NO leemos el select porque lo ocultamos
                    identificationNumber,
                    identificationType,
                } = cardForm.getCardFormData();

                fetch("/test-pasarela/procesar", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        token,
                        issuer_id: issuerId,
                        payment_method_id: paymentMethodId,
                        transaction_amount: Number(amount),
                        installments: 1, // <--- FORZAMOS SIEMPRE 1 CUOTA
                        description: tituloProducto,
                        id_publicacion: idPublicacion,
                        payer: {
                            email: cardholderEmail,
                            identification: { type: identificationType, number: identificationNumber }
                        }
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    progress.style.width = "100%";
                    
                    if(data.status === 'approved'){
                        btn.style.background = "var(--success-color)";
                        btn.innerHTML = '<i class="fas fa-check"></i> ¡Pago Exitoso!';
                        
                        // Mostrar el modal de éxito
                        document.getElementById('successModal').classList.add('visible');
                    } else {
                        throw new Error(data.detalle || "Pago rechazado");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Lo sentimos, no se pudo procesar el pago.\nMotivo: " + error.message);
                    
                    // Reset UI
                    btn.disabled = false;
                    btn.style.background = "var(--primary-color)";
                    btn.innerHTML = '<i class="fas fa-lock"></i> Pagar S/ ' + montoReal;
                    document.getElementById("progressBar").style.display = 'none';
                    progress.style.width = "0";
                });
            }
        },
    });
</script>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>