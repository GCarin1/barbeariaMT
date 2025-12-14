/**
 * Don Barbero - JavaScript Principal
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

(function() {
    'use strict';

    // App namespace
    window.DonBarbero = {
        version: '1.0.0',
        
        /**
         * Inicializar aplicação
         */
        init: function() {
            console.log('Don Barbero v' + this.version + ' initialized');
            this.setupEventListeners();
            this.setupFormValidation();
        },
        
        /**
         * Configurar event listeners
         */
        setupEventListeners: function() {
            // Mobile menu toggle já implementado no layout
            
            // Fechar alerts ao clicar no X
            document.querySelectorAll('[data-dismiss="alert"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('[role="alert"]').remove();
                });
            });
        },
        
        /**
         * Validação de formulários no client-side
         */
        setupFormValidation: function() {
            const forms = document.querySelectorAll('form[data-validate]');
            
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        },
        
        /**
         * Mostrar loading
         */
        showLoading: function(element) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            if (element) {
                element.disabled = true;
                element.innerHTML = '<span class="spinner-custom inline-block"></span> Carregando...';
            }
        },
        
        /**
         * Esconder loading
         */
        hideLoading: function(element, originalText) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            if (element) {
                element.disabled = false;
                element.innerHTML = originalText;
            }
        },
        
        /**
         * Formatar telefone brasileiro
         */
        formatPhone: function(value) {
            value = value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
            } else {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            }
            return value;
        },
        
        /**
         * Validar email
         */
        isValidEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    };
    
    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.DonBarbero.init();
        });
    } else {
        window.DonBarbero.init();
    }
})();
