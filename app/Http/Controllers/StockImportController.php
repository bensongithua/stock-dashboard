<?php

namespace App\Http\Controllers;

use App\Models\StockPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StockImportController extends Controller
{
    public function showUpload()
    {
        return view('stocks.upload');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $imported = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle);

            if (! $header || count($header) < 3) {
                fclose($handle);

                return redirect()->back()->with('error', 'Invalid CSV format. Expected: stock,price,date');
            }

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) {
                    $errors[] = 'Skipped invalid row: '.implode(',', $row);

                    continue;
                }

                try {
                    $stockName = trim($row[0], ' "');
                    $price = floatval(trim($row[1]));
                    $date = date('Y-m-d', strtotime(trim($row[2])));

                    if (empty($stockName) || $price <= 0 || $date == '1970-01-01') {
                        $errors[] = 'Skipped invalid data: '.implode(',', $row);

                        continue;
                    }

                    StockPrice::updateOrCreate(
                        [
                            'stock_name' => $stockName,
                            'date' => $date,
                            'user_id' => Auth::id(),
                        ],
                        ['price' => $price]
                    );

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = 'Error processing row: '.implode(',', $row).' - '.$e->getMessage();
                }
            }
            fclose($handle);
        }

        $message = "Successfully imported {$imported} stock prices.";
        if (! empty($errors)) {
            $message .= ' '.count($errors).' errors occurred.';
        }

        return redirect()->route('dashboard')->with(
            $errors ? 'warning' : 'success',
            $message
        );
    }
}
