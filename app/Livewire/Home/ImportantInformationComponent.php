<?php
namespace App\Livewire\Home;

use Illuminate\Support\Facades\File;
use Livewire\Component;

class ImportantInformationComponent extends Component
{
    public $images = [];
    public $imagePost;

    public function mount()
    {
        $directoryPath = 'image/resaltado/001';
        $fullPath      = public_path($directoryPath);
        $foundImages   = [];

        try {
            if (File::isDirectory($fullPath)) {
                // Method 1: standard File::files()
                $files = File::files($fullPath);
                foreach ($files as $file) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $foundImages[] = asset($directoryPath . '/' . $file->getFilename());
                    }
                }
            }

            // Method 2: Fallback glob if Method 1 found nothing but directory exists
            if (empty($foundImages) && File::isDirectory($fullPath)) {
                $globPattern = $fullPath . '/*.{jpg,jpeg,png,webp,gif,JPG,JPEG,PNG,WEBP,GIF}';
                $globFiles   = glob($globPattern, GLOB_BRACE);
                if ($globFiles) {
                    foreach ($globFiles as $file) {
                        $foundImages[] = asset($directoryPath . '/' . basename($file));
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ImportantInformationComponent: Error scanning directory: " . $e->getMessage());
        }

        $this->images = array_unique($foundImages);

        if (empty($this->images)) {
            \Illuminate\Support\Facades\Log::warning("ImportantInformationComponent: No images found at " . $fullPath);
        } else {
            \Illuminate\Support\Facades\Log::info("ImportantInformationComponent: Loaded " . count($this->images) . " images from " . $fullPath);
        }
    }

    public function render()
    {
        return view('livewire.home.important-information-component');
    }
}
