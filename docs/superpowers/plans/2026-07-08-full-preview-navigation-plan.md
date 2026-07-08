# Full-Preview Navigation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add sidebar TOC navigation, section collapse/expand, progress indicator, and mobile TOC to the `showFullPreview` modal in the LessonWizard.

**Architecture:** All changes are in the Blade view only (`lesson-wizard.blade.php`). The existing `@if($showFullPreview)` block (lines 3240-3423) is restructured into a 2-column flex layout. Navigation state is managed entirely via Alpine.js — no Livewire state changes. The existing `previewSections` computed property and `renderContentBody()` method are reused as-is.

**Tech Stack:** Laravel Blade + Alpine.js (inline `x-data`, `x-show`, `x-transition`, IntersectionObserver) + Tailwind CSS

## Global Constraints

- No changes to `LessonWizard.php` or any PHP file
- No new Livewire properties, events, or methods
- No new model queries or database calls
- All interactivity via Alpine.js only (no new JS files)
- Must work with existing Mermaid zoom controls (`x-data="mermaidEmbed()"`)
- Must not break close/open behavior (`$set('showFullPreview', false)`)
- Must not break the student preview modal or any other feature

---

### Task 1: Restructure modal layout to 2 columns

**Files:**
- Modify: `resources/views/livewire/profesor/lms/lesson-wizard.blade.php:3240-3423` (entire `@if($showFullPreview)` block)

**Interfaces:**
- Consumes: `$this->previewSections` (existing computed property), `$lessonTitle`, `$lessonDescription`, `$selectedActivity`, `$wizardResources`, `$wizardLinks`, `$wizardHtmlEmbeds`
- Produces: 2-column layout with `x-data="tocNavigation()"` on the body wrapper, `data-section-index` attributes on each section

- [ ] **Step 1: Read the current modal section**

```bash
sed -n '3240,3423p' resources/views/livewire/profesor/lms/lesson-wizard.blade.php
```

- [ ] **Step 2: Replace the modal body with 2-column flex layout**

Replace the entire body `<div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">` (line 3270) and everything inside it down to the closing `</div>` at line 3419.

New structure:

```blade
{{-- Body: 2 columnas --}}
<div class="flex flex-1 overflow-hidden min-h-0" x-data="tocNavigation()">
    {{-- ═══ SIDEBAR TOC ═══ --}}
    <aside class="hidden lg:block w-56 shrink-0 border-r border-slate-700/50 bg-slate-800/80 overflow-y-auto p-3 sticky top-0 self-start max-h-[calc(100vh-12rem)]">
        <div class="flex items-center gap-1.5 mb-3 px-1">
            <span class="text-xs text-slate-400">📑</span>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Índice</span>
            <span class="ml-auto text-[10px] font-mono text-slate-600">{{ count($this->previewSections) }} sec.</span>
        </div>
        <div class="space-y-0.5">
            @foreach($this->previewSections as $sIdx => $section)
                @php
                    $hasContent = !empty(array_filter($section['contents'] ?? [], fn($c) => !empty($c['body'])));
                @endphp
                <button @click="scrollTo({{ $sIdx }})"
                        :class="activeSection === {{ $sIdx }} ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700/40 border-transparent'"
                        class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left transition-all text-xs border">
                    <span class="flex items-center justify-center w-5 h-5 rounded {{ $hasContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-700/60 text-slate-500' }} text-[10px] font-mono shrink-0">
                        {{ str_pad($sIdx + 1, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="truncate flex-1">{{ $section['title'] }}</span>
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $hasContent ? 'bg-emerald-400/60' : 'bg-slate-600/40' }}"></span>
                </button>
            @endforeach
        </div>
    </aside>

    {{-- ═══ CONTENIDO ═══ --}}
    <div class="flex-1 overflow-y-auto p-6 space-y-5" x-ref="contentArea">
        {{-- Header preview (existing, simplified) --}}
        ... (existing header preview content)

        {{-- Secciones --}}
        @forelse($this->previewSections as $sIdx => $section)
            <div data-section-index="{{ $sIdx }}" x-data="{ expanded: true }" class="scroll-mt-20">
                ... (section with expand/collapse)
            </div>
        @empty
            ... (existing empty state)
        @endforelse

        {{-- Unlinked resources --}}
        ... (existing unlinked resources section)
    </div>
</div>
```

