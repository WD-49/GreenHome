{{-- Trang giúp tải lại thông tin bình luận --}}
@php
    // $comment và $statusData được truyền từ CommentController@generateActiveCommentRowHtml
    $statusText = $statusData['status_text'] ?? 'N/A';
    $statusClassBadge = $statusData['status_class_badge'] ?? 'bg-light text-dark';
    $currentActionsHtml = $statusData['actions_html'] ?? '';
@endphp

<tr id="active-comment-row-{{ $comment->id }}">
    <td>{{ $comment->id }}</td>
    <td>{{ Str::limit($comment->content, 70) }}</td>
    <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
    <td class="comment-status-cell" id="comment-status-cell-{{ $comment->id }}">
        <span class="badge {{ $statusClassBadge }}">{{ $statusText }}</span>
    </td>
    <td class="comment-actions-cell" id="comment-actions-cell-{{ $comment->id }}">
        {!! $currentActionsHtml !!}
    </td>
</tr>
{{-- Hàng chi tiết bình luận --}}
<tr class="comment-detail-row" id="comment-detail-row-{{ $comment->id }}" style="display: none;">
    <td colspan="5">
        <div class="comment-detail-content p-2 border-top" id="comment-detail-content-{{ $comment->id }}">
            <p class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Đang tải chi tiết...</p>
        </div>
    </td>
</tr>
