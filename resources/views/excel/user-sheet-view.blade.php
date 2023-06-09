<table>
  <tr>
    <th>id</th>
    <th>name</th>
    <th>email</th>
    <th>role_id</th>
    <th>retirement_date</th>
    <th>status</th>
    <th>created_at</th>
  </tr>
  @foreach($data as $item)
    <tr>
      <td>{{$item->id}}</td>
      <td>{{$item->name}}</td>
      <td>{{$item->email}}</td>
      <td>{{$item->role_id}}</td>
      <td>{{$item->retirement_date ?? 'NULL'}}</td>
      <td>{{$item->status}}</td>
      <td>{{$item->created_at}}</td>
    </tr>
  @endforeach
</table>
