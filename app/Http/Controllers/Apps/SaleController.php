namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Transaction;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Show the sales index page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Apps/Sales/Index');
    }

    /**
     * Filter sales data by date range.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function filter(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        // Get sales data and total sales by date range
        $salesData = $this->getSalesByDateRange($request->start_date, $request->end_date);

        return Inertia::render('Apps/Sales/Index', [
            'sales'    => $salesData['sales'],
            'total'    => (int) $salesData['total'],
        ]);
    }

    /**
     * Export sales data to an Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        return Excel::download(new SalesExport($request->start_date, $request->end_date), 
            'sales_' . $request->start_date . '_to_' . $request->end_date . '.xlsx');
    }

    /**
     * Generate a PDF of sales data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Barryvdh\DomPDF\PDF
     */
    public function pdf(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        // Get sales data and total sales by date range
        $salesData = $this->getSalesByDateRange($request->start_date, $request->end_date);

        // Generate PDF
        $pdf = PDF::loadView('exports.sales', [
            'sales' => $salesData['sales'],
            'total' => $salesData['total'],
        ]);

        return $pdf->download('sales_' . $request->start_date . '_to_' . $request->end_date . '.pdf');
    }

    /**
     * Get sales data by date range.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return array
     */
    private function getSalesByDateRange($startDate, $endDate)
    {
        $sales = Transaction::with('cashier', 'customer')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $total = Transaction::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('grand_total');

        return [
            'sales' => $sales,
            'total' => $total,
        ];
    }
}
