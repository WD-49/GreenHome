$(document).ready(function () {
    const filterForm = document.getElementById('filter-form');
    const tableWrapper = $('#ajax-table');
    const tableContent = $('#table-content');
    $(document).on('click', '#ajax-table .pagination a', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        fetchTableData(url);
    });

    function fetchTableData(url, queryString = null) {
        $.ajax({
            url: url + (queryString ? ((url.indexOf('?') !== -1 ? '&' : '?') + queryString) : ''),
            type: 'GET',
            beforeSend: function () {
                tableContent.html('<div class="text-center py-5">Đang tải dữ liệu...</div>');
            },
            success: function (data) {
                tableContent.html(data);
                // Cập nhật URL trên trình duyệt
                let newUrl = url + (queryString ? ((url.indexOf('?') !== -1 ? '&' : '?') + queryString) : '');
                window.history.pushState({}, '', newUrl);
            },
            error: function () {
                tableContent.html('<div class="text-danger">Tải dữ liệu thất bại!</div>');
            }
        });
    }
    // Bắt sự kiện submit form filter
    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const url = filterForm.getAttribute('action');
        let formData = $(filterForm).serializeArray();
        // Lấy giá trị per_page hiện tại từ select (nếu có)
        const perPage = $('#perPage').val();
        if (perPage) {
            // Xóa per_page cũ nếu có
            formData = formData.filter(item => item.name !== 'per_page');
            formData.push({ name: 'per_page', value: perPage });
        }
        fetchTableData(url, $.param(formData));
    });
    filterForm.addEventListener('reset', function (e) {
        e.preventDefault();
        // Reset tất cả input, select, textarea về mặc định
        setTimeout(function () {
            $('#filter-form').find('input[type="text"], input[type="date"], select').val('');
            $('#filter-form').find('select').prop('selectedIndex', 0);
            // Nếu có select2 hoặc plugin khác thì cần trigger('change') nữa

            // Gửi lại AJAX với chỉ per_page
            const url = filterForm.getAttribute('action');
            const perPage = $('#perPage').val();
            let params = perPage ? 'per_page=' + perPage : '';
            fetchTableData(url, params);
        }, 0);
    });

    // function setupAjaxPagination(wrapperId, contentId) {
    //     $(document).on('click', `${wrapperId} .pagination a`, function (e) {
    //         e.preventDefault();
    //         const url = $(this).attr('href');
    //         fetchTabData(url, wrapperId, contentId);
    //     });
    // }

    // setupAjaxPagination('#ajax-comments', '#comment-table-content');
    // setupAjaxPagination('#ajax-reviews', '#review-table-content');
    // setupAjaxPagination('#ajax-variants', '#variant-table-content');

    const perPageSelect = document.getElementById('perPage');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            let url = $('#filter-form').attr('action');
            let formData = $('#filter-form').serializeArray();
            formData.push({ name: 'per_page', value: this.value });
            // Xóa page nếu có
            formData = formData.filter(item => item.name !== 'page');
            $.ajax({
                url: url,
                type: 'GET',
                data: $.param(formData),
                beforeSend: function () {
                    $('#table-content').html('<div class="text-center py-5">Đang tải dữ liệu...</div>');
                },
                success: function (data) {
                    $('#table-content').html(data);
                    // Cập nhật URL
                    let newUrl = url + '?' + $.param(formData);
                    window.history.pushState({}, '', newUrl);
                },
                error: function () {
                    $('#table-content').html('<div class="text-danger">Tải dữ liệu thất bại!</div>');
                }
            });
        });
    }

});
