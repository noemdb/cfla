<style>
    :root {
        --primary-green: #064e3b;
        --secondary-green: #065f46;
        --accent-green: #10b981;
        --dark-bg: #111827;
        --card-bg: #1f2937;
    }

    /* Animaciones suaves para cards */
    .diagnostic-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(135deg, var(--card-bg) 0%, var(--primary-green) 100%);
    }

    .diagnostic-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.15);
    }

    .diagnostic-card:active {
        transform: translateY(-2px);
    }

    /* Opciones de preguntas */
    .question-option {
        transition: all 0.2s ease-in-out;
    }

    .question-option:hover {
        background-color: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .question-option.selected {
        background-color: rgba(16, 185, 129, 0.2);
        border-color: #10b981;
    }

    /* Pulse animation para elementos importantes */
    .pulse-green {
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
    }

    /* Gradientes personalizados */
    .gradient-diagnostic-primary {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
    }

    .gradient-diagnostic-secondary {
        background: linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%);
    }

    .gradient-diagnostic-card {
        background: linear-gradient(135deg, #1f2937 0%, #064e3b 30%, #1f2937 100%);
    }

    /* Efectos de brillo */
    .glow-emerald {
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
    }

    .glow-emerald-strong {
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
    }

    /* Scrollbar personalizado */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #1f2937;
    }

    ::-webkit-scrollbar-thumb {
        background: #059669;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #10b981;
    }

    /* Animación de entrada para elementos */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Efectos para botones */
    .btn-diagnostic {
        background: linear-gradient(135deg, #059669, #10b981);
        transition: all 0.3s ease;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-diagnostic:hover {
        background: linear-gradient(135deg, #047857, #059669);
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
    }

    .btn-diagnostic:active {
        transform: translateY(0);
    }
</style>
