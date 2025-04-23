namespace App\Http\Controllers\Apps;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Profit;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Initialize current day and week
        $day = date('d');
        $week = Carbon::now()->subDays(7);

        // Get weekly sales chart data
        list($sales_date, $grand_total) = $this->getSalesChartData($week);

        // Get today's sales and profit data
        $count_sales_today = Transaction::whereDay('created_at', $day)->count();
        $sum_sales_today = Transaction::whereDay('created_at', $day)->sum('grand_total');
        $sum_profits_today = Profit::whereDay('created_at', $day)->sum('total');

        // Get products with limited stock
        $products_limit_stock = Product::with('category')->where('stock', '<=', 10)->get();

        // Get best-selling products
        list($product, $total) = $this->getBestSellingProducts();

        // Return data to the view
        return Inertia::render('Apps/Dashboard/Index', [
            'sales_date' => $sales_date,
            'grand_total' => $grand_total,
            'count_sales_today' => (int)$count_sales_today,
            'sum_sales_today' => (int)$sum_sales_today,
            'sum_profits_today' => (int)$sum_profits_today,
            'products_limit_stock' => $products_limit_stock,
            'product' => $product,
            'total' => $total
        ]);
    }

    /**
     * Get sales chart data for the last 7 days.
     *
     * @param \Carbon\Carbon $week
     * @return array
     */
    private function getSalesChartData($week)
    {
        $chart_sales_week = DB::table('transactions')
            ->addSelect(DB::raw('DATE(created_at) as date, SUM(grand_total) as grand_total'))
            ->where('created_at', '>=', $week)
            ->groupBy('date')
            ->get();

        $sales_date = [];
        $grand_total = [];

        foreach ($chart_sales_week as $result) {
            $sales_date[] = $result->date;
            $grand_total[] = (int)$result->grand_total;
        }

        return [$sales_date, $grand_total];
    }

    /**
     * Get the best selling products.
     *
     * @return array
     */
    private function getBestSellingProducts()
    {
        $chart_best_products = DB::table('transaction_details')
            ->addSelect(DB::raw('products.title as title, SUM(transaction_details.qty) as total'))
            ->join('products', 'products.id', '=', 'transaction_details.product_id')
            ->groupBy('transaction_details.product_id')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();

        $product = [];
        $total = [];

        foreach ($chart_best_products as $data) {
            $product[] = $data->title;
            $total[] = (int)$data->total;
        }

        return [$product, $total];
    }
}
