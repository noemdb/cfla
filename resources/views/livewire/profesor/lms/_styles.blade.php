@assets
    <style>
        /* ── Published state: disable all interactive controls ── */
        button[disabled],
        button:disabled,
        input:disabled,
        textarea:disabled,
        select:disabled {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
        /* Restore pointer for the banner so it can be read */
        .student-preview-modal:fullscreen {
            background: #f1f5f9;
            overflow-y: auto;
        }
        .student-preview-modal:fullscreen > .bg-black\/80 {
            opacity: 0;
            pointer-events: none;
        }
        /* ── Slide Preview: Enhanced Styling (shared: preview tab + student modal) ── */
        .slide-preview-wrapper,
        .slide-block {
            font-size: 0.9375rem;
            line-height: 1.75;
            color: #1e293b;
        }
        /* Card: applies to both .slide-block standalone and .slide-preview-wrapper > .slide-block */
        .slide-block {
            margin-bottom: 1.25rem;
            padding: 1.25rem 1.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.625rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04);
            transition: box-shadow 0.2s ease;
        }
        .slide-block:last-child {
            margin-bottom: 0;
        }
        .slide-block:hover {
            box-shadow: 0 2px 8px 0 rgb(0 0 0 / 0.06);
        }
        .slide-block-even {
            background: #ffffff;
        }
        .slide-block-odd {
            background: #fafbfc;
        }
        /* Content styling within any slide container */
        .slide-preview-wrapper h2,
        .slide-block h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.75rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            letter-spacing: -0.01em;
        }
        .slide-preview-wrapper h3,
        .slide-block h3 {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1e293b;
            margin: 1rem 0 0.5rem 0;
        }
        .slide-preview-wrapper h4,
        .slide-block h4 {
            font-size: 0.975rem;
            font-weight: 600;
            color: #334155;
            margin: 0.75rem 0 0.4rem 0;
        }
        .slide-preview-wrapper p,
        .slide-block p {
            margin: 0 0 0.75rem 0;
            color: #334155;
        }
        .slide-preview-wrapper p:last-child,
        .slide-block p:last-child {
            margin-bottom: 0;
        }
        .slide-preview-wrapper ul,
        .slide-preview-wrapper ol,
        .slide-block ul,
        .slide-block ol {
            margin: 0.5rem 0 0.75rem 0;
            padding-left: 1.5rem;
        }
        .slide-preview-wrapper li,
        .slide-block li {
            margin-bottom: 0.3rem;
            color: #334155;
        }
        .slide-preview-wrapper li::marker,
        .slide-block li::marker {
            color: #14b8a6;
        }
        .slide-preview-wrapper table,
        .slide-block table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0.75rem 0;
            font-size: 0.875rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .slide-preview-wrapper thead,
        .slide-block thead {
            background: #f1f5f9;
        }
        .slide-preview-wrapper th,
        .slide-block th {
            padding: 0.625rem 0.875rem;
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .slide-preview-wrapper td,
        .slide-block td {
            padding: 0.625rem 0.875rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .slide-preview-wrapper tbody tr:last-child td,
        .slide-block tbody tr:last-child td {
            border-bottom: none;
        }
        .slide-preview-wrapper tbody tr:nth-child(even),
        .slide-block tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .slide-preview-wrapper tbody tr:hover,
        .slide-block tbody tr:hover {
            background: #f1f5f9;
        }
        /* Bold / Italic */
        .slide-preview-wrapper strong,
        .slide-block strong {
            font-weight: 600;
            color: #0f172a;
        }
        .slide-preview-wrapper em,
        .slide-block em {
            font-style: italic;
            color: #475569;
        }
        /* Inline code */
        .slide-preview-wrapper code,
        .slide-block code {
            background: #f1f5f9;
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.8125em;
            color: #be185d;
            border: 1px solid #e2e8f0;
        }
        /* Blockquotes */
        .slide-preview-wrapper blockquote,
        .slide-block blockquote {
            border-left: 3px solid #14b8a6;
            padding: 0.5rem 1rem;
            margin: 0.75rem 0;
            background: #f0fdfa;
            border-radius: 0 0.375rem 0.375rem 0;
            color: #475569;
        }
        /* Horizontal rule */
        .slide-preview-wrapper hr,
        .slide-block hr {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 1rem 0;
        }

        /* Mermaid zoom & fullscreen */
        .mermaid-zoom-toolbar {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        [x-data="mermaidEmbed()"]:fullscreen {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: auto;
        }
        [x-data="mermaidEmbed()"]:fullscreen .mermaid-zoom-toolbar {
            opacity: 1 !important;
            position: fixed;
            top: 1rem;
            right: 1rem;
        }
        .zoom-act {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        /* Mermaid: text wrapping en nodos con texto largo */
        .mermaid .nodeLabel p,
        .mermaid svg foreignObject div p,
        .mermaid svg foreignObject span.nodeLabel {
            white-space: normal !important;
            text-wrap: wrap !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100%;
        }
        .mermaid svg foreignObject {
            overflow: visible !important;
            height: auto !important;
        }
        /* ── Review Questions: cards for each Q&A pair ── */
        .review-questions {
            counter-reset: rq-counter;
        }
        .review-questions h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #d97706;
            margin: 1.5rem 0 0.75rem 0;
            padding: 0.5rem 0.75rem;
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .review-questions h3:first-child {
            margin-top: 0;
        }
        .review-questions ol,
        .review-questions ul {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem 0;
        }
        .review-questions ol > li,
        .review-questions ul > li {
            counter-increment: rq-counter;
            position: relative;
            padding: 1rem 1rem 1rem 3rem;
            margin-bottom: 0.75rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.625rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.04);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
            color: #334155;
            font-size: 0.9375rem;
            line-height: 1.7;
        }
        .review-questions ol > li:hover,
        .review-questions ul > li:hover {
            border-color: #fcd34d;
            box-shadow: 0 2px 8px rgb(251 191 36 / 0.1);
        }
        .review-questions ol > li::before,
        .review-questions ul > li::before {
            content: counter(rq-counter);
            position: absolute;
            left: 0.625rem;
            top: 0.875rem;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f59e0b;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 999px;
            line-height: 1;
        }
        .review-questions ul > li::before {
            content: '';
            width: 0.625rem;
            height: 0.625rem;
            background: #f59e0b;
            top: 1.15rem;
            left: 1.125rem;
        }
        .review-questions ol > li strong,
        .review-questions ul > li strong {
            display: block;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.25rem;
            font-size: 0.975rem;
        }
        .review-questions ol > li em,
        .review-questions ul > li em {
            color: #64748b;
            font-style: italic;
        }
        .review-questions ol > li > p,
        .review-questions ul > li > p {
            margin: 0;
        }
        .review-questions ol > li > p:first-of-type,
        .review-questions ul > li > p:first-of-type {
            display: inline;
        }
        .review-questions ol > li > *:last-child,
        .review-questions ul > li > *:last-child {
            margin-bottom: 0;
        }
    </style>
@endassets
