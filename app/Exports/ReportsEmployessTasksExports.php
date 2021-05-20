<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportsEmployessTasksExports implements FromView,ShouldAutoSize
{
    private $items;

    public function __construct($items){
        $this->items = $items;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.employees_tasks_report', [
            'items' => json_decode($this->items)
        ]);
    }



}
