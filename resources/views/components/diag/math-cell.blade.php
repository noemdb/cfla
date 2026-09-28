@props([
    'content' => '',
    'as' => 'div',
    // Identificador único entre hermanos: el wire:key incorpora el hash del
    // contenido, así un cambio de texto reemplaza el nodo (nunca queda
    // contenido obsoleto) en vez de parchearlo.
    'uid' => '',
])

@php
    // CVE-2025-1390: neutraliza macros \htmlData \htmlClass \htmlStyle antes de renderizar.
    $raw = (string) $content;
    $display = preg_replace('/\\\\html(?:Data|Class|Style)\s*\{[^}]*\}\s*\{[^}]*\}/', '', $raw);

    // Notación química pegada sin delimitadores: el auto-render de KaTeX sólo
    // procesa texto dentro de \( \), $$ o \[ \]. Si el autor escribió \ce{...} /
    // \pu{...} "a pelo" y NO hay ningún delimitador en la celda, se envuelve cada
    // ocurrencia para que llegue a KaTeX. Si el autor ya puso delimitadores, no se
    // toca nada (respeto su marcado explícito).
    //
    // Bloque vs inline: si la fórmula ocupa su propia línea (caso típico: ecuación
    // química en una línea dedicada) se usa $$...$$ (display); si aparece dentro de
    // una frase se usa \(...\) para no romper el interlineado con un bloque.
    // Hasta 3 niveles de llaves anidadas: \ce{\frac{a}{b}} sigue cerrando bien.
    $tieneDelimitadores = str_contains($display, '$$')
        || str_contains($display, '\(')
        || str_contains($display, '\[');
    if (! $tieneDelimitadores && preg_match('/\\\\(?:ce|pu)\s*\{/', $display)) {
        $re = '/\\\\(?:ce|pu)\s*\{(?:[^{}]|\{(?:[^{}]|\{[^{}]*\})*\})*\}/';
        if (preg_match_all($re, $display, $mm, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            $out = '';
            $cursor = 0;
            foreach ($mm as $set) {
                [$formula, $offset] = $set[0];
                $out .= substr($display, $cursor, $offset - $cursor);
                // ¿La fórmula abre su propia línea? (sólo espacios/tabs antes)
                $prev = substr($display, 0, $offset);
                $enSuLinea = $prev === '' || preg_match('/(^|[\r\n])[ \t]*$/', $prev) === 1;
                $out .= $enSuLinea ? '$$' . $formula . '$$' : '\(' . $formula . '\)';
                $cursor = $offset + strlen($formula);
            }
            $out .= substr($display, $cursor);
            $display = $out;
        }
    }

    $wireKey = 'dm-' . ($uid !== '' ? $uid . '-' : 'x-') . md5($raw);
@endphp

{{--
  Celda de texto con LaTeX (KaTeX) segura para morphs Livewire.
  Patrón: wire:ignore + wire:key estable + x-init de un solo disparo.
  Sin observers ni re-renders: el morph solo mueve/reemplaza el nodo opaco,
  por eso no rompe como los componentes reactivos en tablas con live-search.
  Si KaTeX no carga, el texto plano escapado queda visible (degradación gradual).
--}}
<{{ $as }}
    wire:ignore
    wire:key="{{ $wireKey }}"
    x-data
    x-init="(() => { const hasMath = () => { const t = $el.textContent || ''; return t.indexOf('\\(') !== -1 || t.indexOf('$$') !== -1 || t.indexOf('\\[') !== -1; }; let t = 0; const run = () => { try { if (!$el.isConnected) return; if (!hasMath()) return; if (window.renderMathInElement) { renderMathInElement($el, {delimiters: [{left:'\\(',right:'\\)',display:false},{left:'$$',right:'$$',display:true},{left:'\\[',right:'\\]',display:true}], throwOnError: false}); return; } } catch (e) {} if (t++ < 100 && $el.isConnected) setTimeout(run, 100); }; (window._mathKatexReady || Promise.resolve()).then(() => { try { $nextTick(run); } catch (e) { run(); } }); })()"
    {{ $attributes }}
>{{ $display }}</{{ $as }}>
