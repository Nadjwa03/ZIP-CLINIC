<?php

namespace App\Http\Livewire\Admin\Service;

use App\Models\Service;
use App\Models\Speciality;
use Livewire\Component;

class Form extends Component
{
    public $serviceId;
    public $code = '';
    public $service_name = '';
    public $speciality_id = '';
    public $description = '';
    public $price = '';
    public $duration_minutes = '';
    public $is_active = true;

    public $specialities = [];

    protected $rules = [
        'code' => 'nullable|string|max:50|unique:services,code',
        'service_name' => 'required|string|max:200',
        'speciality_id' => 'required|exists:specialities,speciality_id',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'duration_minutes' => 'required|integer|min:1',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'service_name.required' => 'Nama layanan harus diisi',
        'service_name.max' => 'Nama layanan maksimal 200 karakter',
        'speciality_id.required' => 'Spesialisasi harus dipilih',
        'price.required' => 'Harga harus diisi',
        'price.numeric' => 'Harga harus berupa angka',
        'price.min' => 'Harga tidak boleh negatif',
        'duration_minutes.required' => 'Durasi harus diisi',
        'duration_minutes.integer' => 'Durasi harus berupa angka',
        'duration_minutes.min' => 'Durasi minimal 1 menit',
        'code.unique' => 'Kode layanan sudah digunakan',
    ];

    public function mount($id = null)
    {
        // Load specialities untuk dropdown
        $this->specialities = Speciality::where('is_active', true)
            ->orderBy('speciality_name')
            ->get();

        // Load data service kalau edit
        if ($id) {
            $this->serviceId = $id;
            $service = Service::findOrFail($id);
            
            $this->code = $service->code ?? '';
            $this->service_name = $service->service_name;
            $this->speciality_id = $service->speciality_id;
            $this->description = $service->description ?? '';
            $this->price = $service->price;
            $this->duration_minutes = $service->duration_minutes;
            $this->is_active = $service->is_active;
        }
    }

    public function save()
    {
        // Update validation rule untuk edit (ignore current code)
        if ($this->serviceId) {
            $this->rules['code'] = 'nullable|string|max:50|unique:services,code,' . $this->serviceId . ',service_id';
        }
        
        $this->validate();

        $data = [
            'code' => $this->code ?: null,
            'service_name' => $this->service_name,
            'speciality_id' => $this->speciality_id,
            'description' => $this->description ?: null,
            'price' => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'is_active' => $this->is_active,
        ];

        if ($this->serviceId) {
            // Update
            $service = Service::findOrFail($this->serviceId);
            $service->update($data);
            session()->flash('success', 'Layanan berhasil diperbarui!');
        } else {
            // Create
            Service::create($data);
            session()->flash('success', 'Layanan berhasil ditambahkan!');
        }

        return redirect()->route('admin.services.index');
    }

    public function render()
    {
        $title = $this->serviceId ? 'Edit Layanan' : 'Tambah Layanan';
        
        return view('livewire.admin.service.form', [
            'title' => $title
        ]);
    }
}