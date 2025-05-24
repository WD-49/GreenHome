@extends('layouts.admin')

@section('content')
    <h1>Thùng rác admin</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.account.listAdmins') }}" class="btn btn-primary mb-3">Quay lại danh sách</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Trạng thái</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trashedAdmins as $key => $user)
                <tr>
                    <td>{{ $trashedAdmins->firstItem() + $key }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->status == 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}</td>
                    <td>
                        <form action="{{ route('admin.account.restoreUser', $user->id) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            <button onclick="return confirm('Khôi phục admin này?');" type="submit"
                                class="btn btn-success btn-sm">Khôi phục</button>
                        </form>

                        <form action="{{ route('admin.account.forceDeleteUser', $user->id) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa vĩnh viễn người dùng này?');" type="submit"
                                class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không có người dùng nào trong thùng rác.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $trashedAdmins->links() }}
    </div>
@endsection
