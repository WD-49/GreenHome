<table class="table table-striped w-100 nowrap">
    <thead>
        <tr>
            <th>#</th>
            <th>Người bình luận</th>
            <th>Nội dung</th>
            <th>Trạng thái</th>
            <th>Ngày bình luận</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($comments as $index => $comment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $comment->user->name }}</td>
                <td>{{ $comment->content }}</td>
                <td>
                    <span
                        class="badge {{ $comment->status == 'hiển thị' ? 'bg-success' : ($comment->status == 'ẩn' ? 'bg-secondary' : 'bg-warning') }}">
                        {{ $comment->status }}
                    </span>
                </td>
                <td>{{ $comment->created_at->format('d/m/Y') }}</td>
                <td>
                    {{-- <div class="dropdown">
                        <button class="btn btn-light btn-sm me-2" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <span class="mdi mdi-settings-helper"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">

                        </ul>
                    </div> --}}
                </td>
            </tr>
        @endforeach
        @if ($comments->count() == 0)
            <tr>
                <td colspan="8" class="text-center text-muted">Không có bình luận
                </td>
            </tr>
        @endif
    </tbody>
</table>
