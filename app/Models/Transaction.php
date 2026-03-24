<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
 // Mass assignment protection
 protected $fillable = [
  'date',
  'description',
  'amount',
  'business_id',
  'category_id',
  'source_id',
  'transaction_type',
  'status',
  'name',
  ];

 // Ensure 'date' is treated as a Carbon instance for Tailwind formatting
 protected $_casts = [
  'date' => 'date',
  ];

 /**
  * Relationships (Normalization)
  */
 public function business(): BelongsTo
 {
  return $this->belongsTo(Business::class);
 }

 public function category(): BelongsTo
 {
  return $this->belongsTo(Category::class);
 }

 public function source(): BelongsTo
 {
  return $this->belongsTo(Source::class);
 }
}
