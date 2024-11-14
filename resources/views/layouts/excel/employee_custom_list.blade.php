<table style="direction: rtl">
    <thead>
    <tr>
        @foreach($titles as $title)
            <th style="color: #ffffff;background-color: #343A40">{{$title["name"]}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @forelse($employees as $employee)
        <tr>
            @foreach($titles as $title)
                <td>{{$employee[$title["data"]]}}</td>
            @endforeach
        </tr>
    @empty
    @endforelse
    </tbody>
</table>
