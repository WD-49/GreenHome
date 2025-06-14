<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        // Đếm cho tabs
        $paymentAll = PaymentMethod::withTrashed()->get();
        $paymentActive = PaymentMethod::where('status', 1)->get();
        $paymentInactive = PaymentMethod::where('status', 0)->get();
        $paymentTrashed = PaymentMethod::onlyTrashed()->get();

        // Query filter
        $query = PaymentMethod::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('status', 1);
            }
            if ($request->status == 'inactive') {
                $query->where('status', 0);
            }
        }

        $paymentMethods = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.payment_methods.index', [
            'paymentMethods' => $paymentMethods,
            'paymentAll' => $paymentAll,
            'paymentActive' => $paymentActive,
            'paymentInactive' => $paymentInactive,
            'paymentTrashed' => $paymentTrashed,
        ]);
    }

    public function create()
    {
        return view('admin.payment_methods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name.*' => 'required|string|max:255',
            'description.*' => 'nullable|string',
            'status.*' => 'required|in:0,1',
        ]);

        foreach ($data['name'] as $index => $name) {
            PaymentMethod::create([
                'name' => $name,
                'description' => $data['description'][$index] ?? '',
                'status' => $data['status'][$index],
            ]);
        }

        return redirect()->route('admin.paymentMethods.index')->with('success', 'Thêm phương thức thành công!');
    }


    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return view('admin.payment_methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $paymentMethod->update($validated);

        return redirect()->route('admin.paymentMethods.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();
        return redirect()->route('admin.paymentMethods.index')->with('success', 'Đã chuyển vào thùng rác!');
    }

    public function trash(Request $request)
    {
        // Đếm tabs cho thùng rác
        $paymentAll = PaymentMethod::withTrashed()->get();
        $paymentActive = PaymentMethod::where('status', 1)->get();
        $paymentInactive = PaymentMethod::where('status', 0)->get();
        $paymentTrashed = PaymentMethod::onlyTrashed()->get();

        $query = PaymentMethod::onlyTrashed();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('status', 1);
            }
            if ($request->status == 'inactive') {
                $query->where('status', 0);
            }
        }

        $paymentMethods = $query->orderBy('deleted_at', 'desc')->paginate(10);

        // Dùng lại view index cho đồng bộ tabs, table, filter
        return view('admin.payment_methods.trash', [
            'paymentMethods' => $paymentMethods,
            'methodAll' => $paymentAll,
            'methodActive' => $paymentActive,
            'methodInactive' => $paymentInactive,
            'methodTrashed' => $paymentTrashed,
        ]);
    }

    public function restore($id)
    {
        $paymentMethod = PaymentMethod::onlyTrashed()->findOrFail($id);
        $paymentMethod->restore();
        return redirect()->route('admin.paymentMethods.trash')->with('success', 'Khôi phục thành công!');
    }

    public function forceDelete($id)
    {
        $paymentMethod = PaymentMethod::onlyTrashed()->findOrFail($id);
        $paymentMethod->forceDelete();
        return redirect()->route('admin.paymentMethods.trash')->with('success', 'Xóa vĩnh viễn thành công!');
    }
    public function show($id)
    {
        $paymentMethod = PaymentMethod::withTrashed()->findOrFail($id);
        return view('admin.payment_methods.show', compact('paymentMethod'));
    }
}
