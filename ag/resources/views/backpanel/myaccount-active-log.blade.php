@extends('layouts.app')
@section('content')
<section>
	<div class="container">
    	<div class="breadcrumbs">
        	<ul>
            	<li> <a href="#" class="text-color-black" > <span class="red-bg text-color-white">{{$user->agent_level}}</span> {{$user->user_name}} </a> </li>
            </ul>
    	</div>
    </div>
</section>
<section class="myaccount-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-12 pl-0">
				@include('backpanel.account-menu')
			</div>
			<div class="col-lg-9 col-md-9 col-sm-12 pr-md-0">
				<div class="pagetitle text-color-blue-2">
					<h1> Activity Log </h1>
				</div>
				<div class="white-bg mt-20 acc-statement">
					<table class="table custom-table white-bg text-color-blue-2">
	                    <tbody>
	                        <tr>
	                            <th class="light-grey-bg">Login Date & Time</th>
	                            <th class="light-grey-bg">Login Status</th>
	                            <th class="light-grey-bg">IP Address</th>
	                            <th class="light-grey-bg">ISP</th>
	                            <th class="light-grey-bg">City/State/Country</th>
	                        </tr>
							<tr>
								<td> <?php echo date("Y-m-d") ." " . date("h:i:s") ?></td>
								<td class="text-color-green"> Login Success </td>
								<td> 103.88.56.122 </td>
								<td> <!-- www.sportscasinoapi.com  --></td>
								<td> India </td>
							</tr>
							
						</tbody>
					</table>
				</div>
                <div class="pagination-wrap light-grey-bg-1">
                    <ul class="pages">
                        <li id="prev"><a class="disable disable-bg disable-color">Prev</a></li>
                        <li id="pageNumber"><a class="active text-color-yellow">1</a></li>
						<li id="pageNumber"><a>2</a></li>
                        <li id="next"><a class="">Next</a></li>
                    </ul>
                </div>
			</div>
		</div>
    </div>
</section>
@endsection