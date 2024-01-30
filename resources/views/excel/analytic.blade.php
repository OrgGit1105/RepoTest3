<table>
    <thead>
        <tr>
            <th style="text-align: center;font-weight: 500;">Employee name</th>
            <th style="text-align: center;font-weight: 500;">Work Day</th>
            <th style="text-align: center;font-weight: 500;">Late Day</th>
            <th style="text-align: center;font-weight: 500;">Remote Work</th>
            <th style="text-align: center;font-weight: 500;">Day Off</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
            <tr>
                <td style="width: 150px;">{{$value['user_name']}}</td>
                <td style="width: 150px;">{{$value['work_day']}}</td>
                <td style="width: 150px;">{{$value['late_day']}}</td>
                <td style="width: 150px;">{{$value['remote_day']}}</td>
                <td style="width: 150px;">{{$value['off_day']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
