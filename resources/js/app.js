import "./bootstrap";

import 'flowbite';



// Initialization for ES Users
import { Tooltip, Tab, Carousel, Ripple, Animate, Chart, Offcanvas, Dropdown, Collapse, LazyLoad, SmoothScroll, Stepper, initTE } from "tw-elements";
initTE({ Tooltip, Tab, Carousel, Ripple, Animate, Chart, Offcanvas, Dropdown, Collapse, LazyLoad, SmoothScroll, Stepper });


import gsap from 'gsap';

document.addEventListener("DOMContentLoaded", function () {
    const splashScreen = document.getElementById("splash-screen");
    const appContent = document.getElementById("app");

    // Fade-in para el splash screen
    gsap.fromTo(
        splashScreen,
        { opacity: 0 }, // Inicio: completamente invisible
        {
            opacity: 1, // Final: completamente visible
            duration: 1.5, // Duración del fade-in
            onComplete: () => {
                // Delay de 3 segundos antes del fade-out
                gsap.to(splashScreen, {
                    opacity: 0, // Desaparecer
                    duration: 1.5, // Duración del fade-out
                    delay: 3, // Tiempo visible del splash screen
                    onComplete: () => {
                        splashScreen.style.display = "none"; // Oculta el splash
                        appContent.classList.remove("hidden"); // Muestra el contenido
                    },
                });
            },
        }
    );
});