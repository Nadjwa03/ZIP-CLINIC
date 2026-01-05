<?php

namespace App\Http\Livewire\Admin\Speciality;

use App\Models\Speciality;
use Livewire\Component;

class Form extends Component
{
    public $specialityId;
    public $speciality_name = '';
    public $description = '';
    public $is_active = true;

    protected $rules = [
        'speciality_name' => 'required|string|max:100',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'speciality_name.required' => 'Nama spesialisasi harus diisi',
        'speciality_name.max' => 'Nama spesialisasi maksimal 100 karakter',
        'description.max' => 'Deskripsi maksimal 500 karakter',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->specialityId = $id;
            $speciality = Speciality::findOrFail($id);
            
            $this->speciality_name = $speciality->speciality_name;
            $this->description = $speciality->description;
            $this->is_active = $speciality->is_active;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->specialityId) {
            // Update
            $speciality = Speciality::findOrFail($this->specialityId);
            $speciality->update([
                'speciality_name' => $this->speciality_name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Spesialisasi berhasil diperbarui!');
        } else {
            // Create
            Speciality::create([
                'speciality_name' => $this->speciality_name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Spesialisasi berhasil ditambahkan!');
        }

        return redirect()->route('admin.speciality.index');
    }

    public function render()
    {
        $title = $this->specialityId ? 'Edit Spesialisasi' : 'Tambah Spesialisasi';
        
        return view('livewire.admin.speciality.form', [
            'title' => $title
        ]);
    }
}
