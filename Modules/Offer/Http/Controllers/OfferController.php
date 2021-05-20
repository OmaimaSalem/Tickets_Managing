<?php

namespace Modules\Offer\Http\Controllers;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\OfferItems\Entities\OfferItems;
use Modules\Offer\Entities\Offer;
use Modules\Offer\Http\Requests\AddOfferRequest;
use Modules\Offer\Http\Requests\DeleteOfferRequest;
use Modules\Offer\Http\Requests\UpdateOfferRequest;
use Modules\Offer\Http\Resources\OfferCollection;
use Modules\Offer\Http\Resources\OfferResource;
use Modules\Offer\Jobs\OfferCreateJob;

class OfferController extends BaseController
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
      $this->middleware('permission:offer-list', ['only' => ['index', 'show']]);
      $this->middleware('permission:offer-create', ['only' => ['store']]);
      $this->middleware('permission:offer-edit', ['only' => ['update']]);
      $this->middleware('permission:offer-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $offers = Offer::orderBy('created_at', 'DESC')->paginate();

        return $this->sendResponse(new OfferCollection($offers), 'Offers retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(AddOfferRequest $request)
    {
        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        // $offer_last = Offer::latest()->first();
        try {
          $offer = Offer::create($input);
          foreach($input['items'] as $item) {
            $item['offer_id'] = $offer->id;
            $item['created_at'] = Carbon::now();
            $item['created_by'] = auth()->user()->id;
            OfferItems::create($item);
          }
        } catch (Exception $ex) {
          throw new ItemNotCreatedException('Offer');
        }

        return $this->sendResponse(new OfferResource($offer), 'Offer created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Offer $offer)
    {
        return $this->sendResponse(new OfferResource($offer), 'Offer retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Offer $offer, UpdateOfferRequest $request)
    {
        $input = $request->validated();
        $offer->updated_at = Carbon::now();
        $offer->updated_by = auth()->user()->id;
        try {
          $updated = $offer->fill($input)->save();
          foreach ($offer->offer_items as $item) {
            $item->delete();
          };
          foreach($input['items'] as $item) {
            $item['offer_id'] = $offer->id;
            $item['created_at'] = Carbon::now();
            $item['created_by'] = auth()->user()->id;
            OfferItems::create($item);
          }
        } catch (Exception $ex) {
          throw new ItemNotCreatedException('Offer');
        }
        if (!$updated)
        throw new ItemNotUpdatedException('Offer');

        return $this->sendResponse(new OfferResource($offer), 'Offer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $offer = Offer::find($id);

    if (is_null($offer)) {
      throw new ItemNotFoundException($id);
    }

    if ($offer->offer_items->isNotEmpty()) {
      throw new InvalidDataException(
        [
          'items' => $offer->offer_items->toArray()
        ],
        'Can\'t delete!, Offer has items.'
      );
    }

      $offer->delete();

    return $this->sendResponse(new OfferResource($offer), 'Offer deleted successfully.');
    }

    public function send(Offer $offer) {
      OfferCreateJob::dispatch($offer, auth()->user());
      return $this->sendResponse(new OfferResource($offer), 'Offer sent successfully.');
    }
}
