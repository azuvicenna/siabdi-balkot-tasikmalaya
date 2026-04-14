<?php

namespace App\Livewire\RewardSystem;

use App\Models\Participant as ModelsParticipant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Participant extends Component
{
    use WithPagination;

    #[Layout('components.layouts.reward-system')]
    #[Title('Daftar Peserta Undian HKN Day Ke-61')]

    public $search = '';
    public $selectedUnit = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedUnit()
    {
        $this->resetPage();
    }

    public function render()
    {
        $units = ModelsParticipant::query()
            ->select('unit_kerja')
            ->distinct()
            ->orderBy('unit_kerja', 'asc')
            ->pluck('unit_kerja');

        $participants = ModelsParticipant::query()
            ->with('coupons')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('unit_kerja', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedUnit, function ($query) {
                $query->where('unit_kerja', $this->selectedUnit);
            })
            ->orderBy('status_hadir', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate(10);

        return view('livewire.reward-system.participant', [
            'participants' => $participants,
            'units'        => $units,
            'totalPeserta' => ModelsParticipant::count(),
            'totalHadir'   => ModelsParticipant::where('status_hadir', 'Hadir')->count()
        ]);
    }
}
