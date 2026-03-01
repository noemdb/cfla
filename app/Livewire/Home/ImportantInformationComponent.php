<?php
namespace App\Livewire\Home;

use Illuminate\Support\Facades\File;
use Livewire\Component;

class ImportantInformationComponent extends Component
{
    public $images = [];

    public function mount()
    {
        $directoryPath = 'image/important/001';
        $fullPath      = public_path($directoryPath);

        if (File::isDirectory($fullPath)) {
            $files = File::files($fullPath);
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $this->images[] = asset($directoryPath . '/' . $file->getFilename());
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.home.important-information-component');
    }
}
