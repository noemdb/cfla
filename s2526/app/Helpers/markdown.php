<?php

use App\Services\BootstrapMarkdownService;

if (!function_exists('markdown_to_bootstrap')) {
    /**
     * Convert markdown to HTML with Bootstrap classes
     */
    function markdown_to_bootstrap($markdown, $bootstrapVersion = null, $options = [])
    {
        $service = app(BootstrapMarkdownService::class);
        
        if ($bootstrapVersion) {
            $service->setBootstrapVersion($bootstrapVersion);
        }
        
        if (!empty($options)) {
            $service->setOptions($options);
        }
        
        return $service->process($markdown);
    }
}