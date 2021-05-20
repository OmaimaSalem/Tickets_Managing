<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsUsersExports implements FromCollection, WithHeadings,ShouldAutoSize,WithMapping
{
    private $users;

    public function __construct($users){
        $this->users = $users;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->users;
    }
    public function headings(): array
    {
        return ["Employee name", "Position", "Mobile","Email","Company"];
    }



    public function map($project_report): array
    {
        return [
            "Employee name" => $project_report->name,
            "Position"      => $project_report->metadata && $project_report->metadata->position ? $project_report->metadata->position : '-----',
            "Mobile"        => $project_report->metadata && $project_report->metadata->mobile ? $project_report->metadata->mobile : '-----',
            "Email"         => $project_report->metadata && $project_report->metadata->email ? $project_report->metadata->email : '-----',
            "Company"       => $project_report->metadata && $project_report->metadata->company ? $project_report->metadata->company : '-----',
         ];
     }



}
