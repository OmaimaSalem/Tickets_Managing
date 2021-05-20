<?php

namespace Modules\Contract\Http\Controllers;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;
use Modules\Contract\Jobs\ContractCreateJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ContractItems\Entities\ContractItems;
use Modules\Contract\Entities\Contract;
use Modules\Contract\Http\Requests\AddContractRequest;
use Modules\Contract\Http\Requests\DeleteContractRequest;
use Modules\Contract\Http\Requests\UpdateContractRequest;
use Modules\Contract\Http\Resources\ContractCollection;
use Modules\Contract\Http\Resources\ContractResource;

class ContractController extends BaseController
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
      $this->middleware('permission:contract-list', ['only' => ['index', 'show']]);
      $this->middleware('permission:contract-create', ['only' => ['store']]);
      $this->middleware('permission:contract-edit', ['only' => ['update']]);
      $this->middleware('permission:contract-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $contracts = Contract::paginate();

        return $this->sendResponse(new ContractCollection($contracts), 'Contracts retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(AddContractRequest $request)
    {

        $contract = Contract::where('offer_id', $request->offer)->first();

        if($contract) {
            foreach($contract->contract_items as $item) {
                $item->delete();
            }
            $contract->delete();
        }

        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        $input['offer_id'] = $request->offer;
        try {
          $contract = Contract::create($input);
          foreach($input['items'] as $item) {
            $item['contract_id'] = $contract->id;
            $item['created_at'] = Carbon::now();
            $item['created_by'] = auth()->user()->id;
            ContractItems::create($item);
          }
        } catch (Exception $ex) {
          throw new ItemNotCreatedException('Contract');
        }

        return $this->sendResponse(new ContractResource($contract), 'Contract created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Contract $contract)
    {
        return $this->sendResponse(new ContractResource($contract), 'Contract retrieved successfully.');
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
        //
    }

    public function send(Contract $contract) {
      ContractCreateJob::dispatch($contract, auth()->user());
      return $this->sendResponse(new ContractResource($contract), 'Contract sent successfully.');
    }
}
