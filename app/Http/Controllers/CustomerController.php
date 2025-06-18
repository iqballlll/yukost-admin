<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelpers;
use App\Helpers\MessageResponse;
use App\Helpers\ValidationRules;
use App\Models\Customer;
use App\Models\CustomerCompany;
use App\Models\CustomerGroup;
use App\Models\CustomerOutlet;
use App\Models\CustomerPrice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedSort;
class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $tab = strtolower($request->get('tab'));

        if (!in_array($tab, ['group', 'personal'])) {
            abort(404);
        }

        $groupIds = CustomerGroup::select('id', 'group_name')->get();

        $data = ['title' => 'Customer', 'groups' => $this->handleGroupTab($request), 'personal' => $this->handlePersonalTab($request), 'groupIds' => $groupIds];

        return view('pages.customer.index', $data);
    }

    public function ajaxGetCustomPrice(Request $request)
    {
        $customerOutletId = $request->id;

        $customerPrices = CustomerPrice::with('product')
            ->where('outlet_id', $customerOutletId)
            ->when($request->sku, function ($query, $sku) {
                $query->whereHas('product', fn($q) => $q->where('product_id', 'like', "%$sku%"));
            })
            ->when($request->product_name, function ($query, $name) {
                $query->whereHas('product', fn($q) => $q->where('product_name', 'like', "%$name%"));
            })
            ->when($request->base_price, function ($query, $price) {
                $price = (int) str_replace(['Rp.', '.', ','], '', $price);
                $query->where('base_price', $price);
            })
            ->when($request->selling_price, function ($query, $price) {
                $price = (int) str_replace(['Rp.', '.', ','], '', $price);
                $query->where('selling_price', $price);
            })
            ->paginate();

        $filters = $request->only([
            'product_id',
            'product_name',
            'base_price',
            'selling_price',
        ]);

        return view('components.modal-products-pagination', ['outletId' => $customerOutletId, 'customerPrices' => $customerPrices, 'isCustomPrice' => $request->is_custom_price, 'filters' => $filters])->render();
    }

    private function handleGroupTab(Request $request)
    {
        $query = CustomerGroup::query();
        $perPage = $request->per_page ?? 5;
        $hasOutletFilter = $request->group_id || $request->outlet || $request->address || $request->contact || $request->kontra_faktur || !is_null($request->custom_price) || !is_null($request->status);

        if ($request->group_id) {
            $query->where('id', $request->group_id);
        }

        // Filter di level utama
        if ($hasOutletFilter) {
            $query->whereHas('companies.outlets', function ($q) use ($request) {
                $this->applyOutletFilters($q, $request);
            });
        }

        // With eager loading
        $query->with([
            'companies' => function ($q) use ($request, $hasOutletFilter) {
                if ($hasOutletFilter) {
                    $q->whereHas('outlets', function ($q) use ($request) {
                        $this->applyOutletFilters($q, $request);
                    });
                }
            },
            'companies.outlets' => function ($q) use ($request, $hasOutletFilter) {
                if ($hasOutletFilter) {
                    $this->applyOutletFilters($q, $request);
                }
            }
        ]);

        $groups = $query->orderByDesc('created_at')->paginate($perPage);

        if ($hasOutletFilter) {
            $groups->setCollection(
                $groups->getCollection()->map(function ($group) use ($request) {
                    $group->companies = $group->companies->map(function ($company) use ($request) {
                        $company->outlets = $company->outlets->filter(function ($outlet) use ($request) {
                            return (!$request->outlet || str_contains(strtolower($outlet->outlet_name), strtolower($request->outlet)))
                                && (!$request->address || str_contains(strtolower($outlet->address), strtolower($request->address)))
                                && (!$request->contact || str_contains(strtolower($outlet->contact), strtolower($request->contact)))
                                && (is_null($request->kontra_faktur) || $outlet->kontra_faktur == $request->kontra_faktur)
                                && (is_null($request->custom_price) || $outlet->custom_price == $request->custom_price)
                                && (is_null($request->status) || $outlet->is_active == $request->status)
                            ;
                        })->values();

                        return $company;
                    })->filter(function ($company) {
                        return $company->outlets->isNotEmpty();
                    })->values();

                    return $group;
                })->filter(function ($group) {
                    return $group->companies->isNotEmpty();
                })->values()
            );
        }
        return $groups;
    }
    private function handlePersonalTab(Request $request)
    {
        $query = CustomerOutlet::query()->where('type', 'individual');
        $perPage = $request->per_page ?? 5;
        // Filter
        if ($request->filled('customer_name')) {
            $query->where('outlet_name', 'like', '%' . $request->customer_name . '%');
        }

        if ($request->filled('address')) {
            $query->where('address', 'like', '%' . $request->address . '%');
        }

        if ($request->filled('contact')) {
            $query->where('contact', 'like', '%' . $request->contact . '%');
        }

        if (!is_null($request->custom_price)) {
            $query->where('custom_price', $request->custom_price);
        }

        if (!is_null($request->status)) {
            $query->where('is_active', $request->status);
        }

        $sortField = $request->get('sort', 'customer_name');
        $sortOrder = $request->get('order', 'desc');

        $allowedSorts = [
            'customer_name' => 'outlet_name',
            'address' => 'address',
            'contact' => 'contact',
            'custom_price' => 'custom_price',
            'status' => 'is_active',
        ];

        if (array_key_exists($sortField, $allowedSorts)) {
            $query->orderBy($allowedSorts[$sortField], $sortOrder);
        }

        // Pagination
        $personalCustomers = $query->paginate($perPage)->withQueryString();

        return $personalCustomers;

    }

    private function handleAjaxCustomerPrice(Request $request)
    {

    }

    private function applyOutletFilters($query, $request)
    {
        return $query
            ->when($request->outlet, fn($q) => $q->where('outlet_name', 'like', '%' . $request->outlet . '%'))
            ->when($request->address, fn($q) => $q->where('address', 'like', '%' . $request->address . '%'))
            ->when($request->contact, fn($q) => $q->where('contact', 'like', '%' . $request->contact . '%'))
            ->when($request->kontra_faktur, fn($q) => $q->where('kontra_faktur', $request->kontra_faktur))
            ->when(!is_null($request->custom_price), fn($q) => $q->where('custom_price', $request->custom_price))
            ->when(!is_null($request->status), fn($q) => $q->where('is_active', $request->status));
    }

    public function createGroup(Request $request)
    {
        $customerGroup = null;
        if ($request->group_id) {
            $customerGroup = CustomerGroup::find($request->group_id);
            if (!$customerGroup)
                abort(404);
        }

        $companiesForSelectOption = CustomerCompany::where('group_id', $request->group_id)->get();
        $customerCompany = CustomerCompany::where('group_id', $request->group_id)->paginate();
        $companyIds = CustomerCompany::where('group_id', $request->group_id)->pluck('id');
        $customerOutlet = CustomerOutlet::with([
            'company' => function ($q) {
                $q->select('id', 'company_name');
            }
        ])->whereIn('company_id', $companyIds)->paginate();

        $data = ['title' => 'Tambah Customer Group', 'companies' => $companiesForSelectOption, 'customerCompany' => $customerCompany, 'customerGroup' => $customerGroup, 'customerOutlet' => $customerOutlet];
        return view('pages.customer.create', $data);
    }
    public function createPersonal(Request $request)
    {
        $data = [
            'title' => 'Tambah Customer Personal',
            // karena UI nya untuk edit dan create, maka init personal disini null, supaya ga ada pengkondisian lagi hehee
            'personal' => null
        ];

        return view('pages.customer.create-personal', $data);
    }
    public function editPersonal(Request $request)
    {
        $data = ['title' => 'Edit Customer Personal'];

        if ($request->id) {
            $check = CustomerOutlet::find($request->id);
            if (!$check) {
                abort(404);
            }
            $data['personal'] = $check;
        }

        return view('pages.customer.create-personal', $data);
    }

    public function editGroup(Request $request)
    {

        $customerGroup = CustomerGroup::findOrFail($request->group_id);

        if (!$customerGroup)
            abort(404);

        $customerCompany = CustomerCompany::where('group_id', $request->group_id)->paginate();
        $companyIds = $customerCompany->pluck('id');
        $companiesForSelectOption = CustomerCompany::where('group_id', $request->group_id)->get();
        $customerOutlet = CustomerOutlet::with([
            'company' => function ($q) {
                $q->select('id', 'company_name');
            }
        ])->whereIn('company_id', $companyIds)->paginate();

        $data = ['title' => 'Edit Customer', 'customerGroup' => $customerGroup, 'customerCompany' => $customerCompany, 'customerOutlet' => $customerOutlet, 'companies' => $companiesForSelectOption];
        return view('pages.customer.create', $data);
    }

    public function storeOrUpdateCustomerGroup(Request $request)
    {
        try {

            $rules = ValidationRules::get('customer_groups');
            $messages = ValidationRules::messages('customer_groups');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($request->customer_group_id) {
                if ($validator->fails()) {
                    return redirect()->back()->with(['error' => ucfirst($validator->errors()->first())])->withInput();
                }
                $customerGroup = CustomerGroup::find($request->customer_group_id);
                if (!$customerGroup) {
                    return redirect()->back()->with(['error' => MessageResponse::NOT_FOUND])->withInput();
                }

                $customerGroup->update($request->only(array_keys($rules)));
            } else {
                if ($validator->fails()) {
                    return redirect()->route('customers.group.create')->with(['error' => ucfirst($validator->errors()->first())])->withInput();
                }
                $customerGroup = CustomerGroup::create($request->only(array_keys($rules)));
            }

            $msg = $request->customer_group_id ? MessageResponse::UPDATE_SUCCESS : MessageResponse::ADD_SUCCESS;


            $url = url()->previous();

            return redirect()->to("$url?group_id={$customerGroup->id}")->with([
                'success' => $msg,
                'customerGroup' => $customerGroup,
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED]);
            // return redirect()->back()->with(['error' => $th->getMessage()]);
        }
    }
    public function showCustomerGroup($id)
    {
    }
    public function deleteCustomerGroup($id)
    {
        $customerGroup = CustomerGroup::find($id);

        if (!$customerGroup) {
            return response()->json([
                'code' => 404,
                'message' => MessageResponse::NOT_FOUND
            ], 404);
        }

        try {
            $customerGroup->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Customer Grup ' . MessageResponse::DELETE_SUCCESS
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => MessageResponse::FAILED_DELETE,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteCustomerPersonal($id)
    {
        $customerOutlet = CustomerOutlet::find($id);

        if (!$customerOutlet) {
            return response()->json([
                'code' => 404,
                'message' => MessageResponse::NOT_FOUND
            ], 404);
        }

        try {
            $customerOutlet->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Customer ' . MessageResponse::DELETE_SUCCESS
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => MessageResponse::FAILED_DELETE,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function storeOrUpdateCustomerCompany(Request $request)
    {
        try {

            $rules = ValidationRules::get('customer_companies');
            $messages = ValidationRules::messages('customer_companies');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->with(['error' => $validator->errors()->first(), 'show_modal' => true,])->withInput();
            }

            if ($request->customer_company_id) {
                $customerCompany = CustomerCompany::find($request->customer_company_id);
                if (!$customerCompany) {
                    return redirect()->back()->with(['error' => MessageResponse::NOT_FOUND]);
                }

                $customerCompany->update($request->only(array_keys($rules)));
            } else {
                $payload = array_merge(
                    $request->only(array_keys($rules)),
                    ['custom_price' => false]
                );

                $customerCompany = CustomerCompany::create($payload);
            }

            $msg = $request->customer_company_id ? MessageResponse::UPDATE_SUCCESS : MessageResponse::ADD_SUCCESS;

            return redirect()->back()->with(['success' => $msg]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED, 'show_modal' => true,]);
        }
    }
    // public function showCustomerCompany($id) {}
    public function deleteCustomerCompany($id)
    {
        $customerCompany = CustomerCompany::find($id);

        if (!$customerCompany) {
            return response()->json([
                'code' => 404,
                'message' => MessageResponse::NOT_FOUND
            ], 404);
        }

        try {
            $customerCompany->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Customer Perusahaan ' . MessageResponse::DELETE_SUCCESS
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => MessageResponse::FAILED_DELETE,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function storeOrUpdateCustomerOutlet(Request $request)
    {
        try {
            $prevUrl = explode('/', url()->previous());
            $from = '';
            if ($prevUrl[5]) {
                $from = $prevUrl[5];
            }

            $rules = ValidationRules::get('customer_outlets');
            $messages = ValidationRules::messages('customer_outlets');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->with(['error' => ucfirst($validator->errors()->first()), 'show_modal_outlet' => true])->withInput();
            }

            if ($request->customer_outlet_id) {

                if ($validator->fails()) {
                    return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED, 'show_modal_outlet' => true,])->withInput();
                }
                $customerOutlet = CustomerOutlet::find($request->customer_outlet_id);
                if (!$customerOutlet) {
                    return redirect()->back()->with(['error' => MessageResponse::NOT_FOUND]);
                }

                $customerOutlet->update($request->only(array_keys($rules)));
            } else {

                if ($validator->fails()) {
                    return redirect()->back()->with(['error' => MessageResponse::NOT_FOUND]);
                }
                $customerOutlet = CustomerOutlet::create($request->only(array_keys($rules)));
            }

            $msg = $request->customer_outlet_id ? MessageResponse::UPDATE_SUCCESS : MessageResponse::ADD_SUCCESS;
            if ($from == 'personal') {
                return redirect()->route('customers.index', ['tab' => $from])->with(['success' => $msg]);
            } else {
                return redirect()->back()->with(['success' => $msg]);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED, 'show_modal_outlet' => true,]);
        }
    }
    public function deleteCustomerOutlet($id)
    {
        $customerOutlet = CustomerOutlet::find($id);

        if (!$customerOutlet) {
            return response()->json([
                'code' => 404,
                'message' => MessageResponse::NOT_FOUND
            ], 404);
        }

        try {
            $customerOutlet->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Customer Outlet ' . MessageResponse::DELETE_SUCCESS
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => MessageResponse::FAILED_DELETE,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function insertInitCustomPrice(Request $request)
    {
        try {
            DB::beginTransaction();
            $outletId = $request->outlet_id;

            $exists = CustomerPrice::where('outlet_id', $outletId)->exists();

            if (!$exists) {
                $products = Product::select('id', 'base_price', 'selling_price')->get();
                $insertData = $products->map(function ($product) use ($outletId) {
                    return [
                        'outlet_id' => $outletId,
                        'product_id' => $product->id,
                        'base_price' => $product->base_price,
                        'selling_price' => $product->selling_price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                DB::table('customer_prices')->insert($insertData);
            }
            CustomerOutlet::where('id', $outletId)->update(['custom_price' => $request->custom_price]);
            $outlet = CustomerOutlet::where('id', $outletId)->first();

            DB::commit();
            return redirect()->to(url()->previous())->with(['outlet' => $outlet, 'show_modal_custom_price' => true, 'success' => MessageResponse::SYNC_SUCCESS]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return redirect()->to(url()->previous())->with(['error' => MessageResponse::ERROR_OCCURRED]);
        }
    }

    public function updateSellingPrice(Request $request)
    {
        try {
            $customerPrice = CustomerPrice::find($request->id);
            $outlet = CustomerOutlet::find($customerPrice->outlet_id);
            CustomerPrice::where('id', $request->id)->update(['selling_price' => $request->selling_price]);
            return redirect()->back()->with(['show_modal_custom_price' => true, 'outlet' => $outlet]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED]);
        }
    }
    public function restoreSellingPrice(Request $request)
    {
        try {
            $customerPrice = CustomerPrice::find($request->id);
            $product = Product::find($customerPrice->product_id);
            $outlet = CustomerOutlet::find($customerPrice->outlet_id);
            CustomerPrice::where('id', $request->id)->update(['selling_price' => $product->selling_price]);
            return redirect()->back()->with(['show_modal_custom_price' => true, 'outlet' => $outlet]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED]);
        }
    }

    public function syncProducts(Request $request)
    {
        $outletId = $request->outlet_id;

        $outlet = CustomerOutlet::find($outletId);

        $allProducts = Product::select('id', 'base_price', 'selling_price')->get()->keyBy('id');


        $existingProductIds = CustomerPrice::where('outlet_id', $outletId)
            ->pluck('product_id')
            ->toArray();


        $missingProductIds = array_diff($allProducts->keys()->toArray(), $existingProductIds);

        $dataToInsert = [];
        foreach ($missingProductIds as $productId) {
            $product = $allProducts[$productId];

            $dataToInsert[] = [
                'outlet_id' => $outletId,
                'product_id' => $productId,
                'base_price' => $product->base_price,
                'selling_price' => $product->selling_price,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($dataToInsert)) {
            DB::table('customer_prices')->insert($dataToInsert);
        }

        return redirect()->back()->with(['outlet' => $outlet, 'show_modal_custom_price' => true, 'success' => MessageResponse::SYNC_SUCCESS]);
    }

}
