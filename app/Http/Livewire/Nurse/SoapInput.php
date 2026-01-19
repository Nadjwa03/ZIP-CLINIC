<?php

namespace App\Http\Livewire\Nurse;

use App\Models\Visit;
use App\Models\VisitDetail;
use App\Models\Service;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SoapInput extends Component
{
    public $visitId;
    public $visit;

    // SOAP Fields
    public $subjective = '';
    public $objective = '';
    public $assessment = '';
    public $plan = '';
    public $notes = '';
    public $follow_up_at = null;

    // Visit Details (Tindakan)
    public $details = [];
    public $showAddDetail = false;

    // New detail form
    public $newDetail = [
        'service_id' => '',
        'tooth_number' => '',
        'description' => '',
        'quantity' => 1,
        'unit_price' => 0,
    ];

    protected $rules = [
        'subjective' => 'nullable|string',
        'objective' => 'nullable|string',
        'assessment' => 'nullable|string',
        'plan' => 'nullable|string',
        'notes' => 'nullable|string',
        'follow_up_at' => 'nullable|date',
    ];

    public function mount($visitId)
    {
        $this->visitId = $visitId;
        $this->loadVisit();
    }

    public function loadVisit()
    {
        $this->visit = Visit::with(['patient', 'doctor', 'queue', 'details.service'])
            ->findOrFail($this->visitId);

        // Load existing SOAP data
        $this->subjective = $this->visit->subjective ?? '';
        $this->objective = $this->visit->objective ?? '';
        $this->assessment = $this->visit->assessment ?? '';
        $this->plan = $this->visit->plan ?? '';
        $this->notes = $this->visit->notes ?? '';
        $this->follow_up_at = $this->visit->follow_up_at?->format('Y-m-d');

        // Load existing details
        $this->details = $this->visit->details->toArray();
    }

    public function render()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('livewire.nurse.soap-input', [
            'services' => $services,
        ]);
    }

    /**
     * Save SOAP data
     */
    public function saveSoap()
    {
        $this->validate();

        $this->visit->update([
            'subjective' => $this->subjective,
            'objective' => $this->objective,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
            'notes' => $this->notes,
            'follow_up_at' => $this->follow_up_at,
            'entered_by_user_id' => Auth::id(),
            'entry_method' => Auth::user()->isNurse() ? 'NURSE_ASSIST' : 'DIRECT',
        ]);

        session()->flash('soap-message', 'SOAP berhasil disimpan!');
    }

    /**
     * Toggle add detail form
     */
    public function toggleAddDetail()
    {
        $this->showAddDetail = !$this->showAddDetail;
        $this->resetNewDetail();
    }

    /**
     * Reset new detail form
     */
    public function resetNewDetail()
    {
        $this->newDetail = [
            'service_id' => '',
            'tooth_number' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    /**
     * Update price when service selected
     */
    public function updatedNewDetailServiceId($value)
    {
        if ($value) {
            $service = Service::find($value);
            if ($service) {
                $this->newDetail['unit_price'] = $service->price;
                $this->newDetail['description'] = $service->name;
            }
        }
    }

    /**
     * Add new detail/tindakan
     */
    public function addDetail()
    {
        $this->validate([
            'newDetail.service_id' => 'required|exists:services,service_id',
            'newDetail.quantity' => 'required|integer|min:1',
            'newDetail.unit_price' => 'required|numeric|min:0',
        ], [
            'newDetail.service_id.required' => 'Pilih layanan/tindakan.',
        ]);

        $service = Service::find($this->newDetail['service_id']);

        VisitDetail::create([
            'visit_id' => $this->visitId,
            'service_id' => $this->newDetail['service_id'],
            'tooth_number' => $this->newDetail['tooth_number'] ?: null,
            'description' => $this->newDetail['description'] ?: $service->name,
            'quantity' => $this->newDetail['quantity'],
            'unit_price' => $this->newDetail['unit_price'],
            'subtotal' => $this->newDetail['quantity'] * $this->newDetail['unit_price'],
        ]);

        // Reload details
        $this->visit->refresh();
        $this->details = $this->visit->details->toArray();

        $this->showAddDetail = false;
        $this->resetNewDetail();

        session()->flash('soap-message', 'Tindakan berhasil ditambahkan!');
    }

    /**
     * Remove detail
     */
    public function removeDetail($detailId)
    {
        VisitDetail::where('visit_detail_id', $detailId)->delete();

        // Reload details
        $this->visit->refresh();
        $this->details = $this->visit->details->toArray();

        session()->flash('soap-message', 'Tindakan berhasil dihapus!');
    }

    /**
     * Close panel and refresh parent
     */
    public function closePanel()
    {
        $this->dispatch('closeSoapPanel');
        $this->dispatch('refreshTreatmentRoom');
    }
}