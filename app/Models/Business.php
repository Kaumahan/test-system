<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
 protected $_fillable = [ 'name', 'username' ];

 public function transactions()
 {
  return $this->hasMany(Transaction::class);
 }
}
