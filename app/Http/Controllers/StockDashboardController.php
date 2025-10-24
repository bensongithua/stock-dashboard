<?php

namespace App\Http\Controllers;

use App\Models\StockPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = StockPrice::where('user_id', $userId);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $stockRanges = $query->select('stock_name',
            DB::raw('MIN(date) as first_date'),
            DB::raw('MAX(date) as last_date')
        )->groupBy('stock_name')->get();

        $performances = collect();

        foreach ($stockRanges as $range) {
            $firstPrice = StockPrice::where('user_id', $userId)
                ->where('stock_name', $range->stock_name)
                ->where('date', $range->first_date)
                ->first();

            $lastPrice = StockPrice::where('user_id', $userId)
                ->where('stock_name', $range->stock_name)
                ->where('date', $range->last_date)
                ->first();

            if ($firstPrice && $lastPrice && $firstPrice->price > 0) {
                $priceGain = $lastPrice->price - $firstPrice->price;
                $percentageGain = ($priceGain / $firstPrice->price) * 100;

                $performances->push([
                    'stock_name' => $range->stock_name,
                    'gain_percentage' => round($percentageGain, 2),
                    'price_gain' => round($priceGain, 6),
                    'first_price' => $firstPrice->price,
                    'last_price' => $lastPrice->price,
                    'first_date' => $range->first_date,
                    'last_date' => $range->last_date,
                ]);
            }
        }

        $topPerformers = $performances->sortByDesc('gain_percentage')->take(5)->values();

        return view('stocks.dashboard', [
            'topPerformers' => $topPerformers,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
