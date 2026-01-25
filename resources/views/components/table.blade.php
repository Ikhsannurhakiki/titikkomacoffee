@props(['headers'])

<div class="bg-white rounded-2xl border border-primary overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead class="bg-secondary text-white text-[11px] uppercase font-black tracking-wider">
            <tr>
                @foreach ($headers as $header)
                    <th class="p-4">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            {{ $slot }}
        </tbody>
    </table>

    @if (isset($pagination))
        <div class="p-4 bg-white border-t border-gray-100">
            {{ $pagination }}
        </div>
    @endif
</div>
