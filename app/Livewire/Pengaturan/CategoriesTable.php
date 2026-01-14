<?php

namespace App\Livewire\Pengaturan;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CategoriesTable extends DataTableComponent
{
    protected $model = Category::class;

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setEmptyMessage('Data tidak ditemukan');
        $this->setQueryStringDisabled();
        $this->setColumnSelectDisabled();
    }

    public function columns(): array
    {
        return [
            Column::make('No', '_id')
                ->format(function ($value, $row, Column $column) {
                    static $index = 0;

                    return ++$index + (($this->getPage() - 1) * $this->getPerPage());
                })
                ->html(),

            Column::make('Kode', 'code')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<span class="font-semibold text-zinc-900 dark:text-white">'.e($value).'</span>')
                ->html(),

            Column::make('Nama Kategori', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Masa Simpan', 'retention_years')
                ->sortable()
                ->format(fn ($value) => ($value ?? 0).' tahun'),

            Column::make('Aksi', '_id')
                ->format(function ($value, $row, Column $column) {
                    return view('livewire.pengaturan.partials.kategori-actions', ['category' => $row]);
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        return Category::query();
    }
}
