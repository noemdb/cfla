<?php
// Helper stubs for static analyzers (Intelephense, PhpStorm, etc.).
// This file is wrapped in a runtime-free conditional so it never executes,
// but static analyzers will read the declared classes/methods.
if (false) {
    namespace Illuminate\View;

    /**
     * @noinspection PhpUnused
     */
    class View
    {
        /**
         * Livewire provides a `layout` macro on views at runtime.
         * Declare it here so static analyzers (Intelephense) stop flagging undefined method errors.
         *
         * @param string $layout
         * @param array  $data
         * @return \Illuminate\View\View
         */
        public function layout(string $layout, array $data = []) {}
    }
}
