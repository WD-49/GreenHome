<div id="ajax-table" data-url="{{ $url ?? request()->url() }}">
    <div id="table-content">
        {{ $slot }}
    </div>
</div>
