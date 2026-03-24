<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query();

        if ($request->has('business') && $request->business != '') {
            $query->where('business', $request->business);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(15);
        $businesses = Transaction::distinct()->pluck('business');

        return view('transactions.index', compact('transactions', 'businesses'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);

        $rowCount = 0;
        $skippedCount = 0;

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (empty(array_filter($row))) continue;

            $data = [
                'date'             => $row[0],
                'description'      => $row[1],
                'amount'           => (float)$row[2],
                'business'         => $row[3],
                'category'         => $row[4],
                'transaction_type' => $row[5],
                'source'           => $row[6],
                'status'           => $row[7],
            ];

            $exists = Transaction::where('date', $data['date'])
                ->where('description', $data['description'])
                ->where('amount', $data['amount'])
                ->exists();

            if (!$exists) {
                Transaction::create($data);
                $rowCount++;
            } else {
                $skippedCount++;
            }
        }

        fclose($handle);

        return back()->with('success', "Imported $rowCount records. Skipped $skippedCount duplicates.");
    }
}