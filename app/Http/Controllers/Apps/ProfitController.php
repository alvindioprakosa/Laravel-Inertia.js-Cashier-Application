namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Profit;
use Illuminate\Http\Request;
use App\Exports\ProfitsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfitController extends Controller
{
    /**
     * Show the profit index page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Apps/Profits/Index');
    }
    
    /**
     * Filter profits by date range.
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

        // Get filtered data
        $profitsData = $this->getProfitsByDateRange($request->start_date, $request->end_date);

        return Inertia::render('Apps/Profits/Index', [
            'profits'   => $profitsData['profits'],
            'total'     => (int) $profitsData['total'],
        ]);
    }

    /**
     * Export profits as Excel file.
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

        return Excel::download(new ProfitsExport($request->start_date, $request->end_date), 
            'profits_' . $request->start_date . '_to_' . $request->end_date . '.xlsx');
    }
    
    /**
     * Generate profits PDF report.
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

        // Get filtered data
        $profitsData = $this->getProfitsByDateRange($request->start_date, $request->end_date);

        // Generate PDF
        $pdf = PDF::loadView('exports.profits', [
            'profits' => $profitsData['profits'],
            'total'   => $profitsData['total'],
        ]);

        return $pdf->download('profits_' . $request->start_date . '_to_' . $request->end_date . '.pdf');
    }

    /**
     * Get profits data by date range.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return array
     */
    private function getProfitsByDateRange($startDate, $endDate)
    {
        $profits = Profit::with('transaction')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $total = Profit::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total');

        return [
            'profits' => $profits,
            'total'   => $total,
        ];
    }
}
