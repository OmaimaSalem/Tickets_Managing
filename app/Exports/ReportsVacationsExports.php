<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportsVacationsExports implements FromView,ShouldAutoSize
{
    private $vacations;

    public function __construct($vacations){
        $this->vacations = $vacations;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.vacations_report', [
            'vacations' => $this->vacations
        ]);
    }



}
