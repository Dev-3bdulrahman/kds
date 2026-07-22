<?php

namespace Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Dev3bdulrahman\Kds\Models\KdsOrder;
use Dev3bdulrahman\Kds\Models\KdsDisplay;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'display')]
    public string $displayFilter = '';

    public ?int $orderId = null;
    public ?int $display_id = null;
    public string $status = 'pending';
    public string $priority = 'normal';
    public string $notes = '';

    public bool $showFormModal = false;

    protected $listeners = ['delete' => 'deleteOrder'];

    #[Layout('layouts.admin')]
    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDisplayFilter()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->orderId = null;
        $this->display_id = null;
        $this->status = 'pending';
        $this->priority = 'normal';
        $this->notes = '';
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $order = KdsOrder::findOrFail($id);

        $this->orderId = $order->id;
        $this->display_id = $order->display_id;
        $this->status = $order->status;
        $this->priority = $order->priority;
        $this->notes = $order->notes ?? '';

        $this->showFormModal = true;
    }

    public function save()
    {
        $rules = [
            'display_id' => 'nullable|exists:kds_displays,id',
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
            'priority' => 'required|in:normal,urgent',
            'notes' => 'nullable|string',
        ];

        $this->validate($rules);

        $data = [
            'display_id' => $this->display_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'notes' => $this->notes ?: null,
        ];

        if ($this->orderId) {
            $order = KdsOrder::findOrFail($this->orderId);
            $order->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('kds::kds.success_updated')]);
        } else {
            $data['company_id'] = auth()->user()->company_id;
            $data['order_number'] = 'KDS-' . strtoupper(uniqid());
            KdsOrder::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('kds::kds.success_created')]);
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function deleteOrder($id)
    {
        $targetId = is_array($id) ? ($id['id'] ?? null) : $id;
        if ($targetId) {
            KdsOrder::findOrFail($targetId)->delete();
            $this->dispatch('notify', ['type' => 'success', 'message' => __('kds::kds.success_deleted')]);
        }
    }

    public function render()
    {
        $query = KdsOrder::with(['items', 'display']);

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('table_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->displayFilter)) {
            $query->where('display_id', $this->displayFilter);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        $displays = KdsDisplay::all();

        return view('kds::livewire.admin.orders.index', [
            'orders' => $orders,
            'displays' => $displays,
        ])->title(__('kds::kds.orders'));
    }
}
