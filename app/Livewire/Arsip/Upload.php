<?php

namespace App\Livewire\Arsip;

use Livewire\Component;

class Upload extends Component
{
    public function render()
    {
        return view('livewire.arsip.upload')
            ->layout('components.layouts.app.sidebar');
    }
}
