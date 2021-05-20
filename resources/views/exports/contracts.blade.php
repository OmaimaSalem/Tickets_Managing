<table>
    <thead>
        <tr>
            <th>name</th>
            <th>email</th>
            <th>company</th>
            <th>address</th>
            <th>zipcode</th>
            <th>city</th>
            <th>country</th>
            <th>issued at</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $key => $item)
        <tr>
            <td align="left" valign="top">{{ $item->user->metadata->first_name." ".$item->user->metadata->last_name }}</td>
            <td align="left" valign="top">{{ $item->user->email }}</td>
            <td align="left" valign="top">{{ $item->user->metadata->company }}</td>
            <td align="left" valign="top">{{ $item->user->metadata->address }}</td>
            <td align="left" valign="top">{{ $item->user->metadata->postal_code }}</td>
            <td align="left" valign="top">{{ $item->user->metadata->state }}</td>
            <td align="left" valign="top">{{ $item->user->metadata->country }}</td>
            <td align="left" valign="top">{{ $item->created_at }}</td>
        </tr>
        @if(count($item->items) > 0)
        <tr align="left" valign="top">

            <td colspan="8">
            <table>
            <thead>
                <tr>
                    <th>Title</th>                    
                    <th>unite price</th>                    
                    <th>Amount</th>                    
                    <th>total price</th>
                    <th>vat</th>
                    <th>net</th>
                    <th>discount</th>
                    <th>total gross</th>
                </tr>
            </thead>
                <tbody>
                    @foreach ($item->items as $contract_item)
                    <tr>
                        <th>{{$contract_item->item}}</th>                    
                        <th>{{$contract_item->item_price}}</th>                    
                        <th>{{$contract_item->item_count}}</th>                    
                        <th>{{$contract_item->item_total_price}}</th>
                        <th>{{$contract_item->item_vat}}</th>
                        <th>{{$contract_item->item_net}}</th>
                        <th>{{$contract_item->item_discount}}</th>
                        <th>{{$contract_item->item_total}}</th>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </td>   
        </tr>
        @endif

        @endforeach
    </tbody>
</table>
