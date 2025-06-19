<table class="table table-bordered align-middle mb-0">
    <thead class="table-primary text-center">
        <tr>
            <th>ID</th>
            <th>Tên phương thức</th>
            <th>Mô tả</th>
            <th>Trạng thái</th>
            <th class="text-center">...</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($paymentMethods as $method)
            <tr>
                <td>{{ $method->id }}</td>
                <td>{{ $method->name }}</td>
                <td>{!! $method->description !!}</td>
                <td>
                    <span class="badge {{ $method->status ? 'bg-success' : 'bg-secondary' }}">
                        {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                            <i class="mdi mdi-dots-horizontal"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 px-2 py-2">
                            <li>
                                <a class="dropdown-item text-primary" href="{{ route('admin.paymentMethods.show', $method->id) }}">
                                    <i class="mdi mdi-eye-outline"></i> Xem
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-warning" href="{{ route('admin.paymentMethods.edit', $method->id) }}">
                                    <i class="mdi mdi-pencil-outline"></i> Sửa
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('admin.paymentMethods.destroy', $method->id) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa phương thức này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="mdi mdi-trash-can-outline"></i> Xóa
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-danger">Không có phương thức nào.</td>
            </tr>
        @endforelse
    </tbody>
</table>
