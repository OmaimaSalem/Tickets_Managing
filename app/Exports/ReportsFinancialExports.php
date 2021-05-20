<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportsFinancialExports implements FromView,ShouldAutoSize
{
    private $projects;

    public function __construct($projects){
        $this->projects = $projects;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.financial_report', [
            'projects' => json_decode($this->projects)
        ]);
    }



}
