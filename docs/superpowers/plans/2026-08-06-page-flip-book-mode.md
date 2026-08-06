# Page Flip Book Mode Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement an opt-in "modo libro" (flipbook) view for ActivityView using StPageFlip, with one page per visible section, placeholder for interactive content, and completion tracking.

**Architecture:** 
- Alpine store `lmsView` for reactive mode switching (scroll/book) replacing window globals
- Shared `_content-renderer.blade.php` partial for content rendering in both modes
- Dedicated `_flipbook-page.blade.php` partial for book pages with overflow handling
- Lazy-initialized StPageFlip instance built on first book mode entry
- Completion state synchronization via `markComplete()` dispatch and Alpine store

**Tech Stack:**
- Alpine.js 3.x (already in project)
- StPageFlip (page-flip npm package - to be installed)
- Laravel Livewire 3, Blade components
- Vite 5, manualChunks for code splitting
- PHP 8.2+, Laravel 10

## Global Constraints

- Use PHP 8.2 for this project (prod version per memory)
- Preserve security invariant: never modify `resources/views/components/lms/math-text.blade.php`
- Maintain `.lms-content` surface as white (#fff) even in dark mode
- Do not touch `tests/Feature/Planning/FlowDiagramTest.php` (preexisting unrelated failures)
- Keep scroll mode as default; book mode is opt-in via toggle
- Placeholder enlazado approach for interactive content (Mermaid) in book mode
- One page per visible section, ordered by sort_order

---
### Task 1: Add page-flip dependency and configure asset loading

**Files:**
- Modify: `package.json`
- Modify: `vite.config.js`
- Create: `resources/js/loaders.js` (add loadPageFlip function)

**Interfaces:**
- Consumes: None
- Produces: `loadPageFlip()` function for dynamic import

- [ ] **Step 1: Add page-flip dependency to package.json**

```json
{
  "dependencies": {
    "page-flip": "^1.0.6"
  }
}
```

- [ ] **Step 2: Install the new dependency**

Run: `npm install`

Expected: Installs page-flip and updates package-lock.json

- [ ] **Step 3: Configure Vite manualChunks for page-flip**

In `vite.config.js`, add to the manualChunks function:

```js
if (id.includes('node_modules/page-flip') || id.includes('node_modules/st-page-flip')) {
    return 'page-flip';
}
```

- [ ] **Step 4: Add loadPageFlip function to loaders.js**

```javascript
export function loadPageFlip() {
    if (!window._pageFlipPromise) {
        window._pageFlipPromise = import('page-flip')
            .catch((err) => {
                window._pageFlipPromise = undefined;
                throw err;
            });
    }
    return window._pageFlipPromise;
}
```

- [ ] **Step 5: Commit**

```bash
git add package.json package-lock.json vite.config.js resources/js/loaders.js
git commit -m "feat: add page-flip dependency and loader"
```

### Task 2: Create shared content renderer partial

**Files:**
- Create: `resources/views/livewire/student/lms/_content-renderer.blade.php`

**Interfaces:**
- Consumes: LmsActivityContent object, mode ('scroll'|'book'), stepNum, isLast, sectionId
- Produces: Rendered content appropriate for mode

- [ ] **Step 1: Create _content-renderer.blade.php with @props**

```blade
@props([
    'content' => null,    // LmsActivityContent
    'mode' => 'scroll',   // 'scroll' | 'book'
    'stepNum' => 1,
    'isLast' => false,
    'sectionId' => null,  // For placeholder linking in book mode
])
```

- [ ] **Step 2: Copy @switch block from activity-view.blade.php**

Copy the entire `@foreach($section->visibleContents ... @switch($content->type))` block (lines 341-626 from activity-view.blade.php) and paste it inside the partial.

- [ ] **Step 3: Adapt for mode parameter**

Within the `@switch`:
- For `TEXT` cases that detect Mermaid (class contains 'mermaid' OR regex match):
  - When `mode === 'scroll'`: Keep current `mermaidEmbed()` rendering
  - When `mode === 'book'`: Replace with placeholder:
    ```blade
    <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-4 text-center">
        <p class="text-sm text-amber-800">���📊 Este diagrama se ve mejor en modo deslizar.</p>
        <button type="button" @click="openSection({{ $sectionId }})"
                class="mt-2 text-sm font-semibold text-amber-700 underline hover:text-amber-900">
            Ir a la sección
        </button>
    </div>
    ```
- All other cases (IMAGE, VIDEO, AUDIO, EMBED, FILE_PREVIEW, HTML, non-Mermaid TEXT): Render identically for both modes

- [ ] **Step 4: Adapt wrappers based on mode**

Change the outer wrapper logic:
- When `mode === 'scroll'`: Use current card styling with step number circle
- When `mode === 'book'`: Use simpler styling appropriate for book page (no card, just content with margin/padding, step number as small header)

- [ ] **Step 5: Handle wire:key appropriately**

- When `mode === 'scroll'`: Keep `wire:key="content-{{ $content->id }}" 
- When `mode === 'book'`: Remove wire:key (no diffing needed in book mode)

- [ ] **Step 6: Commit**

```bash
git add resources/views/livewire/student/lms/_content-renderer.blade.php
git commit -m "feat: create shared content renderer partial"
```

### Task 3: Create book page partial

**Files:**
- Create: `resources/views/livewire/student/lms/_flipbook-page.blade.php`

**Interfaces:**
- Consumes: Activity section object (with title, visibleContents, etc.)
- Produces: Complete book page section ready for StPageFlip

- [ ] **Step 1: Create _flipbook-page.blade.php**

```blade
<div class="stf__item p-6">
    <!-- Section header with accent -->
    @php
        $title = $section->title ?? '';
        $isInicio = preg_match('/^INICIO/i', $title);
        $isDesarrollo = preg_match('/^DESARROLLO/i', $title);
        $isCierre = preg_match('/^CIERRE/i', $title);
        
        $accentColor = $isInicio ? 'blue' : ($isDesarrollo ? 'emerald' : ($isCierre ? 'amber' : 'gray'));
        $accentDot = match($accentColor) {
            'blue' => 'bg-blue-500',
            'emerald' => 'bg-emerald-500',
            'amber' => 'bg-amber-500',
            default => 'bg-gray-500',
        };
        $accentRing = match($accentColor) {
            'blue' => 'ring-blue-500/20',
            'emerald' => 'ring-emerald-500/20',
            'amber' => 'ring-amber-500/20',
            default => 'ring-gray-500/20',
        };
        $badgeLabel = match($accentColor) {
            'blue' => 'Inicio',
            'emerald' => 'Desarrollo',
            'amber' => 'Cierre',
            default => '',
        };
        $badgeClass = match($accentColor) {
            'blue' => 'bg-blue-100 text-blue-800',
            'emerald' => 'bg-emerald-100 text-emerald-800',
            'amber' => 'bg-amber-100 text-amber-800',
            default => 'bg-gray-100 text-gray-800',
        };
    @endphp
    
    <div class="flex items-center space-x-3 mb-4">
        <div class="w-2 h-2 {{ $accentDot }} {{ $accentRing }} rounded-full"></div>
        @if($badgeLabel)
            <span class="text-xs font-semibold {{ $badgeClass }} px-2 py-0.5 rounded">{{ $badgeLabel }}</span>
        @endif
        <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
    </div>
    
    <!-- Section content -->
    <div class="space-y-6">
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
    
    <!-- Sticky page indicator at bottom of page -->
    <div class="mt-6 pt-4 border-t border-gray-200 text-sm text-gray-500">
        Página {{ $loop->parent->iteration }} de {{ $loop->parent->count }}
    </div>
</div>
```

- [ ] **Step 2: Add necessary Alpine/x-show attributes for conditional rendering**

The sticky page indicator will be enhanced in the lessonBook component to show current page/total.

- [ ] **Step 3: Commit**

```bash
git add resources/views/livewire/student/lms/_flipbook-page.blade.php
git commit -m "feat: create book page partial"
```

### Task 4: Modify ActivityView Livewire component

**Files:**
- Modify: `app/Livewire/Student/Lms/ActivityView.php`

**Interfaces:**
- Consumes: Activity object via mount()
- Produces: New $flipEnabled property, updated markComplete() method

- [ ] **Step 1: Add $flipEnabled property**

```php
/** ¿Se ofrece el toggle de modo libro? (≥2 secciones, publicada, no modo lectura). */
public bool $flipEnabled = false;
```

- [ ] **Step 2: Calculate $flipEnabled in mount()**

After setting `$this->sections` (around line 80), add:

```php
$this->flipEnabled = $this->sections->count() >= 2
    && ! $this->isPreview
    && ! $this->modoLectura;
```

- [ ] **Step 3: Modify markComplete() to dispatch event**

Add at the end of markComplete() method (after line 180):

```php
$this->dispatch('activity-completed');
```

- [ ] **Step 4: Commit**

```bash
git add app/Livewire/Student/Lms/ActivityView.php
git commit -m "feat: add flipEnabled flag and completion dispatch"
```

### Task 5: Update ActivityView blade structure

**Files:**
- Modify: `resources/views/livewire/student/lms/activity-view.blade.php`

**Interfaces:**
- Consumes: $sections, $resources, $links, $htmlEmbeds, $comments, $completed, $isPreview, $modoLectura, $flipEnabled
- Produces: Updated view with mode toggle, dual containers, book mode structure

- [ ] **Step 1: Add Alpine store for lmsView in @once script**

Find the existing `@once <script>` block (around line 1168) and modify it to add the lmsView store:

```javascript
document.addEventListener('alpine:init', function () {
    if (Alpine._readingNavRegistered) return;
    Alpine._readingNavRegistered = true;
    Alpine.data('readingNav', () => ({
        // ... existing readingNav code ...
    }));
    
    // Add lmsView store
    Alpine.store('lmsView', {
        mode: 'scroll',
        set(v) { this.mode = v; },
    });
});
```

- [ ] **Step 2: Add toggle to sticky nav bar**

In the sticky nav area (around where the progress bar is), add the segmented control when $flipEnabled:

```blade
@if($flipEnabled)
<div class="flex items-center gap-1 rounded-full border border-gray-200 bg-white p-1"
     role="group" aria-label="Modo de lectura">
    <button type="button"
            :class="Alpine.store('lmsView').mode === 'scroll' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors"
            :aria-pressed="Alpine.store('lmsView').mode === 'scroll'"
            @click="Alpine.store('lmsView').set('scroll')">
        Deslizar
    </button>
    <button type="button"
            :class="Alpine.store('lmsView').mode === 'book' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors"
            :aria-pressed="Alpine.store('lmsView').mode === 'book'"
            @click="Alpine.store('lmsView').set('book')">
        Libro
    </button>
</div>
@endif
```

- [ ] **Step 3: Replace section rendering with dual x-show containers**

Find the main section rendering loop (after TOC, before resources/links) and replace:

```blade
<!-- OLD: @foreach($sections as $section) ... @endforeach -->
<!-- NEW: -->
<div x-show="Alpine.store('lmsView').mode === 'scroll'">
    <!-- Current scroll mode content: TOC + sections -->
    @if($sections->count() > 1)
        <!-- TOC remains -->
    @endif
    
    @foreach($sections as $section)
        <section id="seccion-{{ $section->id }}" wire:key="section-{{ $section->id }}"
                 class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <!-- Section header (existing logic) -->
            @php
                // ... existing $accentColor, $accentDot, etc. calculation ...
            @endphp
            
            <header class="...">
                <!-- Existing header content -->
            </header>
            
            <!-- Section contents using partial -->
            @foreach($section->visibleContents as $idx => $content)
                @include('livewire.student.lms._content-renderer', [
                    'content' => $content,
                    'mode' => 'scroll',
                    'stepNum' => $idx + 1,
                    'isLast' => $loop->last,
                    'sectionId' => $section->id,
                ])
            @endforeach
            
            <!-- Section-linked html embeds/resources/links (existing) -->
        </section>
    @endforeach
</div>

<div x-show="Alpine.store('lmsView').mode === 'book'" x-cloak>
    <div wire:ignore>
        <div x-data="lessonBook()" x-init="init()" data-completed="{{ $completed ? '1' : '0' }}">
            <div id="lms-flipbook-root">
                @foreach($sections as $section)
                    @include('livewire.student.lms._flipbook-page', ['section' => $section])
                @endforeach
            </div>
        </div>
    </div>
    <!-- Barra final: fuera del wire:ignore -->
    <div class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4"
         x-show="Alpine.store('lmsView').mode === 'book'">
        <p class="text-sm text-gray-600">
            Página <span x-text="pageIndex + 1"></span> de <span x-text="total"></span>
        </p>
        <button type="button"
                x-show="!completed"
                wire:click="markComplete"
                class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
            Marcar como completada
        </button>
        <span x-show="completed" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600">
            � ✓ Completada
        </span>
    </div>
</div>
```

- [ ] **Step 4: Add lessonBook Alpine component to @once script**

Still within the same `@once <script>` block, add the lessonBook component:

```javascript
if (Alpine._lessonBookRegistered) return;
Alpine._lessonBookRegistered = true;

Alpine.data('lessonBook', () => ({
    pageFlip: null,
    pageIndex: 0,
    total: 0,
    completed: false,
    init() {
        this.completed = this.$root.dataset.completed === '1';
        Livewire.on('activity-completed', () => { this.completed = true; });
    },
    ensureFlipbook() {
        if (this.pageFlip) return;
        
        // Wait for DOM to be visible
        this.$nextTick(() => {
            const root = document.getElementById('lms-flipbook-root');
            if (!root) return;
            
            loadPageFlip().then(module => {
                // Count pages (children with class stf__item)
                this.total = root.querySelectorAll('.stf__item').length;
                
                // Initialize StPageFlip
                this.pageFlip = new module.default(root, {
                    width: '100%',
                    height: '100%',
                    // Responsive sizing will be handled separately
                    showCover: false,
                    // ... other options ...
                });
                
                // Handle prefers-reduced-motion
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    this.pageFlip.flippingTime(0);
                }
                
                // Handle responsive/usePortrait
                this.updatePageSize();
                
                // Set initial page
                this.pageFlip.turn(this.pageIndex);
            }).catch(err => {
                console.error('Failed to load page-flip:', err);
                // Show error in UI - handled separately
            });
        });
    },
    openSection(id) {
        Alpine.store('lmsView').set('scroll');
        this.$nextTick(() => {
            const sectionEl = document.getElementById('seccion-' + id);
            if (sectionEl) {
                sectionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    },
    setPage(index) {
        this.pageIndex = index;
        if (this.pageFlip) {
            this.pageFlip.turn(index);
        }
    },
    updatePageSize() {
        // Calculate page size based on viewport
        const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
        const vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
        
        // Use portrait on narrow screens (< md), landscape on wider
        const isPortrait = vw < 768; // md breakpoint
        const ratio = isPortrait ? 1.5 : 1.2; // height/width ratio
        
        let width, height;
        if (isPortrait) {
            height = Math.min(vh * 0.8, vw * ratio);
            width = height / ratio;
        } else {
            width = Math.min(vw * 0.8, vh / ratio);
            height = width * ratio;
        }
        
        if (this.pageFlip) {
            this.pageFlip.size(width, height);
        }
    }
}));

// Initialize page size on resize
window.addEventListener('resize', () => {
    if (Alpine.store('lmsView').mode === 'book' && window.lessonBookInstance) {
        window.lessonBookInstance.updatePageSize();
    }
});

// Expose lessonBook instance for resize handler (simplified approach)
document.addEventListener('alpine:init', () => {
    Alpine.$data('lessonBook', (instance) => {
        window.lessonBookInstance = instance;
    });
});
```

- [ ] **Step 5: Update section rendering loop to use new structure**

Replace the current section rendering loop with the dual-container approach shown above.

- [ ] **Step 6: Commit**

```bash
git add resources/views/livewire/student/lms/activity-view.blade.php
git commit -m "feat: update activity-view blade for book mode toggle and containers"
```

### Task 6: Implement lessonBook component methods and error handling

**Files:**
- Modify: `resources/views/livewire/student/lms/activity-view.blade.php` (lessonBook methods)

**Interfaces:**
- Consumes: None
- Produces: Complete lessonBook functionality with error handling

- [ ] **Step 1: Complete ensureFlipbook with responsive sizing**

Complete the `ensureFlipbook()` method to:
1. Check if pageFlip exists (idempotency)
2. Wait for DOM visibility with $nextTick
3. Load page-flip via loadPageFlip()
4. Count .stf__item elements for total pages
5. Initialize StPageFlip with options
6. Apply prefers-reduced-motion settings
7. Set up responsive sizing with updatePageSize()
8. Turn to initial page

- [ ] **Step 2: Add error handling for page-flip load failure**

In the catch block of ensureFlipbook():
1. Set an error state that can be displayed
2. Disable the toggle button
3. Show user-friendly message in the book mode container

- [ ] **Step 3: Add keyboard accessibility**

Add keyboard event listener in lessonBook:
- Register listener when mode switches to 'book'
- Unregister when switching away
- Handle ArrowLeft/Right, Home/End, Escape keys

- [ ] **Step 4: Enhance page indicator with current page**

Make sure the "Página X / N" display updates correctly when pages turn
- Listen to StPageFlip events if available, or polling approach
- Update pageIndex accordingly

- [ ] **Step 5: Commit**

```bash
git add resources/views/livewire/student/lms/activity-view.blade.php
git commit -m "feat: complete lessonBook implementation with error handling and a11y"
```

### Task 7: Create book mode test file

**Files:**
- Create: `tests/Feature/Lms/ActivityViewBookModeTest.php`

**Interfaces:**
- Consumes: None
- Produces: Test coverage for book mode functionality

- [ ] **Step 1: Create test file with basic structure**

```php
<?php

namespace Tests\Feature\Lms;

use Tests\TestCase;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Lms\LmsActivityContent;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityViewBookModeTest extends TestCase
{
    use RefreshDatabase;
    
    // Test methods will go here
}
```

- [ ] **Step 2: Implement gate tests**

Test that toggle doesn't appear when:
- Less than 2 sections
- In preview mode ($isPreview = true)
- In modo lectura ($modoLectura = true)

- [ ] **Step 3: Implement book mode presence test**

Test that toggle appears when:
- 2+ sections visible
- Not in preview
- Not in modo lectura

- [ ] **Step 4: Implement book page count test**

When in book mode:
- Verify correct number of pages equals number of visible sections
- Verify each section title appears in the book

- [ ] **Step 5: Implement wire:ignore test**

Verify that the flipbook root has `wire:ignore` attribute (not wire:ignore.self)

- [ ] **Step 6: Implement Mermaid placeholder test**

In book mode:
- Verify Mermaid content shows placeholder instead of diagram
- Verify placeholder has link to open section
- Verify clicking placeholder switches to scroll and scrolls to section

- [ ] **Step 7: Implement completion test**

- Complete activity in scroll mode
- Switch to book mode
- Verify completion state is reflected (CTA changes to "��✓ Completada")

- [ ] **Step 8: Implement completion dispatch test**

- Call markComplete()
- Assert that 'activity-completed' event was dispatched

- [ ] **Step 9: Commit**

```bash
git add tests/Feature/Lms/ActivityViewBookModeTest.php
git commit -m "feat: add book mode test suite"
```

### Task 8: Run tests and verify implementation

**Files:**
- Modify: None (if all previous tasks correct)
- Test: Run PHPUnit tests

**Interfaces:**
- Consumes: All implemented functionality
- Produces: Passing test suite

- [ ] **Step 1: Run new book mode tests**

Run: `php8.2 artisan test --filter=ActivityViewBookModeTest`

Expected: All tests pass

- [ ] **Step 2: Run regression tests**

Run: `php8.2 artisan test --filter="StudentHomeTest|StudentAccessTest"`

Expected: All existing tests still pass (validating content renderer equivalence)

- [ ] **Step 3: Run full test suite to catch any regressions**

Run: `php8.2 artisan test`

Expected: All tests pass (or same failures as baseline excluding unrelated ones)

- [ ] **Step 4: Commit**

```bash
git commit -m "test: verify book mode implementation and regression"
```

### Task 9: Final verification and cleanup

**Files:**
- Modify: Any files needing minor fixes based on test results
- Test: Manual verification in browser

**Interfaces:**
- Consumes: Implementation
- Produces: Working feature ready for review

- [ ] **Step 1: Manual verification in development server**

Run: `php artisan serve` and `npm run dev` in separate tabs

Verify:
- Toggle appears correctly when eligible
- Switching modes works smoothly
- Book mode shows one page per section
- Placeholders work for Mermaid
- Completion works in both modes
- Responsive behavior (mobile vs desktop)
- Accessibility (keyboard navigation)
- Error handling (simulate network failure)

- [ ] **Step 2: Fix any issues found**

Address any bugs or usability issues discovered in manual testing.

- [ ] **Step 3: Final commit**

```bash
git add .
git commit -m "feat: complete page-flip book mode implementation"
```

## Summary

This implementation plan covers:
1. Dependency setup and asset loading (page-flip)
2. Shared content rendering partial to avoid duplication
3. Book page partial for individual sections
4. Livewire component modifications for mode tracking and completion events
5. Blade template changes for dual-mode UI with toggle
6. Alpine.js lessonBook component with lazy initialization
7. Comprehensive test coverage
8. Verification and quality assurance

Each task is bite-sized, follows TDD principles, and produces independently verifiable results. The plan maintains all existing functionality while adding the new book mode feature as an opt-in alternative view.