<table class="table table-striped w-100 nowrap">
    <thead>
        <tr>
            <th>#</th>
            <th>user</th>
            <th>variant-sku</th>
            <th>rating</th>
            <th>title</th>
            <th>status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reviews as $index => $review)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $review->user->name }}</td>

                <td>{{ $review->ProductVariant->sku }}</td>

                {{-- Hiển thị rating bằng sao --}}
                <td>
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $review->rating)
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                </td>

                <td>{{ $review->title }}</td>

                {{-- Hiển thị trạng thái với màu sắc --}}
                <td>
                    @php
                        switch ($review->status) {
                            case 'pending':
                                $badgeClass = 'bg-warning';
                                $statusText = 'Chưa duyệt';
                                break;
                            case 'approved':
                                $badgeClass = 'bg-success';
                                $statusText = 'Đã duyệt';
                                break;
                            case 'rejected':
                                $badgeClass = 'bg-danger';
                                $statusText = 'Ẩn';
                                break;
                            default:
                                $badgeClass = 'bg-secondary';
                                $statusText = 'Không rõ';
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </td>

                <td class="text-end">
                    {{-- Các nút thao tác có thể được mở lại nếu cần --}}
                </td>
            </tr>
        @endforeach

        @if ($reviews->count() == 0)
            <tr>
                <td colspan="8" class="text-center text-muted">Không có đánh giá nào</td>
            </tr>
        @endif
    </tbody>
</table>
