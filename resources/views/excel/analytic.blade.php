<table>
    <thead>
        <tr>
            <th>Employee name</th>
            <th>Work Day</th>
            <th>Remote Work</th>
            <th>Day Off</th>
        </tr>  
    </thead>
    <tbody>
        @foreach($data as $key => $value)
            <tr>
                <td style="width: 150px;">{{$value['user_name']}}</td>
                <td style="width: 150px;">{{$value['work_day']}}</td>
                <td style="width: 150px;">{{$value['remote_day']}}</td>
                <td style="width: 150px;">{{$value['off_day']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>