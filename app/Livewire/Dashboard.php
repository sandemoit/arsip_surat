<?php

namespace App\Livewire;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function isAdmin(): bool
    {
        return Auth::user()->isAdmin();
    }

    #[Computed]
    public function stats(): array
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // ADMIN: Lihat semua data
            return [
                'total_arsip' => Archive::count(),
                'arsip_hari_ini' => Archive::whereDate('created_at', today())->count(),
                'surat_masuk' => Archive::byJenis('masuk')->count(),
                'surat_keluar' => Archive::byJenis('keluar')->count(),
                'disposisi_pending' => Disposition::pending()->count(),
                'total_users' => User::count(),
                'total_kategori' => Category::count(),
            ];
        } else {
            // STAF: Lihat data yang relevan dengan mereka
            return [
                'arsip_diupload' => Archive::where('uploader_id', $user->id)->count(),
                'arsip_hari_ini' => Archive::where('uploader_id', $user->id)->whereDate('created_at', today())->count(),
                'disposisi_masuk' => Disposition::where('receiver_id', $user->id)->pending()->count(),
                'disposisi_selesai' => Disposition::where('receiver_id', $user->id)->selesai()->count(),
                'disposisi_terkirim' => Disposition::where('sender_id', $user->id)->count(),
            ];
        }
    }

    #[Computed]
    public function recentArchives()
    {
        // Semua user bisa lihat arsip terbaru
        return Archive::with(['category', 'uploader'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function recentDispositions()
    {
        $user = Auth::user();

        return Disposition::with(['archive', 'sender', 'receiver'])
            ->where('receiver_id', $user->id)
            ->pending()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function monthlyStats(): array
    {
        $months = [];

        // Mulai dari bulan terkini ke bulan terlama (terbaru di kiri)
        for ($i = 0; $i <= 5; $i++) {
            $date = now()->subMonths($i);

            $count = Archive::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $months[] = [
                'name' => $date->translatedFormat('M'),
                'value' => $count,
                'label' => 'arsip',
            ];
        }

        return $months;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
