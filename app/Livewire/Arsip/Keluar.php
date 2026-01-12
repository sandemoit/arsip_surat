<?php

namespace App\Livewire\Arsip;

use Livewire\Component;

class Keluar extends Component
{
    public function render()
    {
        return view('livewire.arsip.keluar')
            ->layout('components.layouts.app.sidebar');
    }
}
