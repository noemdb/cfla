<?php
namespace App\Livewire\Home;

use Illuminate\Support\Facades\File;
use Livewire\Component;

class ImportantInformationComponent extends Component
{
    public $images = [];

    public function mount()
    {
        $directory = public_path('image/important/001');
        if (File::exists($directory)) {
            $files = File::files($directory);
            foreach ($files as $file) {
                if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $this->images[] = asset('image/important/001/' . $file->getFilename());
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.home.important-information-component');
    }
}
