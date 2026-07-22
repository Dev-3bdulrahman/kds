<?php

namespace Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Displays;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Dev3bdulrahman\Kds\Models\KdsDisplay;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $typeFilter = '';

    public ?int $displayId = null;
    public string $name = '';
    public string $name_ar = '';
    public string $display_type = 'kitchen';
    public string $location = '';
    public string $status = 'online';
    public array $display_categories = [];

    public bool $showFormModal = false;

    protected $listeners = ['delete' => 'deleteDisplay'];

    #[Layout('layouts.admin')]
    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->displayId = null;
        $this->name = '';
        $this->name_ar = '';
        $this->display_type = 'kitchen';
        $this->location = '';
        $this->status = 'online';
        $this->display_categories = [];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $display = KdsDisplay::findOrFail($id);

        $this->displayId = $display->id;
        $this->name = $display->name;
        $this->name_ar = $display->name_ar ?? '';
        $this->display_type = $display->display_type;
        $this->location = $display->location ?? '';
        $this->status = $display->status;
        $this->display_categories = $display->display_categories ?? [];

        $this->showFormModal = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'display_type' => 'required|in:kitchen,workshop,bar',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:online,offline,maintenance',
            'display_categories' => 'nullable|array',
        ];

        $this->validate($rules);

        $data = [
            'company_id' => auth()->user()->company_id,
            'name' => $this->name,
            'name_ar' => $this->name_ar ?: null,
            'display_type' => $this->display_type,
            'location' => $this->location ?: null,
            'status' => $this->status,
            'display_categories' => !empty($this->display_categories) ? $this->display_categories : null,
        ];

        if ($this->displayId) {
            $display = KdsDisplay::findOrFail($this->displayId);
            $display->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('kds::kds.success_updated')]);
        } else {
            KdsDisplay::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('kds::kds.success_created')]);
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function deleteDisplay($id)
    {
        $targetId = is_array($id) ? ($id['id'] ?? null) : $id;
        if ($targetId) {
            KdsDisplay::findOrFail($targetId)->delete();
            $this->dispatch('notify', ['type' => 'success', 'message' => __('kds::kds.success_deleted')]);
        }
    }

    public function render()
    {
        $query = KdsDisplay::query();

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (!empty($this->typeFilter)) {
            $query->where('display_type', $this->typeFilter);
        }

        $displays = $query->paginate(10);

        return view('kds::livewire.admin.displays.index', [
            'displays' => $displays,
        ])->title(__('kds::kds.displays'));
    }
}
