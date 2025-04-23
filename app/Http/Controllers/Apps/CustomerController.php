namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest; // Use FormRequest class

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::when(request()->q, function ($customers) {
            $customers = $customers->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        return Inertia::render('Apps/Customers/Index', [
            'customers' => $customers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Apps/Customers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        Customer::create([
            'name'    => $request->name,
            'no_telp' => $request->no_telp,
            'address' => $request->address,
        ]);

        return redirect()->route('apps.customers.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return Inertia::render('Apps/Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $customer->update([
            'name'    => $request->name,
            'no_telp' => $request->no_telp,
            'address' => $request->address,
        ]);

        return redirect()->route('apps.customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('apps.customers.index');
    }
}
