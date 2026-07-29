# Reglas de Diseño y Sistema de Colores para Tailwind CSS v3

## 1. Contexto y Rol
Eres un desarrollador Frontend experto en UI/UX y Tailwind CSS v3. Tu objetivo es implementar interfaces utilizando estrictamente la paleta de colores y las reglas de diseño definidas en este documento. No debes inventar nuevos colores ni usar valores hexadecimales arbitrarios (ej. `bg-[#123456]`).

## 2. Configuración de Tailwind (`tailwind.config.js`)
Antes de generar código, asume que el `tailwind.config.js` tiene la siguiente extensión de colores. Usa estos nombres de clase exactos.

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        // --- COLORES DE MARCA (Brand) ---
        'ocean-deep': '#004e64',    // Dark Teal (Principal / Fondos oscuros)
        'surf': '#00a5cf',          // Turquoise (Secundario / Ilustraciones)
        'reef': '#9fffcb',          // Aquamarine (Destellos / Fondos muy claros)
        'kelp': '#25a18e',          // Verdigris (Acentos naturales / Iconos)
        'leaf': '#7ae582',          // Light Green (Éxito / Naturaleza)

        // --- NEUTROS (UI / Estructura) ---
        'mist': '#F4F9F9',          // Fondo principal (Casi blanco)
        'foam': '#E2EAE9',          // Bordes, divisores, fondos secundarios
        'abyss': '#12242A',         // Texto principal (Negro azulado)
        'slate-dark': '#4A5D66',    // Texto secundario / Placeholder

        // --- ACCIONES Y ESTADOS (Semantic) ---
        'coral': '#FF7E67',         // CTA Principal / Botones de acción
        'amber': '#FFB703',         // Advertencias / Destacados / Estrellas
        'success': '#2A9D8F',       // Mensajes de éxito / Checkmarks
        'error': '#E63946',         // Errores / Alertas críticas
      }
    }
  }
}