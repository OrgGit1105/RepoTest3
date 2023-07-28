<?php
    $style_default  = "vertical-align: center; width: 26px; text-align: center; height: 50px; font-size: 16px; font-weight: 500;";
?>
<table>
    <thead>
        <tr>
            <th colspan="7" style="{{$style_default}}">{{$year_month}}</th>
        </tr>
        <tr>
            <th style="{{$style_default}}">Sun</th>
            <th style="{{$style_default}}">Mon</th>
            <th style="{{$style_default}}">Tue</th>
            <th style="{{$style_default}}">Wed</th>
            <th style="{{$style_default}}">Thu</th>
            <th style="{{$style_default}}">Fri</th>
            <th style="{{$style_default}}">Sat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($days->chunk(7) as $week)
        <tr>
            @foreach ($week as $day)
            <td style="height: 200px; width: 200px; vertical-align: top;">
                <p style="vertical-align: right;">{{ $day }}</p>
                <br>
                @foreach($datas as $k => $value)
                @if ($value['start'] == $day)
                <span>{{$value['title']}}</span>
                <br>
                @endif
                @endforeach
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>