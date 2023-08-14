<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 14px;text-align: center;height: 30px;font-weight: 500;">{{$user_name}}</th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Happy</th>
            <th>Sad</th>
            <th>Angry</th>
            <th>Confused</th>
            <th>Disgusted</th>
            <th>Surprised</th>
            <th>Calm</th>
            <th>Fear</th>
        </tr>  
    </thead>
    <tbody>
        @foreach($data as $key => $value)
            <tr>
                <td style="width: 150px;">{{\Carbon\Carbon::parse($value['time'])->format('Y-m-d')}} </td>
                <td style="width: 150px;">{{$value['happy']}}</td>
                <td style="width: 150px;">{{$value['sad']}}</td>
                <td style="width: 150px;">{{$value['angry']}}</td>
                <td style="width: 150px;">{{$value['confused']}}</td>
                <td style="width: 150px;">{{$value['disgusted']}}</td>
                <td style="width: 150px;">{{$value['surprised']}}</td>
                <td style="width: 150px;">{{$value['calm']}}</td>
                <td style="width: 150px;">{{$value['fear']}}</td>

            </tr>
        @endforeach
    </tbody>
</table>