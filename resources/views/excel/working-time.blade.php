<table>
    <thead>
        <tr>
            <th></th>
            <th style="font-size: 14px;text-align: center;height: 30px;font-weight: 500;">Type</th>
            <th style="font-size: 14px;text-align: center;height: 30px;font-weight: 500;">Employee Name</th>
            <th style="font-size: 14px;text-align: center;height: 30px;font-weight: 500;">Date</th>
            <th style="font-size: 14px;text-align: center;height: 30px;font-weight: 500;">IN</th>
            <th style="font-size: 14px;text-align: center;height: 30px;font-weight: 500;">OUT</th>
            <th style="font-size: 14px;text-align: center;height: 30px;font-weight: 500;">Input Type</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $value)
            <tr>
                <td style="width: 150px;">{{$value['warning']}}</td>
                <td style="width: 150px;">{{$value['type_date']}}</td>
                <td style="width: 150px;">{{$value['user_name']}}</td>
                <td style="width: 150px;">{{$value['date']}}</td>
                <td style="width: 150px;">{{$value['in_time']}}</td>
                <td style="width: 150px;">{{$value['out_time']}}</td>
                <td style="width: 150px;">{{$value['registration_type']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>