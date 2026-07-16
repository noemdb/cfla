# Activities-Per-Day Chart for Planning Indicators

**Date:** 2026-07-16  
**Status:** Approved  
**Context:** User requested a visual chart on the planning/indicators page showing activities registered per day.

## Problem

The planning indicators page (`/app/planning/indicators`) shows KPI boxes and detailed tabbed indicator data, but lacks a temporal visualization of activity registration volume.

## Solution

Add a single-row bar chart using ApexCharts showing the count of activities grouped by their start date (`finicial`), positioned between the global KPI boxes and the main tabbed content.

## Architecture

### Backend (Livewire Component)
- **File:** `app/Livewire/Planning/Indicator/IndexComponent.php`
- **New property:** `$chartActivitiesByDay` — array of `{date: string, count: int}` objects
- **Query:** Joins `activities` → `pevaluacions` → `pensums`, grouped by `activities.finicial`, applying all existing filters (lapso, peducativo, pestudio, grado, profesor)
- **Data flow:** Query runs inside `loadAllData()`, results are serialized to JSON and passed to the Blade view

### Frontend (Blade + ApexCharts)
- **File:** `resources/views/livewire/planning/indicator/index-component.blade.php`
- **Import:** ApexCharts ESM module registered globally in `resources/js/app.js`
- **Container:** `<div>` with `wire:ignore` attribute, placed between KPI boxes row and content section
- **Init:** Script in `@script` block initializes ApexCharts on `livewire:init` and re-renders on filter changes via a dispatched event
- **Chart config:** Column/bar chart, emerald palette matching app theme, gradient fill, rounded corners, dark-mode-aware tooltip

### Filters
The chart reuses the existing 4 filters (P.Educativo, P.Estudio, Grado, Profesor) plus the lapso selector. No new filters added.

### Stack
- ApexCharts 5.16.0 installed via npm
- Node 20.x compatible
- Vite build pipeline processes the import

## Files Changed

| File | Change |
|------|--------|
| `app/Livewire/Planning/Indicator/IndexComponent.php` | Add `$chartActivitiesByDay` property and query method |
| `resources/views/livewire/planning/indicator/index-component.blade.php` | Add chart container + initialization script between KPI boxes and tabs |
| `resources/js/app.js` | Import ApexCharts globally |
| `blueprint/apexcharts/README.md` | New reference file for future chart implementations |

## Implementation Plan

1. Update PHP component (`IndexComponent.php`) — add chart data query, property, and integration into `loadAllData()`
2. Register ApexCharts in `app.js`
3. Update Blade view — add chart HTML + initialization script
4. Build frontend (`npm run build`)
5. Write blueprint reference doc
6. Verify page renders correctly
