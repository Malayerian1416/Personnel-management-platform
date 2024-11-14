<table style="direction: rtl">
    <thead>
    <tr>
        @forelse($template as $column)
            @if($column["ignore"])
                <th style="color: #ffffff;background-color: #861616">
                    --
                </th>
            @else
                <th style="color: #ffffff;background-color: #343A40">
                    {{$column["title"]}}
                </th>
            @endif
        @empty
        @endforelse
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
