import "./bootstrap";

import "flowbite";

// Initialization for ES Users
import {
    Tooltip,
    Tab,
    Carousel,
    Ripple,
    Animate,
    Chart,
    Offcanvas,
    Dropdown,
    Collapse,
    LazyLoad,
    SmoothScroll,
    Stepper,
    initTE,
} from "tw-elements";
initTE({
    Tooltip,
    Tab,
    Carousel,
    Ripple,
    Animate,
    Chart,
    Offcanvas,
    Dropdown,
    Collapse,
    LazyLoad,
    SmoothScroll,
    Stepper,
});

// Funciones globales para el sistema de votación
window.VotingSystem = {
    // Generar fingerprint del navegador
    generateFingerprint() {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        ctx.textBaseline = "top";
        ctx.font = "14px Arial";
        ctx.fillText("Fingerprint test", 2, 2);

        return btoa(
            JSON.stringify({
                userAgent: navigator.userAgent,
                language: navigator.language,
                platform: navigator.platform,
                screen: screen.width + "x" + screen.height,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                canvas: canvas.toDataURL(),
            })
        ).slice(0, 32);
    },

    // Detectar IP privada usando WebRTC
    async detectPrivateIP() {
        return new Promise((resolve) => {
            const pc = new RTCPeerConnection({
                iceServers: [{ urls: "stun:stun.l.google.com:19302" }],
            });

            let privateIP = null;

            pc.createDataChannel("");

            pc.onicecandidate = (event) => {
                if (event.candidate) {
                    const candidate = event.candidate.candidate;
                    const match = candidate.match(/(\d+\.\d+\.\d+\.\d+)/);

                    if (match) {
                        const ip = match[1];
                        // Verificar si es IP privada
                        if (this.isPrivateIP(ip)) {
                            privateIP = ip;
                            pc.close();
                            resolve(privateIP);
                        }
                    }
                }
            };

            pc.createOffer()
                .then((offer) => pc.setLocalDescription(offer))
                .catch(() => resolve(null));

            // Timeout después de 3 segundos
            setTimeout(() => {
                pc.close();
                resolve(privateIP);
            }, 3000);
        });
    },

    // Verificar si una IP es privada
    isPrivateIP(ip) {
        const parts = ip.split(".").map(Number);

        // 10.0.0.0 - 10.255.255.255
        if (parts[0] === 10) return true;

        // 172.16.0.0 - 172.31.255.255
        if (parts[0] === 172 && parts[1] >= 16 && parts[1] <= 31) return true;

        // 192.168.0.0 - 192.168.255.255
        if (parts[0] === 192 && parts[1] === 168) return true;

        return false;
    },

    // Actualizar tiempo restante
    updateTimeRemaining(element, endTime) {
        const updateTimer = () => {
            const now = new Date().getTime();
            const remaining = endTime - now;

            if (remaining <= 0) {
                element.textContent = "Expirada";
                element.className = element.className.replace(
                    "text-orange-600",
                    "text-red-600"
                );
                return;
            }

            const hours = Math.floor(remaining / (1000 * 60 * 60));
            const minutes = Math.floor(
                (remaining % (1000 * 60 * 60)) / (1000 * 60)
            );
            const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

            if (hours > 0) {
                element.textContent = `${hours}:${minutes
                    .toString()
                    .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
            } else {
                element.textContent = `${minutes}:${seconds
                    .toString()
                    .padStart(2, "0")}`;
            }
        };

        updateTimer();
        return setInterval(updateTimer, 1000);
    },
};

// Detectar IP privada automáticamente cuando se carga la página
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const privateIP = await window.VotingSystem.detectPrivateIP();
        if (privateIP && window.Livewire) {
            // Enviar IP privada al componente Livewire si existe
            window.Livewire.dispatch("privateIpDetected", {
                privateIp: privateIP,
            });
        }
    } catch (error) {
        console.warn("No se pudo detectar la IP privada:", error);
    }
});
