<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;

class BootstrapMarkdownService
{
    protected $bootstrapVersion;
    protected $options;
    protected $converter;
    protected $useExternalParser;

    public function __construct($bootstrapVersion = null, $options = [])
    {
        $this->bootstrapVersion = $bootstrapVersion ?? config('markdown.bootstrap_version', '4.3');
        $this->options = $options ?? config('markdown.default_options', []);
        $this->useExternalParser = config('markdown.use_external_parser', true);
        
        if ($this->useExternalParser && class_exists(CommonMarkConverter::class)) {
            $this->initializeExternalParser();
        }
    }

    protected function initializeExternalParser()
    {
        $environment = Environment::createCommonMarkEnvironment();
        
        // Agregar extensiones
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new AttributesExtension());
        
        $this->converter = new CommonMarkConverter([], $environment);
    }

    /**
     * Procesa markdown a HTML con Bootstrap
     */
    public function process($markdown, $customOptions = [])
    {
        $options = array_merge($this->options, $customOptions);
        
        // Usar parser externo si está disponible
        if ($this->converter) {
            $html = $this->converter->convertToHtml($markdown);
        } else {
            $html = $this->parseBasicMarkdown($markdown); // Cambiado de convertMarkdown a parseBasicMarkdown
        }
        
        // Aplicar clases de Bootstrap
        $html = $this->applyBootstrapClasses($html);
        
        // Aplicar opciones personalizadas
        if (!empty($options)) {
            $html = $this->applyCustomOptions($html, $options);
        }
        
        return $html;
    }

    /**
     * Parsing básico de markdown (para cuando no hay parser externo)
     */
    protected function parseBasicMarkdown($text)
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Encabezados
        $text = preg_replace_callback('/^#\s+(.+)$/m', function($matches) {
            return '<h1>' . e($matches[1]) . '</h1>';
        }, $text);
        
        $text = preg_replace_callback('/^##\s+(.+)$/m', function($matches) {
            return '<h2>' . e($matches[1]) . '</h2>';
        }, $text);
        
        $text = preg_replace_callback('/^###\s+(.+)$/m', function($matches) {
            return '<h3>' . e($matches[1]) . '</h3>';
        }, $text);
        
        $text = preg_replace_callback('/^####\s+(.+)$/m', function($matches) {
            return '<h4>' . e($matches[1]) . '</h4>';
        }, $text);
        
        $text = preg_replace_callback('/^#####\s+(.+)$/m', function($matches) {
            return '<h5>' . e($matches[1]) . '</h5>';
        }, $text);
        
        $text = preg_replace_callback('/^######\s+(.+)$/m', function($matches) {
            return '<h6>' . e($matches[1]) . '</h6>';
        }, $text);
        
        // Negritas
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/__([^_]+)__/', '<strong>$1</strong>', $text);
        
        // Cursivas
        $text = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/_([^_]+)_/', '<em>$1</em>', $text);
        
        // Listas no ordenadas
        $text = preg_replace('/^[\-\*\+]\s+(.+)$/m', '<li>$1</li>', $text);
        
        // Agrupar listas
        $lines = explode("\n", $text);
        $result = '';
        $inList = false;
        
        foreach ($lines as $line) {
            if (strpos($line, '<li>') === 0) {
                if (!$inList) {
                    $result .= '<ul>';
                    $inList = true;
                }
                $result .= $line;
            } else {
                if ($inList) {
                    $result .= '</ul>';
                    $inList = false;
                }
                $result .= $line . "\n";
            }
        }
        
        if ($inList) {
            $result .= '</ul>';
        }
        
        // Código en línea
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        
        // Enlaces
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text);
        
        // Imágenes
        $text = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1">', $text);
        
        // Separadores horizontales
        $text = preg_replace('/^\s*[\-\*_]{3,}\s*$/m', '<hr>', $text);
        
        // Convertir saltos de línea en <br> para párrafos simples
        $text = nl2br($text);
        
        return $text;
    }

    /**
     * Aplica clases de Bootstrap según la versión
     */
    protected function applyBootstrapClasses($html)
    {
        $classes = $this->getBootstrapClasses();
        
        foreach ($classes as $selector => $class) {
            if ($selector === 'table') {
                $html = preg_replace('/<table(\s[^>]*)?>/', '<table$1 class="' . $class . '">', $html);
            } elseif ($selector === 'img') {
                $html = preg_replace('/<img([^>]*)>/', '<img$1 class="' . $class . '">', $html);
            } elseif ($selector === 'blockquote') {
                $html = preg_replace('/<blockquote(\s[^>]*)?>/', '<blockquote$1 class="' . $class . '">', $html);
            } elseif ($selector === 'code') {
                $html = preg_replace('/<code(\s[^>]*)?>/', '<code$1 class="' . $class . '">', $html);
            } elseif ($selector === 'pre') {
                $html = preg_replace('/<pre(\s[^>]*)?>/', '<pre$1 class="' . $class . '">', $html);
            } elseif ($selector === 'ul') {
                $html = preg_replace('/<ul(\s[^>]*)?>/', '<ul$1 class="' . $class . '">', $html);
            } elseif ($selector === 'ol') {
                $html = preg_replace('/<ol(\s[^>]*)?>/', '<ol$1 class="' . $class . '">', $html);
            } elseif ($selector === 'li') {
                $html = preg_replace('/<li(\s[^>]*)?>/', '<li$1 class="' . $class . '">', $html);
            }
        }
        
        return $html;
    }

    /**
     * Obtiene las clases de Bootstrap según la versión
     */
    protected function getBootstrapClasses()
    {
        if ($this->bootstrapVersion === '5') {
            return [
                'table' => 'table table-striped table-bordered',
                'img' => 'img-fluid rounded',
                'blockquote' => 'blockquote alert alert-info',
                'code' => 'bg-light p-1 border rounded',
                'pre' => 'bg-dark text-white p-3 rounded',
                'ul' => 'list-group',
                'ol' => 'list-group',
                'li' => 'list-group-item',
            ];
        }
        
        // Default Bootstrap 4.3
        return [
            'table' => 'table table-striped table-bordered',
            'img' => 'img-fluid rounded',
            'blockquote' => 'blockquote alert alert-info',
            'code' => 'bg-light p-1 border rounded',
            'pre' => 'bg-dark text-light p-3 rounded',
            'ul' => 'list-group',
            'ol' => 'list-group',
            'li' => 'list-group-item',
        ];
    }

    /**
     * Aplica opciones personalizadas
     */
    protected function applyCustomOptions($html, $options)
    {
        foreach ($options as $tag => $class) {
            if (in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
                $html = preg_replace("/<{$tag}(\s[^>]*)?>/", "<{$tag}$1 class=\"{$class}\">", $html);
            } elseif ($tag === 'p') {
                $html = preg_replace('/<p(\s[^>]*)?>/', "<p$1 class=\"{$class}\">", $html);
            } elseif ($tag === 'a') {
                $html = preg_replace('/<a([^>]*)>/', "<a$1 class=\"{$class}\">", $html);
            }
        }
        
        return $html;
    }

    /**
     * Métodos de conveniencia
     */
    public function toHtml($markdown)
    {
        return $this->process($markdown);
    }
    
    public function parse($markdown)
    {
        return $this->process($markdown);
    }
    
    public function __invoke($markdown)
    {
        return $this->process($markdown);
    }
    
    /**
     * Establece la versión de Bootstrap
     */
    public function setBootstrapVersion($version)
    {
        $this->bootstrapVersion = $version;
        return $this;
    }

    /**
     * Establece opciones adicionales
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
}