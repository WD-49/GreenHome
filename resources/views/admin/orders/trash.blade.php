@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">🗑️ Thùng rác đơn hàng</h1>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-6 py-3 border">STT</th>
                        <th class="px-6 py-3 border">Mã đơn hàng</th>
                        <th class="px-6 py-3 border">Tên người đặt</th>
                        <th class="px-6 py-3 border">Ngày xóa</th>
                        <th class="px-6 py-3 border text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $index => $order)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-blue-600">#{{ $order->id }}</td>
                            <td class="px-6 py-4">{{ $order->shipping_name }}</td>
                            <td class="px-6 py-4">{{ $order->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    <button onclick="return confirm('Khôi phục đơn hàng này?');"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold px-4 py-2 rounded">
                                        Khôi phục
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center px-6 py-4 text-blue-500">Không có đơn hàng nào trong thùng
                                rác.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.orders.index') }}"
                class="inline-block bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded shadow">
                ← Quay lại danh sách đơn hàng
            </a>
        </div>
    </div>
@endsection
