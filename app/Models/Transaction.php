<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model {

 protected $_fillable = array(
  'date',
  'description',
  'amount',
  'business_id',
  'category_id',
  'source_id',
  'transaction_type',
  'status',
  'name',
 );

 protected $_casts = array(
  'date' => 'date',
 );

 public function business(): BelongsTo {
  return $this->belongsTo( Business::class );
 }

 public function category(): BelongsTo {
  return $this->belongsTo( Category::class );
 }

 public function source(): BelongsTo {
  return $this->belongsTo( Source::class );
 }
}