Key changes:
- Outer div becomes `flex flex-1 overflow-hidden min-h-0` with `x-data="tocNavigation()"`
- Sidebar: `hidden lg:block w-56 ... sticky`
- Content area: `flex-1 overflow-y-auto p-6 space-y-5` with `x-ref="contentArea"`
- Each section gets `data-section-index="{{ $sIdx }}"` and `x-data="{ expanded: true }"`

- [ ] **Step 3: Add progress badge to the header**

Find the header `<div class="flex items-center gap-3">` (line 3251). After the `</div>` closing, before the close button, add:

```blade
<span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-700 text-slate-300 border border-slate-600/50 ml-auto">
    {{ count($this->previewSections) }} secciones
</span>
```

- [ ] **Step 4: Add collapse/expand to each section**

Replace each section's rendering to include a collapse toggle. The section heading changes from:
```blade
<div class="flex items-center gap-2">
    <span class="w-1 h-5 bg-emerald-500 rounded-full"></span>
    <h3 class="text-base font-bold text-slate-100">{{ $section['title'] }}</h3>
</div>
```

To:
```blade
<div class="flex items-center gap-2">
    <button @click="expanded = !expanded"
            class="p-1 -ml-1 rounded-lg hover:bg-slate-700/50 text-slate-500 hover:text-slate-300 transition-all"
            :class="expanded ? 'rotate-90' : ''">
        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    <span class="w-1 h-5 bg-emerald-500 rounded-full shrink-0"></span>
    <h3 class="text-base font-bold text-slate-100">{{ $section['title'] }}</h3>
    <span class="text-[10px] text-slate-600 font-mono ml-auto">
        {{ count($section['contents'] ?? []) }} bloques
    </span>
</div>
```

Then wrap all the section content (contents loop + resources) in:
```blade
<div x-show="expanded" x-transition:enter.duration.200 x-collapse>
    ... existing contents, resources, links, embeds ...
</div>
```

Note: `x-collapse` requires the collapse plugin. Check if it's available. If not, use `x-show` with `x-transition:enter.duration.200` only.

- [ ] **Step 5: Commit the layout restructure**

```bash
git add resources/views/livewire/profesor/lms/lesson-wizard.blade.php
git commit -m "feat: add sidebar TOC, collapse/expand, and progress badge to full-preview modal"
```

---

### Task 2: Add Alpine.js TOC navigation (IntersectionObserver)

**Files:**
- Modify: `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` — add Alpine data component

**Interfaces:**
- Consumes: `data-section-index` attributes on section elements
- Produces: `activeSection` reactive property, `scrollTo(index)` method

- [ ] **Step 1: Add `tocNavigation()` Alpine component**

This is added within the `@script` section at the bottom of the view or inline in the modal. Since the existing Mermaid embed component is in the `@script` section, add `tocNavigation` alongside it or right before the modal's `x-data` usage.

Add to the `@script` section (find it — should be near the bottom of the file around line ~3900+):

```js
Alpine.data('tocNavigation', () => ({
    activeSection: 0,
    observer: null,

    init() {
        this.$nextTick(() => {
            const sections = this.$el.querySelectorAll('[data-section-index]');
            if (!sections.length) return;

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.activeSection = parseInt(entry.target.dataset.sectionIndex);
                    }
                });
            }, { rootMargin: '-80px 0px -60% 0px' });

            sections.forEach(el => this.observer.observe(el));
        });
    },

    scrollTo(index) {
        const el = this.$el.querySelector(`[data-section-index="${index}"]`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            this.activeSection = index;
        }
    },

    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}));
```

- [ ] **Step 2: Verify IntersectionObserver availability**

Alpine.js is available globally, and Alpine.data() extension point is standard. IntersectionObserver is broadly supported (all modern browsers, Safari 12.1+). No polyfill needed.

- [ ] **Step 3: Commit**

```bash
git add resources/views/livewire/profesor/lms/lesson-wizard.blade.php
git commit -m "feat: add IntersectionObserver-based TOC navigation with active section tracking"
```

---

### Task 3: Add mobile TOC floating button and overlay

**Files:**
- Modify: `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` — add mobile toggle before closing `</div>` of modal

