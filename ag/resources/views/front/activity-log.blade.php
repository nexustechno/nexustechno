@extends('layouts.front_layout')
@section('content')
<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            @include('front.leftpanel-account')
            <div class="dashboard-right-pannel">
                <div class="pagetitle text-color-blue-2">
                    <h1>Activity Log</h1>
                </div>
                <div class="summery-table mt-3">
                    <table class="table custom-table">
                        <thead>
                            <tr class="light-grey-bg">
                                <th>Login Date & Time</th>
                                <th>Login Status</th>
                                <th class="text-right">IP Address</th>
                                <th class="text-right w-25">ISP</th>
                                <th class="text-right w-15">City/State/Country</th>          
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="white-bg">
                                <td><?php echo date("Y-m-d") ." " . date("h:i:s") ?></td>
                                <td class="text-color-green">{{ strtoupper(trans($user->status)) }}</td>
                                <td class="text-right">{{$user->ip_address}}</td>
                                <td class="text-right">INDIA</td>
                                <td class="text-right">INDIA</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('layouts.footer')
@endsection