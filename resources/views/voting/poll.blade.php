@extends('layouts.vote')

@section('title', ($poll->title ?? 'Votación') . ' - Sistema de Votación')

@section('content')
    <div class="relative">
        <!-- Elementos decorativos de fondo para tema oscuro -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Círculos de luz verde difuminados -->
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-emerald-500/10 to-green-600/10 rounded-full blur-3xl">
            </div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-green-600/10 to-emerald-500/10 rounded-full blur-3xl">
            </div>

            <!-- Efectos de luz adicionales -->
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-emerald-900/5 to-green-900/5 rounded-full blur-3xl">
            </div>

            <!-- Partículas de luz sutil -->
            <div class="absolute top-20 right-20 w-2 h-2 bg-emerald-400/30 rounded-full animate-pulse"></div>
            <div class="absolute bottom-32 left-16 w-1 h-1 bg-green-400/40 rounded-full animate-pulse"
                style="animation-delay: 1s;"></div>
            <div class="absolute top-1/3 right-1/3 w-1.5 h-1.5 bg-emerald-300/20 rounded-full animate-pulse"
                style="animation-delay: 2s;"></div>
        </div>

        <!-- Contenido principal -->
        <div class="relative z-10">
            <livewire:app.voting.voting-poll :access-token="$accessToken" />
        </div>
    </div>


@endsection


@section('script')
    @parent
    <!-- Script para copiar al portapapeles -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('copy-to-clipboard', (event) => {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(event.text).then(() => {
                        console.log('Texto copiado al portapapeles');
                    }).catch(err => {
                        console.error('Error al copiar: ', err);
                        fallbackCopyTextToClipboard(event.text);
                    });
                } else {
                    fallbackCopyTextToClipboard(event.text);
                }
            });
        });

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    console.log('Texto copiado usando fallback');
                }
            } catch (err) {
                console.error('Error en fallback copy: ', err);
            }

            document.body.removeChild(textArea);
        }

        function shareParticipation(url, title) {
            const shareData = {
                title: title,
                text: 'He participado en esta encuesta. Ve los detalles de mi participación:',
                url: url
            };

            if (navigator.share) {
                navigator.share(shareData).catch(err => {
                    console.log('Error sharing: ', err);
                    fallbackShare(url);
                });
            } else {
                fallbackShare(url);
            }
        }

        function fallbackShare(url) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    showNotification('Enlace copiado al portapapeles para compartir', 'success');
                });
            } else {
                fallbackCopyTextToClipboard(url);
                showNotification('Enlace copiado al portapapeles', 'success');
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300 ${
                type === 'success' ? 'bg-green-600 text-white' :
                type === 'error' ? 'bg-red-600 text-white' :
                'bg-blue-600 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    </script>
@endsection
