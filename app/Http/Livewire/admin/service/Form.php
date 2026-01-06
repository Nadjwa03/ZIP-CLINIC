<?php

namespace App\Http\Livewire\Admin\Service;

use App\Models\Service;
use App\Models\Speciality;
use Livewire\Component;

class Form extends Component
{
    public $serviceId;
    public $code;
    public $service_name;
    public $speciality_id;
    public $description;
    public $price;
    public $duration_minutes;
    public $is_active = true;

    public $specialities = [];
    public $isEdit = false;

    protected function rules()
    {
        return [
            'code' => 'nullable|string|max:50|unique:services,code,' . ($this->serviceId ?? 'NULL') . ',service_id',
            'service_name' => 'required|string|max:200',
            'speciality_id' => 'required|exists:specialities,speciality_id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'service_name.required' => 'Nama layanan wajib diisi',
        'speciality_id.required' => 'Spesialisasi wajib dipilih',
        'price.required' => 'Harga wajib diisi',
        'price.numeric' => 'Harga harus berupa angka',
        'duration_minutes.required' => 'Durasi wajib diisi',
        'duration_minutes.integer' => 'Durasi harus berupa angka',
    ];

    public function mount($serviceId = null)
    {
        $this->serviceId = $serviceId;
        $this->isEdit = !is_null($serviceId);

        // Load specialities
        $this->specialities = Speciality::where('is_active', true)
            ->orderBy('speciality_name')
            ->get();

        // If edit mode, load service data
        if ($this->isEdit) {
            $service = Service::findOrFail($serviceId);
            $this->code = $service->code;
            $this->service_name = $service->service_name;
            $this->speciality_id = $service->speciality_id;
            $this->description = $service->description;
            $this->price = $service->price;
            $this->duration_minutes = $service->duration_minutes;
            $this->is_active = $service->is_active;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                // Update existing service
                $service = Service::findOrFail($this->serviceId);
                $service->update([
                    'code' => $this->code,
                    'service_name' => $this->service_name,
                    'speciality_id' => $this->speciality_id,
                    'description' => $this->description,
                    'price' => $this->price,
                    'duration_minutes' => $this->duration_minutes,
                    'is_active' => $this->is_active,
                ]);

                session()->flash('success', 'Layanan berhasil diupdate!');
            } else {
                // Create new service
                Service::create([
                    'code' => $this->code,
                    'service_name' => $this->service_name,
                    'speciality_id' => $this->speciality_id,
                    'description' => $this->description,
                    'price' => $this->price,
                    'duration_minutes' => $this->duration_minutes,
                    'is_active' => $this->is_active,
                ]);

                session()->flash('success', 'Layanan berhasil ditambahkan!');
            }

            return redirect()->route('admin.services.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.service.form');
    }
}
