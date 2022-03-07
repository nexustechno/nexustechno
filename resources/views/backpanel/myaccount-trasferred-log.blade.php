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
				@include('backpanel/account-menu-sidebar')
			</div>
			<div class="col-lg-9 col-md-9 col-sm-12">
				<div class="pagetitle text-color-blue-2">
					<h1> Transferred Log </h1>
				</div>
				<div class="white-bg mt-20 acc-statement">
					<table class="table custom-table white-bg text-color-blue-2">
	                    <tbody>
	                        <tr>
	                            <th class="light-grey-bg">Date/Time</th>
	                            <th class="light-grey-bg">Before Settlement</th>
	                            <th class="light-grey-bg">Settled Amount</th>
	                            <th class="light-grey-bg">After Settlement</th>
	                            <th class="light-grey-bg">Remark</th>
	                            <th class="light-grey-bg">From/To</th>
	                        </tr>
							<tr>
								<td colspan="6"> No Data </td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
    </div>
</section>
@endsection