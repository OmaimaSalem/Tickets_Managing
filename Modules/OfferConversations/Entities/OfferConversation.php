<?php

namespace Modules\OfferConversations\Entities;

use Illuminate\Database\Eloquent\Model;

class OfferConversation extends Model
{
  protected $table = 'offer_conversations';
  public $timestamps = true;
  protected $fillable = array('offer_id', 'conversation', 'created_by', 'updated_by');

  public function creator()
  {
      return $this->hasOne('App\Models\User', 'id',  'created_by');
  }

  public function updater()
  {
      return $this->hasOne('App\Models\User', 'id', 'updated_by');
  }

  public function offer()
  {
      return $this->hasOne('Modules\Offer\Entities\Offer', 'id', 'offer_id');
  }
}
