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
    $display = preg_replace('/\\\\html(?:Data|Class|Style)\s*\{[^}]*\}\s*\{[^}]*\}/', '', (string) $content);
    $wireKey = 'dm-' . ($uid !== '' ? $uid . '-' : 'x-') . md5((string) $content);
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
