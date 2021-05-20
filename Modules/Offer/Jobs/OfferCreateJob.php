<?php

namespace Modules\Offer\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Offer\Entities\Offer;
use Modules\Offer\Notifications\OfferCreatedNotification;
use PDF;

class OfferCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $offer, $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Offer $offer, $user)
    {
        $this->offer = $offer;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = User::find($this->offer->client_id);
        if (!$client) {
            throw new ItemNotFoundException($client);
        }
        $result = $this->generate_pdf();

        $temp = \App::getLocale();

        \App::setLocale(isset($client->metadata->language) ? $client->metadata->language : 'de');
        
        if($client->support === 0) {
            try {
                $client->notify(new OfferCreatedNotification($this->offer, $this->user, $result));
            } catch (\Exception $ex) {
                throw new \Exception($ex);
            }
        }

        \App::setLocale($temp);
    }

    public function generate_pdf() {
        $offer = $this->offer;
        $pdf = PDF::loadView('offer.index', compact('offer'));
        return ['pdf' => $pdf,'title' => 'Offer-'.$offer->id.'.pdf'];
    }
}
