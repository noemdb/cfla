import "./bootstrap";

import 'flowbite';



// Initialization for ES Users
import { Tooltip, Tab, Carousel, Ripple, Animate, Chart, Offcanvas, Dropdown, Collapse, LazyLoad, SmoothScroll, Stepper, initTE } from "tw-elements";
initTE({ Tooltip, Tab, Carousel, Ripple, Animate, Chart, Offcanvas, Dropdown, Collapse, LazyLoad, SmoothScroll, Stepper });


// Funciones globales para el sistema de votación
window.VotingSystem = {
    // Generar fingerprint del navegador
    generateFingerprint() {
        const canvas = document.createElement("canvas")
        const ctx = canvas.getContext("2d")
        ctx.textBaseline = "top"
        ctx.font = "14px Arial"
        ctx.fillText("Fingerprint test", 2, 2)

        return btoa(
        JSON.stringify({
            userAgent: navigator.userAgent,
            language: navigator.language,
            platform: navigator.platform,
            screen: screen.width + "x" + screen.height,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            canvas: canvas.toDataURL(),
        }),
        ).slice(0, 32)
    },

    // Actualizar tiempo restante
    updateTimeRemaining(element, endTime) {
        const updateTimer = () => {
        const now = new Date().getTime()
        const remaining = endTime - now

        if (remaining <= 0) {
            element.textContent = "Expirada"
            element.className = element.className.replace("text-orange-600", "text-red-600")
            return
        }

        const hours = Math.floor(remaining / (1000 * 60 * 60))
        const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60))
        const seconds = Math.floor((remaining % (1000 * 60)) / 1000)

        if (hours > 0) {
            element.textContent = `${hours}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`
        } else {
            element.textContent = `${minutes}:${seconds.toString().padStart(2, "0")}`
        }
        }

        updateTimer()
        return setInterval(updateTimer, 1000)
    },
}
