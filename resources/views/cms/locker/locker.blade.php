@extends ( 'cms.layout.layout' )

@section ( 'content' )
    <div class="row clearfix" id="locker">
        @foreach ($carts as $cart)
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h4>Cart {{ $cart->cart_desc }}</h4>      
                            </div>
                        </div>
                    </div>
                    <div class="body table-responsive">
                        <table class="table table-bordered-locker" style="font-size: 1rem;">
                            <tbody>                    
                                @if ($cart->slot_amount == 28)
                                    @for ($i = 0; $i < 14; $i++)
                                        @php
                                            $slotA = $i;
                                            $slotB = $i + 14;
                                        @endphp
                                        <tr>
                                            <td>
                                                <center><div style="background:@php if($cart->lockers[$slotA]->connection_status == TRUE ? $color = 'green' : $color = '#ccc'); echo $color; @endphp;width:16px;height:16px;border-radius:8px"></div></center>
                                            </td>
                                            <td>@php echo $cart->lockers[$slotA]->abs_slotindex; @endphp</td>
                                            <td>@php echo $cart->lockers[$slotA]->Name; @endphp</td>
                                            <td><a href="/cms/students/@php echo $cart->lockers[$slotA]->userid @endphp/view">@php echo $cart->lockers[$slotA]->fullname @endphp</a></td>
                                            <td>
                                                <center><div style="background:@php if($cart->lockers[$slotB]->connection_status == TRUE ? $color = 'green' : $color = '#ccc'); echo $color; @endphp;width:16px;height:16px;border-radius:8px"></div></center>
                                            </td>
                                            <td>@php echo $cart->lockers[$slotB]->abs_slotindex; @endphp</td>
                                            <td>@php echo $cart->lockers[$slotB]->Name; @endphp</td>
                                            <td><a href="/cms/students/@php echo $cart->lockers[$slotB]->userid @endphp/view">@php echo $cart->lockers[$slotB]->fullname @endphp</a></td>
                                        </tr>
                                    @endfor
                                @else
                                    @foreach($cart->lockers as $locker)
                                        <tr>
                                            <td>
                                                <center><div style="background:{{ $locker->connection_status == TRUE ? 'green' : '#ccc' }};width:16px;height:16px;border-radius:8px"></div></center>
                                            </td>
                                            <td>{{ $locker->abs_slotindex }}</td>
                                            <td>{{ $locker->Name }}</td>
                                            <td><a href="/cms/students/{{ $locker->userid }}/view">{{ $locker->fullname }}</a></td>
                                        </tr>
                                    @endforeach  
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection