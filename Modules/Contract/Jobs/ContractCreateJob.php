<?php

namespace Modules\Contract\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Contract\Entities\Contract;
use Modules\Contract\Notifications\ContractCreatedNotification;
use App\Models\User;
use PDF;

class ContractCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $contract, $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Contract $contract, $user)
    {
      $this->contract = $contract;
      $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $client = User::find($this->contract->client_id);
      if (!$client) {
          throw new ItemNotFoundException($client);
      }

      $temp = \App::getLocale();

      \App::setLocale(isset($client->metadata->language) ? $client->metadata->language : 'de');
      if($client->support === 0) {
        try {
          $client->notify(new ContractCreatedNotification($this->contract, $this->user));
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
      }

      \App::setLocale($temp);
    }
}
