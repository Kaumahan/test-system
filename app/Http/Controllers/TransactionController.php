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
  // 1. Validation for file type and existence
  $request->validate([
   'csv_file' => 'required|file|mimes:csv,txt|max:2048',
   ], [
   'csv_file.mimes'    => 'The file must be a CSV',
   'csv_file.required' => 'Please select a file to upload.',
   ]);

  $file   = $request->file('csv_file');
  $handle = fopen($file->getRealPath(), 'r');
  $header = fgetcsv($handle); // Read header

  // 2. Check if file is empty (only header or totally empty)
  if (!$header || ($row = fgetcsv($handle)) === false) {
   fclose($handle);
   return back()->with('error', 'The uploaded file is empty or has no data rows.');
  }

  // Reset pointer to after header because we just "peeked" at the first row
  rewind($handle);
  fgetcsv($handle);

  $count      = 0;
  $duplicates = 0;

  while (($row = fgetcsv($handle)) !== false) {
   if (empty(array_filter($row))) {
    continue;
   }

   $biz = Business::where('name', $row[ 3 ])->first() ?: (new Business([ 'name' => $row[ 3 ] ]));
   if (!$biz->exists) {
    $biz->save();
   }

   $cat = Category::where('name', $row[ 4 ])->first() ?: (new Category([ 'name' => $row[ 4 ] ]));
   if (!$cat->exists) {
    $cat->save();
   }

   $src = Source::where('name', $row[ 6 ])->first() ?: (new Source([ 'name' => $row[ 6 ] ]));
   if (!$src->exists) {
    $src->save();
   }

   // 3. Check for Duplicate Uploads (Integrity Check)
   $exists = Transaction::where([
    [ 'date', '=', $row[ 0 ] ],
    [ 'description', '=', $row[ 1 ] ],
    [ 'amount', '=', $row[ 2 ] ],
    [ 'business_id', '=', $biz->id ],
    ])->exists();

   if ($exists) {
    $duplicates++;
    continue;
   }

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

  fclose($handle);

  if (0 === $count && $duplicates > 0) {
   return back()->with('info', "No new records added. $duplicates duplicate rows were skipped.");
  }

  return back()->with('success', "Processed $count records. Skipped $duplicates duplicates.");
 }
}
