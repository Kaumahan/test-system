<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Source;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
 public function index(Request $request)
 {
  $transactions = Transaction::with([ 'business', 'category', 'source' ])
   ->when($request->business_id, fn($q) => $q->where('business_id', $request->business_id))
   ->when($request->status, fn($q) => $q->where('status', $request->status))
   ->orderBy('date', 'desc')
   ->paginate(15);

  $businesses = Business::orderBy('name')->get();
  return view('transactions.index', compact('transactions', 'businesses'));
 }

 public function upload(Request $request)
 {
  $request->validate([ 'csv_file' => 'required|mimes:csv,txt|max:2048' ]);

  $file   = $request->file('csv_file');
  $handle = fopen($file->getRealPath(), 'r');
  fgetcsv($handle); // Skip header

  $count = 0;
  while (($row = fgetcsv($handle)) !== false) {
   if (empty(array_filter($row))) {
    continue;
   }

   // 1. Manual Check for Business
   $biz = Business::where('name', $row[ 3 ])->first();
   if (!$biz) {
    $biz       = new Business();
    $biz->name = $row[ 3 ];
    $biz->save();
   }

   // 2. Manual Check for Category
   $cat = Category::where('name', $row[ 4 ])->first();
   if (!$cat) {
    $cat       = new Category();
    $cat->name = $row[ 4 ];
    $cat->save();
   }

   // 3. Manual Check for Source
   $src = Source::where('name', $row[ 6 ])->first();
   if (!$src) {
    $src       = new Source();
    $src->name = $row[ 6 ];
    $src->save();
   }

   // 4. Manual Check for Transaction (to avoid duplicates in the main table)
   $exists = Transaction::where([
    [ 'date', '=', $row[ 0 ] ],
    [ 'description', '=', $row[ 1 ] ],
    [ 'amount', '=', $row[ 2 ] ],
    [ 'business_id', '=', $biz->id ],
    ])->exists();

   if (!$exists) {
    $transaction                   = new Transaction();
    $transaction->date             = $row[ 0 ];
    $transaction->description      = $row[ 1 ];
    $transaction->amount           = $row[ 2 ];
    $transaction->business_id      = $biz->id;
    $transaction->category_id      = $cat->id;
    $transaction->source_id        = $src->id;
    $transaction->transaction_type = $row[ 5 ];
    $transaction->status           = $row[ 7 ];
    $transaction->save();
    $count++;
   }
  }

  fclose($handle);
  return back()->with('success', "Processed $count records.");
 }
}
