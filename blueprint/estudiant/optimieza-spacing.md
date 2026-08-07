Plan de Implementación: Optimización del Espacio en Modo Libro (Flipbook)                                                                                              
                                                                                                                                                                        
 🎯 Objetivo                                                                                                                                                            
                                                                                                                                                                        
 Reducir el scroll vertical en el modo libro (flipbook) de ActivityView mostrando más contenido por página mediante cinco optimizaciones aprobadas:                     
 1. Reducción de espaciado interno (padding/margins)                                                                                                                    
 2. Ajuste del cálculo de dimensiones de StPageFlip                                                                                                                     
 3. Estilos condensados específicos para modo libro                                                                                                                     
 4. Optimización del encabezado de sección                                                                                                                              
 5. Tipografía optimizada para libro                                                                                                                                    
                                                                                                                                                                        
 │ Nota: La opción 5 (refinamiento del indicador de página) no fue aprobada y no se incluye en este plan.                                                               
                                                                                                                                                                        
 📁 Archivos a Modificar y Cambios Específicos                                                                                                                          
                                                                                                                                                                        
 ### 1. resources/views/livewire/student/lms/_flipbook-page.blade.php                                                                                                   
                                                                                                                                                                        
 (Combina las Opciones 1 y 4)                                                                                                                                           
                                                                                                                                                                        
 Cambios a realizar:                                                                                                                                                    
                                                                                                                                                                        
 ```blade                                                                                                                                                               
   @props(['section' => null])                                                                                                                                          
                                                                                                                                                                        
   @php                                                                                                                                                                 
       // Variables de acento replicadas de activity-view.blade.php (design §6.3):                                                                                      
       $sectionUpper = mb_strtoupper($section->title ?? '');                                                                                                            
       $accentColor = 'mint';                                                                                                                                           
       $accentDot = 'bg-emerald-500';                                                                                                                                   
       $accentRing = 'ring-emerald-500/20';                                                                                                                             
       $badgeLabel = null;                                                                                                                                              
       $badgeClass = '';                                                                                                                                                
                                                                                                                                                                        
       if (preg_match('/\b(INICIO|INTRODUCCI[OÓ]N|APERTURA|BIENVENIDA|PRESENTACI[OÓ]N)\b/', $sectionUpper)) {                                                           
           $badgeLabel = 'INICIO';                                                                                                                                      
           $badgeClass = 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20';                              
           $accentColor = 'blue';                                                                                                                                       
           $accentDot = 'bg-blue-500';                                                                                                                                  
           $accentRing = 'ring-blue-500/20';                                                                                                                            
       } elseif (preg_match('/\b(DESARROLLO|ACTIVIDAD|CONTENIDO|EXPLICACI[OÓ]N|EJERCICIO|PR[AÁ]CTICA|AN[AÁ]LISIS|PROFUNDIZACI[OÓ]N|REFLEXI[OÓ]N|LECTURA|TEMA)\b/',      
 $sectionUpper)) {                                                                                                                                                      
           $badgeLabel = 'DESARROLLO';                                                                                                                                  
           $badgeClass = 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30';           
           $accentColor = 'mint';                                                                                                                                       
           $accentDot = 'bg-emerald-500';                                                                                                                               
           $accentRing = 'ring-emerald-500/20';                                                                                                                         
       } elseif (preg_match('/\b(CIERRE|CONCLUSI[OÓ]N|RESUMEN|EVALUACI[OÓ]N|REPASO|S[IÍ]NTESIS|FINAL|RETROALIMENTACI[OÓ]N)\b/', $sectionUpper)) {                       
           $badgeLabel = 'CIERRE';                                                                                                                                      
           $badgeClass = 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20';                        
           $accentColor = 'amber';                                                                                                                                      
           $accentDot = 'bg-amber-500';                                                                                                                                 
           $accentRing = 'ring-amber-500/20';                                                                                                                           
       }                                                                                                                                                                
   @endphp                                                                                                                                                              
                                                                                                                                                                        
   <div class="stf__item">                                                                                                                                              
       @php                                                                                                                                                             
           // Página izquierda del pliego → el lomo queda a la derecha; página derecha → lomo a la izquierda.                                                           
           $isLeftPage = ($loop->index % 2 === 0);                                                                                                                      
       @endphp                                                                                                                                                          
       <div class="relative flex flex-col h-full p-4 sm:p-6 md:p-8 bg-[#fcfaf4] dark:bg-[#23211b]">                                                                     
           {{-- Sombra del lomo: da profundidad de libro abierto sobre el pliegue central --}}                                                                          
           <span aria-hidden="true"                                                                                                                                     
                 class="pointer-events-none absolute inset-y-0 w-1/3 {{ $isLeftPage ? 'right-0 bg-[linear-gradient(to_left,rgba(31,28,20,0.12),transparent_85%)]' :     
 'left-0 bg-[linear-gradient(to_right,rgba(31,28,20,0.12),transparent_85%)]' }}"></span>                                                                                
                                                                                                                                                                        
           {{-- Section header (misma identidad visual que el scroll) --}}                                                                                              
           <div class="flex items-center gap-1.5 pb-2 border-b border-emerald-200 dark:border-gray-700/40">                                                             
               <span class="w-0.5 h-4 rounded-full {{ $accentDot }} shrink-0"></span>                                                                                   
               <h2 class="text-xs sm:text-[13px] font-display font-bold text-gray-900 dark:text-white flex-1 min-w-0 leading-snug">                                     
                   {{ $section->title }}                                                                                                                                
               </h2>                                                                                                                                                    
               @if($badgeLabel)                                                                                                                                         
                   <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0 rounded-full text-[9px] font-semibold uppercase tracking-wider {{ $badgeClass }}">  
                       {{ $badgeLabel }}                                                                                                                                
                   </span>                                                                                                                                              
               @endif                                                                                                                                                   
           </div>                                                                                                                                                       
                                                                                                                                                                        
           {{-- Contenido de la sección, vía partial compartido en mode='book' --}}                                                                                     
           <div class="mt-2 space-y-1 flex-1 min-h-0 overflow-y-auto">                                                                                                  
               @foreach($section->visibleContents as $idx => $content)                                                                                                  
                   @include('livewire.student.lms._content-renderer', [                                                                                                 
                       'content' => $content,                                                                                                                           
                       'mode' => 'book',                                                                                                                                
                       'stepNum' => $idx + 1,                                                                                                                           
                       'isLast' => $loop->last,                                                                                                                         
                       'sectionId' => $section->id,                                                                                                                     
                   ])                                                                                                                                                   
               @endforeach                                                                                                                                              
           </div>                                                                                                                                                       
                                                                                                                                                                        
           {{-- Pie de página: el @include hereda $loop del @foreach($sections) de activity-view --}}                                                                   
           <div class="mt-1.5 pt-1.5 border-t border-gray-200 dark:border-gray-700/40 flex items-center justify-between text-[10px] font-semibold text-gray-400         
 dark:text-gray-500">                                                                                                                                                   
               <span>Página {{ $loop->iteration }} de {{ $loop->count }}</span>                                                                                         
               <span class="uppercase tracking-wider">{{ $accentColor }}</span>                                                                                         
           </div>                                                                                                                                                       
       </div>                                                                                                                                                           
   </div>                                                                                                                                                               
 ```                                                                                                                                                                    
                                                                                                                                                                        
 Impacto estimado: Reduce aproximadamente un 25-30% el espacio vertical no esencial en cada página del libro.                                                           
                                                                                                                                                                        
 ### 2. resources/views/livewire/student/lms/activity-view.blade.php                                                                                                    
                                                                                                                                                                        
 (Contiene los cambios para las Opciones 2 y 6)                                                                                                                         
                                                                                                                                                                        
 #### Parte A: Función dimensions() del componente lessonBook (Opción 2)                                                                                                
                                                                                                                                                                        
 Reemplace la función existente dimensions() por:                                                                                                                       
                                                                                                                                                                        
 ```javascript                                                                                                                                                          
   dimensions() {                                                                                                                                                       
       const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);                                                                          
       const vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);                                                                        
       const isPortrait = vw < 768; // breakpoint md de Tailwind                                                                                                        
       const ratio = isPortrait ? 1.4 : 1.1; // Antes: 1.5 (retrato) / 1.2 (paisaje)                                                                                    
                                                                                                                                                                        
       let width, height;                                                                                                                                               
       if (isPortrait) {                                                                                                                                                
           height = Math.min(vh * 0.88, vw * ratio); // Antes: 0.8                                                                                                      
           width = height / ratio;                                                                                                                                      
       } else {                                                                                                                                                         
           width = Math.min(vw * 0.88, vh / ratio); // Antes: 0.8                                                                                                       
           height = width * ratio;                                                                                                                                      
       }                                                                                                                                                                
       return { width, height, isPortrait };                                                                                                                            
   }                                                                                                                                                                    
 ```                                                                                                                                                                    
                                                                                                                                                                        
 #### Parte B: Bloque <style> existente (Opción 6: tipografía optimizada para libro)                                                                                    
                                                                                                                                                                        
 Agregue al final del bloque <style> existente:                                                                                                                         
                                                                                                                                                                        
 ```css                                                                                                                                                                 
   /* TIPTOGRAFÍA OPTIMIZADA PARA MODO LIBRO (OPCIÓN 6) */                                                                                                              
   .lms-content.book-mode {                                                                                                                                             
       font-size: 0.875rem; /* Era ~1.0625rem (17px) */                                                                                                                 
       line-height: 1.5;    /* Era 1.75 */                                                                                                                              
   }                                                                                                                                                                    
   .lms-content.book-mode h2 {                                                                                                                                          
       font-size: 1.2rem;                                                                                                                                               
       margin-top: 1rem;                                                                                                                                                
       margin-bottom: 0.75rem;                                                                                                                                          
   }                                                                                                                                                                    
   .lms-content.book-mode h3 {                                                                                                                                          
       font-size: 1.1rem;                                                                                                                                               
       margin-top: 0.9rem;                                                                                                                                              
       margin-bottom: 0.6rem;                                                                                                                                           
   }                                                                                                                                                                    
   .lms-content.book-mode p {                                                                                                                                           
       margin-bottom: 0.75rem;                                                                                                                                          
   }                                                                                                                                                                    
   .lms-content.book-mode ul,                                                                                                                                           
   .lms-content.book-mode ol {                                                                                                                                          
       margin-bottom: 0.75rem;                                                                                                                                          
       padding-left: 1.25rem;                                                                                                                                           
   }                                                                                                                                                                    
   .lms-content.book-mode blockquote {                                                                                                                                  
       margin: 0.75rem 0;                                                                                                                                               
       padding-left: 1rem;                                                                                                                                              
       border-left-width: 3px;                                                                                                                                          
   }                                                                                                                                                                    
                                                                                                                                                                        
   /* Activar/desactivar la clase book-mode dinámicamente */                                                                                                            
   // Esta lógica debe añadirse en el componente lessonBook (ver abajo)                                                                                                 
 ```                                                                                                                                                                    
                                                                                                                                                                        
 Además, modifique el componente lessonBook para activar/desactivar la clase book-mode:                                                                                 
 Dentro del mismo bloque <script>@endonce, en la definición del componente Alpine lessonBook, actualice los métodos:                                                    
                                                                                                                                                                        
 ```javascript                                                                                                                                                          
   init() {                                                                                                                                                             
       this.completed = this.$root.dataset.completed === '1';                                                                                                           
       this.total = this.$root.querySelectorAll('#lms-flipbook-root .stf__item').length;                                                                                
       Alpine.store('lmsView').flipbook = this;                                                                                                                         
       Livewire.on('activity-completed', () => { this.completed = true; });                                                                                             
       this.unwatchMode = Alpine.watch(() => Alpine.store('lmsView').mode, (mode) => {                                                                                  
           if (mode === 'book') {                                                                                                                                       
               this.attachKeyboardListener();                                                                                                                           
               const root = document.getElementById('lms-flipbook-root');                                                                                               
               if (root) root.classList.add('book-mode');                                                                                                               
           } else {                                                                                                                                                     
               this.detachKeyboardListener();                                                                                                                           
               const root = document.getElementById('lms-flipbook-root');                                                                                               
               if (root) root.classList.remove('book-mode');                                                                                                            
           }                                                                                                                                                            
       });                                                                                                                                                              
   },                                                                                                                                                                   
   destroy() {                                                                                                                                                          
       if (this.unwatchMode) this.unwatchMode();                                                                                                                        
       this.detachKeyboardListener();                                                                                                                                   
       if (this.resizeListener) {                                                                                                                                       
           window.removeEventListener('resize', this.resizeListener);                                                                                                   
           this.resizeListener = null;                                                                                                                                  
       }                                                                                                                                                                
       const root = document.getElementById('lms-flipbook-root');                                                                                                       
       if (root) root.classList.remove('book-mode');                                                                                                                    
   },                                                                                                                                                                   
 ```                                                                                                                                                                    
                                                                                                                                                                        
 Impacto estimado:                                                                                                                                                      
 - Opción 2: Aumenta aproximadamente un 8-12% el área utilizable para el contenido                                                                                      
 - Opción 6: Reduce aproximadamente un 12-18% el espacio vertical en bloques de texto continuo                                                                          
                                                                                                                                                                        
 ### 3. resources/views/livewire/student/lms/_content-renderer.blade.php                                                                                                
                                                                                                                                                                        
 (Opción 3: estilos condensados específicos para modo libro)                                                                                                            
                                                                                                                                                                        
 Cambios a realizar:                                                                                                                                                    
                                                                                                                                                                        
 1. Agregar detección de modo libro: Después de la línea @props([ ... ]), añada:                                                                                        
    ```blade                                                                                                                                                            
      @php                                                                                                                                                              
          $bookModeClass = $mode === 'book' ? 'book-compact' : '';                                                                                                      
      @endphp                                                                                                                                                           
    ```                                                                                                                                                                 
                                                                                                                                                                        
 2. Aplicar la clase condicionalmente: Envuélvase el contenedor principal de cada tipo de contenido con esta clase. Ejemplo para el caso TEXT:                          
    ```blade                                                                                                                                                            
      @case('TEXT')                                                                                                                                                     
          <!-- ... código existente de detección de Mermaid y plantillas ... -->                                                                                        
                                                                                                                                                                        
          <div class="{{ $bookModeClass }} ... ">                                                                                                                       
              @if($tpl === 'mermaid')                                                                                                                                   
                  <!-- ... contenido existente ... -->                                                                                                                  
              @elseif($tpl === 'concept')                                                                                                                               
                  <!-- ... contenido existente ... -->                                                                                                                  
              <!-- ... y así sucesivamente para cada subcaso ... -->                                                                                                    
          </div>                                                                                                                                                        
                                                                                                                                                                        
      @break                                                                                                                                                            
    ```                                                                                                                                                                 
    Repita este patrón para todos los casos: IMAGE, VIDEO, EMBED, FILE_PREVIEW, AUDIO, HTML, y default.                                                                 
                                                                                                                                                                        
 3. Defina los estilos compactos en el bloque <style> de activity-view.blade.php (junto a los de la Option 6):                                                          
    ```css                                                                                                                                                              
      /* ESTILOS CONDENSADOS ESPECÍFICOS PARA MODO LIBRO (OPCIÓN 3) */                                                                                                  
      .book-compact p { margin-bottom: 0.75rem; } /* Era 1rem */                                                                                                        
      .book-compact ul, .book-compact ol { padding-left: 1.25rem; } /* Era 1.5rem */                                                                                    
      .book-compact .prose { font-size: 0.9rem; line-height: 1.5; } /* Ajustar según necesidad */                                                                       
      .book-compact blockquote { border-left-width: 3px; padding-left: 1rem; } /* Era 4px/1.25rem */                                                                    
      .book-compact { margin-bottom: 0.75rem; } /* Refine spacing between elements */                                                                                   
    ```                                                                                                                                                                 
                                                                                                                                                                        
 Impacto estimado: Reduce aproximadamente un 10-15% el espacio vertical en bloques de texto típico sin comprometer la legibilidad.                                      
                                                                                                                                                                        
 📊 RESUMEN DE IMPACTO ESPERADO                                                                                                                                         
                                                                                                                                                                        
 ┌───────────────────────────┬─────────────────────────────┬────────────────────────────────────────┐                                                                   
 │ Optimización              │ Archivo(es) Afectado(s)     │ Reducción Estimada de Espacio Vertical │                                                                   
 ├───────────────────────────┼─────────────────────────────┼────────────────────────────────────────┤                                                                   
 │ 1. Espaciado interno      │ _flipbook-page.blade.php    │ 25-30% (por página)                    │                                                                   
 ├───────────────────────────┼─────────────────────────────┼────────────────────────────────────────┤                                                                   
 │ 2. Dimensiones StPageFlip │ activity-view.blade.php     │ 8-12% (por página)                     │                                                                   
 ├───────────────────────────┼─────────────────────────────┼────────────────────────────────────────┤                                                                   
 │ 3. Estilos condensados    │ _content-renderer.blade.php │ 10-15% (en bloques de texto)           │                                                                   
 ├───────────────────────────┼─────────────────────────────┼────────────────────────────────────────┤                                                                   
 │ 4. Encabezado de sección  │ _flipbook-page.blade.php    │ Incluido en la Opción 1                │                                                                   
 ├───────────────────────────┼─────────────────────────────┼────────────────────────────────────────┤                                                                   
 │ 6. Tipografía libro       │ activity-view.blade.php     │ 12-18% (en texto continuo)             │                                                                   
 ├───────────────────────────┼─────────────────────────────┼────────────────────────────────────────┤                                                                   
 │ TOTAL COMBINADO           │                             │ 35-50% más contenido por página        │                                                                   
 └───────────────────────────┴─────────────────────────────┴────────────────────────────────────────┘                                                                   
                                                                                                                                                                        
 🚀 INSTRUCCIONES DE IMPLEMENTACIÓN                                                                                                                                     
                                                                                                                                                                        
 1. Preparación:                                                                                                                                                        
     - Cree una rama de trabajo en git: git checkout -b feature/libro-optimizado                                                                                        
     - Haga copias de seguridad de los archivos originales                                                                                                              
                                                                                                                                                                        
 2. Implementación:                                                                                                                                                     
     - Modifique cada archivo siguiendo exactamente las especificaciones anteriores                                                                                     
     - Pruebe después de cada cambio para asegurar que no se introduzcan regresiones                                                                                    
                                                                                                                                                                        
 3. Validación:                                                                                                                                                         
     - Verifique el funcionamiento en ambos modos (scroll y libro)                                                                                                      
     - Pruebe la navegación por teclado (flechas, Inicio/Fin, Escape)                                                                                                   
     - Valide en múltiples dispositivos (móvil, tablet, escritorio)                                                                                                     
     - Pruebe con diversos tipos de contenido (texto, listas, videos, Mermaid, etc.)                                                                                    
                                                                                                                                                                        
 4. Integración:                                                                                                                                                        
     - Una vez probado y validado, fusione la rama a la principal                                                                                                       
     - Documente los cambios en el historial de commits de git                                                                                                          
                                                                                                                                                                        
 ⚠️ CONSIDERACIONES IMPORTANTES                                                                                                                                         
                                                                                                                                                                        
 - Accesibilidad: Asegúrese de que las reducciones de tamaño de fuente y espaciado no comprometan la legibilidad para usuarios con déficits visuales                    
 - Consistencia: Mantenga la identidad visual del modo libro alineada con el modo scroll donde sea apropiado                                                            
 - Renderizado de elementos especiales: Verifique que elementos como diagramas Mermaid, videos y audios mantengan su funcionalidad y proporciones correctas             
 - Compatibilidad cruzada: Pruebe en navegadores principales (Chrome, Firefox, Safari) y en el rango de versiones soportado por el proyecto                             
                                                                                                                                                                        
 ✅ PRÓXIMOS PASOS                                                                                                                                                      
                                                                                                                                                                        
 Una vez implementado este plan, considere:                                                                                                                             
 1. Recopilar feedback de usuarios reales sobre la experiencia de lectura en modo libro                                                                                 
 2. Medir objetivamente la reducción en el número de páginas necesarias para el mismo contenido                                                                         
 3. Evaluar si se requieren ajustes adicionales basados en el uso real                                                                                                  
 4. Documentar las lecciones aprendidas para futuras optimizaciones de interfaz    