<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\faq\StoreFaqRequest as FaqStoreFaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;
class FaqController extends Controller
{
    public function index() {
        $faqs = Faq::paginate(10);
        return view('admin.faq.index', compact('faqs'));
    }
    public function create() {
        return view('admin.faq.create');
    }
    public function store(FaqStoreFaqRequest $request) {
        Faq::create($request->validated());
        return redirect()->route('admin.faqs.index')
        ->with('success', 'Thêm FAQ thành công!');
    }
    public function edit($id) {
        $faq = Faq::findOrFail($id);
        return view('admin.faq.edit', compact('faq'));
    }
    public function destroy(Request $request) {
        $id = $request->input('id');
        $faq = Faq::findOrFail($id);
        $faq->delete();
        return redirect()->route('admin.faqs.index')
        ->with('success', 'Câu hỏi đã được xóa thành công!');
    }
}
