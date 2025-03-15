<div wire:poll.5s>
    {{-- <button wire:click="loadData">Refresh Data</button> --}}
    <table class="table table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categoryIndustries as $index => $category)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $category->category }}</td>
                    <td>{{ $category->count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
