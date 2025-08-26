<?php

namespace admin\course_reports\Controllers;

use admin\courses\Models\CoursePurchase;
use admin\course_transactions\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ReportManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admincan_permission:report_manager_list')->only(['index']);
    }

    public function index(Request $request)
    {
        try {
            // Optional date filter
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

            // Transaction Stats
            $transactionCount = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
            $transactionTotal = Transaction::where('status', 'success')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            // Purchase Stats
            $purchaseCount = CoursePurchase::whereBetween('created_at', [$startDate, $endDate])->count();
            $purchaseRevenue = CoursePurchase::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            // Status breakdown
            $purchaseByStatus = CoursePurchase::selectRaw('status, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('status')
                ->pluck('count', 'status');
            return view('report::admin.index', compact(
                'startDate',
                'endDate',
                'transactionCount',
                'transactionTotal',
                'purchaseCount',
                'purchaseRevenue',
                'purchaseByStatus'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load reports: ' . $e->getMessage());
        }
    }
}
