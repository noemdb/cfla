<div class="lms-content text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none prose-table:border-collapse prose-table:border prose-table:border-slate-300 prose-th:bg-slate-100 prose-th:px-3 prose-th:py-2 prose-th:border prose-th:border-slate-300 prose-td:px-3 prose-td:py-1.5 prose-td:border prose-td:border-slate-300">
    @if($rendered)
        {!! $rendered !!}
    @else
        <p class="text-sm text-slate-400 italic">Sin contenido disponible.</p>
    @endif
</div>

{{-- Estilos consistentes para contenido pedagógico renderizado desde Markdown --}}
@once
    <style>
        /* ── Headings (títulos) ── */
        .lms-content :is(h1, h2, h3, h4) {
            color: #0f172a !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
            letter-spacing: -0.01em !important;
        }
        .lms-content h1 {
            font-size: 1.5em !important;
            margin: 1.2em 0 0.6em !important;
            padding-bottom: 0.3em !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .lms-content h2 {
            font-size: 1.25em !important;
            margin: 1.4em 0 0.5em !important;
            padding-bottom: 0.25em !important;
            border-bottom: 1.5px solid #e2e8f0 !important;
            color: #0d9488 !important;
        }
        .lms-content h3 {
            font-size: 1.05em !important;
            margin: 1.2em 0 0.4em !important;
            color: #1e293b !important;
        }
        .lms-content h4 {
            font-size: 0.95em !important;
            margin: 1em 0 0.3em !important;
            color: #334155 !important;
            font-weight: 600 !important;
        }

        /* ── Subtitles / lead ── */
        .lms-content :is([class~="lead"], blockquote) {
            font-size: 1em !important;
            line-height: 1.6 !important;
            color: #475569 !important;
            font-style: normal !important;
            font-weight: 500 !important;
        }
        .lms-content blockquote {
            border-left: 3px solid #0d9488 !important;
            background: #f0fdfa !important;
            padding: 0.75em 1em !important;
            margin: 1.2em 0 !important;
            border-radius: 0 0.5rem 0.5rem 0 !important;
        }
        .lms-content blockquote p {
            margin: 0 !important;
        }

        /* ── Tables ── */
        .lms-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 1.2em 0 !important;
            font-size: 0.9em !important;
            line-height: 1.5 !important;
        }
        .lms-content table thead {
            border-bottom: 2px solid #0d9488 !important;
        }
        .lms-content table th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 0.9em !important;
            text-transform: uppercase !important;
            letter-spacing: 0.025em !important;
            padding: 0.6rem 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            text-align: left !important;
            vertical-align: top !important;
        }
        .lms-content table td {
            padding: 0.5rem 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            vertical-align: top !important;
            color: #334155 !important;
        }
        .lms-content table tbody tr {
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .lms-content table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
        }
        .lms-content table tbody tr:hover {
            background-color: #f1f5f9 !important;
        }
        .lms-content table :is(th, td) strong {
            color: inherit !important;
        }

        /* ── Paragraph spacing ── */
        .lms-content p {
            margin: 0.6em 0 !important;
            line-height: 1.65 !important;
        }
        .lms-content p:first-child {
            margin-top: 0 !important;
        }
        .lms-content ul, .lms-content ol {
            margin: 0.5em 0 !important;
            padding-left: 1.2em !important;
        }
        .lms-content li {
            margin: 0.2em 0 !important;
        }

        /* ── Strong / emphasis ── */
        .lms-content strong {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .lms-content em {
            color: #475569 !important;
        }
    </style>
@endonce
