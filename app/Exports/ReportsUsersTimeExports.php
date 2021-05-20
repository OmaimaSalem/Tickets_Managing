<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Http\Resources\User\UserResource;

class ReportsUsersTimeExports implements FromView,ShouldAutoSize
{
    private $users;

    public function __construct($users){
        $this->users = $users;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.time_report', [
            'users' => json_decode($this->users)
        ]);
    }



}
