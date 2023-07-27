<table>
    <thead>
        <tr>
            <th style="width: 26px">Sun</th>
            <th style="width: 26px">Mon</th>
            <th style="width: 26px">Tue</th>
            <th style="width: 26px">Wed</th>
            <th style="width: 26px">Thu</th>
            <th style="width: 26px">Fri</th>
            <th style="width: 26px">Sat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($days->chunk(7) as $week)
         <tr>
            @foreach ($week as $day)
                <td style="height: 200px; width: 200px;"> 
                   {{ $day }}
                   <br> 
                    @foreach($datas as $k => $value)
                        @if ($value['start'] == $day)
                            {{$value['title']}}
                            <br>
                        @endif
                    @endforeach
                </td>
            @endforeach
            </tr>
        @endforeach
    </tbody>
</table>