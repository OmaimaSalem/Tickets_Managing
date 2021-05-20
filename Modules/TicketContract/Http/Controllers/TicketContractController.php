<?php

namespace Modules\TicketContract\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController;
use Modules\TicketContract\Entities\TicketContract;
use Modules\TicketContract\Entities\TicketContractItem;
use App\Models\User;



use App\Exports\ContractsExport;
use Maatwebsite\Excel\Facades\Excel;



class TicketContractController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $contracts = TicketContract::with(['items','user.metadata','ticket.tasks'=>function($tasks){
                $tasks->status_id = 4;
        }])->paginate();
        return $this->sendResponse($contracts, 'contract retrived successfully.');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        if(!isset($request->contractData['client']['id'])){
            return $this->sendError('Please select user first',[],500);
        }
        $user     = User::find($request->contractData['client']['id']);

        $user->metadata()->updateOrCreate(['user_id'=>$user->id],[
            'first_name'  => $request->contractData['client']['metadata']['first_name'],
            'last_name'   => $request->contractData['client']['metadata']['last_name'],
            'address'     => $request->contractData['client']['metadata']['address'],
            'postal_code' => $request->contractData['client']['metadata']['postal_code'],
            'country'     => $request->contractData['client']['metadata']['country'],
            'state'       => $request->contractData['client']['metadata']['state'],
            'company'     => $request->contractData['client']['metadata']['company'], 
        ]);
        $contract = TicketContract::updateOrCreate(['ticket_id'   => $request->contractData['ticket_id'],    ],[
            'user_id'     => $user->id,
            'ticket_id'   => $request->contractData['ticket_id'],
            'total_price' => $request->contractData['total_price'],
        ]);

        $contract->items()->delete();
        $items = [];


        foreach ($request->contractData['items'] as  $item) {
            if(!$item['item']) continue;
            $items[] = new TicketContractItem([
              "ticket_id"  => $request->contractData['ticket_id'],
              "ticket_contruct_id" => $contract->id,
              "item"             => $item['item'],
              "item_id"          => null,
              "item_price"       => $item['price'] ? $item['price'] : 0,
              "item_count"       => $item['amount'] ? $item['vat'] : 1,
              "item_total_price" => $item['total_price'],
              "item_vat"         => $item['vat'] ? $item['vat'] : 0,
              "item_net"         => $item['net'],
              "item_discount"    => $item['discount'] ? $item['vat'] : 0,
              "item_total"       => $item['total'],
            ]);
      }

        $contract->items()->saveMany($items);
        return $this->sendResponse($request->all(), 'contract created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //return view('ticketcontract::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //return view('ticketcontract::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $contract = TicketContract::find($id);
        if($contract){
            $contract->items()->delete();
            $contract->delete();
            return $this->sendResponse($id, 'contract created successfully.');
        }
    }


    public function excel_export(){
        $contracts = TicketContract::with(['items','user.metadata'])->get();
        // dd($contracts);
        return Excel::download(new ContractsExport(json_encode($contracts)), date('d-m-Y').'-contracts.xlsx');
    }
}
