<?php

namespace App\Livewire\Admin;

use App\Models\CompanyAsset;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AssetManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $assetId = null;

    public $name = '';
    public $serial_number = '';
    public $type = 'electronics';
    public $user_id = '';
    public $date_assigned = '';
    public $return_date = '';
    public $status = 'available';
    public $notes = '';

    public $purchase_date = '';
    public $purchase_cost = '';
    public $expiration_date = '';

    public $showHistoryModal = false;
    public $assetHistories = null;

    public $search = '';
    public $typeFilter = '';

    protected $queryString = ['search', 'typeFilter'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'serial_number' => 'nullable|string|max:255',
        'type' => 'required|in:electronics,vehicle,furniture,uniform',
        'purchase_date' => 'nullable|date',
        'purchase_cost' => 'nullable|numeric|min:0',
        'expiration_date' => 'nullable|date',
        'user_id' => 'nullable|ulid|exists:users,id',
        'date_assigned' => 'nullable|date',
        'return_date' => 'nullable|date|after_or_equal:date_assigned',
        'status' => 'required|in:available,assigned,maintenance,lost,retired,sold,auctioned,disposed',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        //
    }

    public function create()
    {
        $this->reset(['assetId', 'name', 'serial_number', 'type', 'purchase_date', 'purchase_cost', 'expiration_date', 'user_id', 'date_assigned', 'return_date', 'notes']);
        $this->type = 'electronics';
        $this->status = 'available';
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $asset = CompanyAsset::findOrFail($id);
        $this->assetId = $asset->id;
        $this->name = $asset->name;
        $this->serial_number = $asset->serial_number;
        $this->type = $asset->type;
        $this->purchase_date = $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : null;
        $this->purchase_cost = $asset->purchase_cost;
        $this->expiration_date = $asset->expiration_date ? \Carbon\Carbon::parse($asset->expiration_date)->format('Y-m-d') : null;
        $this->user_id = $asset->user_id;
        $this->date_assigned = $asset->date_assigned ? \Carbon\Carbon::parse($asset->date_assigned)->format('Y-m-d') : null;
        $this->return_date = $asset->return_date ? \Carbon\Carbon::parse($asset->return_date)->format('Y-m-d') : null;
        $this->status = $asset->status;
        $this->notes = $asset->notes;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // Auto-manage status consistency based on assignment
        if ($this->user_id && $this->status === 'available') {
            $this->status = 'assigned';
        } elseif (!$this->user_id && $this->status === 'assigned') {
            $this->status = 'available';
        }

        $data = [
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'type' => $this->type,
            'purchase_date' => $this->purchase_date ?: null,
            'purchase_cost' => $this->purchase_cost ?: null,
            'expiration_date' => $this->expiration_date ?: null,
            'user_id' => $this->user_id ?: null,
            'date_assigned' => $this->user_id && $this->date_assigned ? $this->date_assigned : null,
            'return_date' => $this->return_date ?: null,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->editMode) {
            $asset = CompanyAsset::findOrFail($this->assetId);
            $originalUserId = $asset->user_id;
            $originalStatus = $asset->status;

            $asset->update($data);

            // Log Assignment
            if ($data['user_id'] && $originalUserId !== $data['user_id']) {
                \App\Models\CompanyAssetHistory::create([
                    'company_asset_id' => $asset->id,
                    'user_id' => $data['user_id'],
                    'action' => 'assigned',
                    'notes' => __('Assigned by Admin'),
                ]);
            }

            // Log Return by Admin
            if (!$data['user_id'] && $originalUserId) {
                \App\Models\CompanyAssetHistory::create([
                    'company_asset_id' => $asset->id,
                    'user_id' => $originalUserId,
                    'action' => 'returned',
                    'notes' => __('Returned to storage pool by Admin'),
                ]);
            }

            // Log Status Change (maintenance, lost, retired)
            if ($data['status'] !== $originalStatus && in_array($data['status'], ['maintenance', 'lost', 'retired'])) {
                \App\Models\CompanyAssetHistory::create([
                    'company_asset_id' => $asset->id,
                    'user_id' => $data['user_id'] ?: $originalUserId,
                    'action' => $data['status'],
                    'notes' => __('Status changed to :status', ['status' => $data['status']]),
                ]);
            }

            session()->flash('success', __('Asset updated successfully.'));
        } else {
            $asset = CompanyAsset::create($data);

            \App\Models\CompanyAssetHistory::create([
                'company_asset_id' => $asset->id,
                'user_id' => null,
                'action' => 'created',
                'notes' => __('Asset registered into the system'),
            ]);

            if ($asset->user_id) {
                \App\Models\CompanyAssetHistory::create([
                    'company_asset_id' => $asset->id,
                    'user_id' => $asset->user_id,
                    'action' => 'assigned',
                    'notes' => __('Assigned upon registration'),
                ]);
            }

            session()->flash('success', __('Asset recorded successfully.'));
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        CompanyAsset::destroy($id);
        session()->flash('success', __('Asset removed from inventory.'));
    }

    public function viewHistory($id)
    {
        $asset = CompanyAsset::with('histories.user')->findOrFail($id);
        $this->assetHistories = $asset->histories;
        $this->showHistoryModal = true;
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function render()
    {
        $query = CompanyAsset::with('user');

        $admin = auth()->user();
        if (!$admin->isSuperadmin) {
            $query->where(function ($q) use ($admin) {
                $q->whereNull('user_id')
                  ->orWhereHas('user', function ($uq) use ($admin) {
                      if ($admin->kabupaten_kode) {
                          $uq->where('kabupaten_kode', $admin->kabupaten_kode);
                      } elseif ($admin->provinsi_kode) {
                          $uq->where('provinsi_kode', $admin->provinsi_kode);
                      }
                  });
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $users = User::where('group', 'user')
            ->whereNotNull('nip')->where('nip', '!=', '')
            ->managedBy($admin)->orderBy('name')->get();

        return view('livewire.admin.asset-manager', [
            'assets' => $query->latest()->paginate(10),
            'users' => $users,
        ]);
    }
}
