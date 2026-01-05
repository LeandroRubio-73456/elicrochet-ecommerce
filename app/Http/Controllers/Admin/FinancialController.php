<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    /**
     * Display financial dashboard
     */
    public function index()
    {
        // 1. Total Income (Paid or Completed)
        $totalIncome = Order::whereIn('status', ['paid', 'completed', 'shipped', 'ready_to_ship'])
            ->sum('total_amount');

        // 2. Orders last 30 days
        $ordersLast30Days = Order::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // 4. Chart Data (Sales per Month - Last 6 Months)
        $dateField = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $salesData = Order::select(
            DB::raw('sum(total_amount) as sum'),
            DB::raw("$dateField as months")
        )
            ->whereIn('status', ['paid', 'completed', 'shipped', 'working'])
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('months')
            ->orderBy('months', 'ASC')
            ->get();

        $chartLabels = $salesData->pluck('months');
        $chartValues = $salesData->pluck('sum');

        // 5. Sales History Table (Paid/Completed Orders)
        $salesHistory = Order::whereIn('status', ['paid', 'completed', 'shipped', 'delivered'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('back.finance.index', compact(
            'totalIncome',
            'ordersLast30Days',
            'chartLabels',
            'chartValues',
            'salesHistory'
        ));
    }

    /**
     * Export Report to CSV
     */
    public function export()
    {
        $fileName = 'elicrochet_ventas_'.date('Y-m-d_H-i').'.csv';
        $orders = Order::orderBy('id', 'desc')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Cliente', 'Email', 'Fecha', 'Estado', 'Total', 'Tipo'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fwrite($file, "\xEF\xBB\xBF");

            // Title Row
            fputcsv($file, ['Reporte Financiero - EliCrochet'], ';');
            fputcsv($file, [], ';'); // Empty row

            // Headers
            fputcsv($file, $columns, ';');

            foreach ($orders as $order) {
                $row['ID'] = $order->id;
                $row['Cliente'] = $order->customer_name;
                $row['Email'] = $order->customer_email;
                $row['Fecha'] = $order->created_at->format('Y-m-d H:i');
                $row['Estado'] = ucfirst($order->status);
                $row['Total'] = $order->total_amount;
                $row['Tipo'] = ucfirst($order->type ?? 'Stock');

                fputcsv($file, [$row['ID'], $row['Cliente'], $row['Email'], $row['Fecha'], $row['Estado'], $row['Total'], $row['Tipo']], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