**Interfaces:**
- Consumes: Same `tocNavigation().scrollTo()` method, same `$this->previewSections`
- Produces: Floating button + overlay TOC only visible on `< lg` screens

- [ ] **Step 1: Add floating TOC button**

Add this right before the closing `</div>` of the main modal wrapper (before `@endif` on line 3423):

```blade
{{-- Botón TOC flotante (mobile) --}}
<div x-data="{ mobileTocOpen: false }"
     x-init="$watch('mobileTocOpen', val => document.body.classList.toggle('overflow-hidden', val))">
    {{-- Floating button --}}
    <button @click="mobileTocOpen = true"
            class="lg:hidden fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white shadow-xl shadow-emerald-900/30 flex items-center justify-center transition-all active:scale-90">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Overlay TOC --}}
    <div x-show="mobileTocOpen" x-cloak
         x-transition:enter.duration.200
         class="lg:hidden fixed inset-0 z-[9999]">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="mobileTocOpen = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-slate-800 border-t border-slate-700 rounded-t-2xl p-4 max-h-[50vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">📑 Índice</span>
                <button @click="mobileTocOpen = false" class="p-1.5 text-slate-500 hover:text-white rounded-lg hover:bg-slate-700/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-0.5">
                @foreach($this->previewSections as $sIdx => $section)
                    @php
                        $hasContent = !empty(array_filter($section['contents'] ?? [], fn($c) => !empty($c['body'])));
                    @endphp
                    <button @click="mobileTocOpen = false; $el.closest('[x-data=\\'tocNavigation()\\']')?.__x.$data.scrollTo({{ $sIdx }})"
                            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-left transition-all text-sm hover:bg-slate-700/50">
                        <span class="flex items-center justify-center w-6 h-6 rounded {{ $hasContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-700/60 text-slate-500' }} text-xs font-mono shrink-0">
                            {{ $sIdx + 1 }}
                        </span>
                        <span class="text-slate-300 font-medium truncate">{{ $section['title'] }}</span>
                        <span class="w-1.5 h-1.5 rounded-full shrink-0 ml-auto {{ $hasContent ? 'bg-emerald-400/60' : 'bg-slate-600/40' }}"></span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/livewire/profesor/lms/lesson-wizard.blade.php
git commit -m "feat: add mobile floating TOC button with bottom-sheet overlay"
```

---

### Task 4: Final verification

**Files:**
- Review: `resources/views/livewire/profesor/lms/lesson-wizard.blade.php`

- [ ] **Step 1: Review the changed region**

Read the modified modal section and verify:
1. Modal header has progress badge
2. Layout is 2-column with sidebar TOC (hidden on mobile)
3. Each section has `data-section-index`, collapse toggle, and `x-data="{ expanded: true }"`
4. Content is wrapped in `x-show="expanded"`
5. `tocNavigation()` Alpine component is registered
6. Mobile TOC button + overlay exists and uses correct Alpine references
7. Close button and `wire:click="$set('showFullPreview', false)"` still works
8. Mermaid diagrams still render with the existing `x-data="mermaidEmbed()"`
9. No PHP syntax errors (mismatched `@php`/`@endphp`, `@if`/`@endif`, etc.)

- [ ] **Step 2: Verify helper reference accuracy**

- `renderContentBody()` — existing PHP method, used via `{!! $this->renderContentBody($rawBody) !!}`
- `previewSections` — existing computed property `getPreviewSectionsProperty()`, accessed as `$this->previewSections`
- `$wizardResources`, `$wizardLinks`, `$wizardHtmlEmbeds` — existing Livewire public properties
- Mermaid zoom (`x-data="mermaidEmbed()"`) — existing, already defined in the `@script` section

- [ ] **Step 3: Squash and final commit message**

```bash
git log --oneline -5
```

The commits can be squashed into one coherent commit:
```bash
# If not already squashed:
git rebase -i HEAD~4
# Mark 2nd, 3rd, 4th as `squash`
# Final message:
# feat: add sidebar TOC navigation, section collapse/expand, and mobile support to full-preview modal
```

Or leave as individual commits if preferred.

- [ ] **Step 4: Verify no regressions**

Run any existing tests:
```bash
php artisan test --filter=LessonWizard
```

If no specific tests exist for the view, a visual check in the browser is sufficient.
