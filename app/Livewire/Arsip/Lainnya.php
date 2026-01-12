<?php

namespace App\Livewire\Arsip;

use Livewire\Component;

class Lainnya extends Component
{
    public function render()
    {
        return view('livewire.arsip.lainnya')
            ->layout('components.layouts.app.sidebar');
    }
}
