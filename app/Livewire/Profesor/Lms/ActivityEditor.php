<?php

namespace App\Livewire\Profesor\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Services\Lms\LmsMediaUploadService;
use App\Services\Lms\LmsPublicationService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ActivityEditor extends Component
{
    use WithFileUploads;

    public Activity $activity;
    public $sections = [];

    // Nueva sección
    public string $newSectionTitle = '';

    // Nuevo contenido
    public ?int $editingSectionId = null;
    public string $contentType = 'TEXT';
    public string $contentTitle = '';
    public string $contentBody = '';

    // Upload de recurso
    public $resourceFile;
    public string $resourceName = '';
    public string $resourceDescription = '';

    // URL externa
    public string $linkTitle = '';
    public string $linkUrl = '';
    public string $linkType = 'REFERENCE';

    // Publicación
    public string $pubStatus = 'DRAFT';
    public ?string $publishAt = null;
    public bool $allowDownloads = true;

    public bool $showLinkForm = false;
    public bool $showResourceForm = false;

    protected function rules(): array
    {
        return [
            'newSectionTitle'    => 'required|string|max:255',
            'contentBody'        => 'required_without:resourceFile|nullable|string',
            'resourceFile'       => 'nullable|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,mp4,mp3',
            'resourceName'       => 'required_with:resourceFile|nullable|string|max:255',
            'linkTitle'          => 'required_with:linkUrl|nullable|string|max:255',
            'linkUrl'            => 'required_with:linkTitle|nullable|url|max:1000',
        ];
    }

    public function mount(Activity $activity): void
    {
        abort_unless(
            auth()->user()->is_admin
            || $activity->pevaluacion->profesor_id === auth()->id(),
            403
        );

        $this->activity = $activity;
        $this->loadSections();
        $this->loadPublication();
    }

    private function loadSections(): void
    {
        $this->sections = $this->activity
            ->lmsSections()
            ->with(['contents.media'])
            ->get()
            ->toArray();
    }

    private function loadPublication(): void
    {
        $pub = $this->activity->lmsPublication;
        if ($pub) {
            $this->pubStatus      = $pub->status;
            $this->publishAt      = $pub->publish_at?->format('Y-m-d\TH:i');
            $this->allowDownloads = $pub->allow_downloads;
        }
    }

    public function addSection(): void
    {
        $this->validate(['newSectionTitle' => 'required|string|max:255']);

        LmsActivitySection::create([
            'activity_id' => $this->activity->id,
            'title'       => $this->newSectionTitle,
            'sort_order'  => count($this->sections) + 1,
        ]);

        $this->newSectionTitle = '';
        $this->loadSections();
        $this->dispatch('section-added');
    }

    public function addTextContent(int $sectionId): void
    {
        $this->validate(['contentBody' => 'required|string|min:1']);

        LmsActivityContent::create([
            'section_id' => $sectionId,
            'type'       => 'TEXT',
            'title'      => $this->contentTitle ?: null,
            'body'       => $this->contentBody,
            'sort_order' => LmsActivityContent::where('section_id', $sectionId)->count() + 1,
        ]);

        $this->contentBody       = '';
        $this->contentTitle      = '';
        $this->editingSectionId  = null;
        $this->loadSections();
    }

    public function uploadResource(): void
    {
        $this->validate([
            'resourceFile' => 'required|file|max:51200',
            'resourceName' => 'required|string|max:255',
        ]);

        $media = app(LmsMediaUploadService::class)->upload($this->resourceFile, auth()->id());

        LmsActivityResource::create([
            'activity_id'  => $this->activity->id,
            'media_id'     => $media->id,
            'uploaded_by'  => auth()->id(),
            'display_name' => $this->resourceName,
            'description'  => $this->resourceDescription,
            'sort_order'   => $this->activity->lmsResources()->count() + 1,
        ]);

        LmsActivityLog::record($this->activity->id, auth()->id(), 'RESOURCE_ADD', $media->id, 'lms_media_library');

        $this->reset('resourceFile', 'resourceName', 'resourceDescription');
        $this->showResourceForm = false;
        $this->dispatch('resource-uploaded');
    }

    public function addLink(): void
    {
        $this->validate([
            'linkTitle' => 'required|string|max:255',
            'linkUrl'   => 'required|url|max:1000',
        ]);

        LmsActivityLink::create([
            'activity_id' => $this->activity->id,
            'added_by'    => auth()->id(),
            'title'       => $this->linkTitle,
            'url'         => $this->linkUrl,
            'link_type'   => $this->linkType,
            'sort_order'  => $this->activity->lmsLinks()->count() + 1,
        ]);

        $this->reset('linkTitle', 'linkUrl', 'linkType');
        $this->showLinkForm = false;
        $this->dispatch('link-added');
    }

    public function toggleSectionVisibility(int $sectionId): void
    {
        $section = LmsActivitySection::findOrFail($sectionId);
        abort_unless($section->activity_id === $this->activity->id, 403);
        $section->update(['is_visible' => !$section->is_visible]);
        $this->loadSections();
    }

    public function deleteSection(int $sectionId): void
    {
        $section = LmsActivitySection::findOrFail($sectionId);
        abort_unless($section->activity_id === $this->activity->id, 403);
        $section->delete();
        $this->loadSections();
    }

    public function deleteResource(int $resourceId): void
    {
        $resource = LmsActivityResource::findOrFail($resourceId);
        abort_unless($resource->activity_id === $this->activity->id, 403);
        $resource->delete();
    }

    public function deleteLink(int $linkId): void
    {
        $link = LmsActivityLink::findOrFail($linkId);
        abort_unless($link->activity_id === $this->activity->id, 403);
        $link->delete();
    }

    public function publishActivity(): void
    {
        app(LmsPublicationService::class)->publish(
            $this->activity,
            [
                'publish_at'      => $this->publishAt,
                'allow_downloads' => $this->allowDownloads,
            ],
            auth()->id()
        );
        $this->dispatch('activity-published');
        $this->loadPublication();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profesor.lms.activity-editor')
               ->layout('planning.layouts.app');
    }
}
