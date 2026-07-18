<div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">ID</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Name</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            @forelse($items as $item)
                @php
                    $idVal = $item->{$idCol};
                    $nameVal = $item->{$nameCol};
                @endphp
                <tr x-data="{ editing: false, name: '{{ addslashes($nameVal) }}' }">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $idVal }}</td>
                    <td class="px-4 py-3">
                        <span x-show="!editing" class="font-bold text-gray-900 dark:text-white">{{ $nameVal }}</span>
                        <input x-show="editing" x-model="name" form="edit-form-{{ $type }}-{{ $idVal }}" type="text" name="{{ $nameCol }}" class="text-xs rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </td>
                    <td class="px-4 py-3 text-right">
                        <form id="edit-form-{{ $type }}-{{ $idVal }}" action="{{ route('admin.reference-data.update', ['type' => $type, 'id' => $idVal]) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                        </form>
                        <button x-show="!editing" @click="editing = true" type="button" class="text-xs text-blue-600 hover:text-blue-900 font-bold mr-3">Edit</button>
                        <button x-show="editing" form="edit-form-{{ $type }}-{{ $idVal }}" type="submit" class="text-xs text-green-600 hover:text-green-900 font-bold mr-2">Save</button>
                        <button x-show="editing" @click="editing = false" type="button" class="text-xs text-gray-500 hover:text-gray-700 font-bold mr-3">Cancel</button>

                        <form action="{{ route('admin.reference-data.destroy', ['type' => $type, 'id' => $idVal]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-900 font-bold">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-gray-400 italic">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
