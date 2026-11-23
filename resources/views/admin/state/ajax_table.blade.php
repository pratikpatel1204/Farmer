<div class="table-responsive">
    <table class="table table-bordered" id="StatesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($states as $key => $item)
                <tr id="row_{{ $item->id }}">
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <button class="btn btn-sm btn-info editStateBtn" data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteStateBtn" data-id="{{ $item->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>