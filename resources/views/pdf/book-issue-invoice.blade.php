@extends('layouts.pdf-layout')
@section('content')
<table class="table table-bordered table-stripped">
	<thead>
		<tr class="info">
			<td>{{ Html::image($data['schoolInfo']['logo'], '', ['style' => 'width: 100px;']) }}</td>
			<td colspan="2"><center><h2>{{ $data['schoolInfo']['name'].', '.$data['schoolInfo']['address']}}</h2>
				<br> Mobile : {{$data['schoolInfo']['phone'].', Email : '.$data['schoolInfo']['email'] }}</center></td>
		</tr>
		<tr class="active">
			<th>Name</th>
			<th>Class</th>
			<th>Book name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{{ $data['studentInfo']->name }}</td>
			<td>{{ 'Std - '.Helper::integerToRoman($data['studentInfo']->admission_class) }}</td>
			<td>{{ $data['getBookIssueDetails']['raw_book_name']}}</td>
		</tr>
	</tbody>
	<thead>
		<tr class="active">
			<th>Issue Date</th>
			<th>Return Date</th>
			<th>Returning Date</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{{ Helper::changeDateFormat('d-m-Y', $data['getBookIssueDetails']['from_date']) }}</td>
			<td>{{ Helper::changeDateFormat('d-m-Y', $data['getBookIssueDetails']['to_date']) }}</td>
			<td>{{ isset($data['getBookIssueDetails']['return_date']) ? Helper::changeDateFormat('d-m-Y', $data['getBookIssueDetails']['return_date']) : 'Not Returned Yet!' }}</td>
		</tr>
	</tbody>
	<thead>
		<tr class="active">
			<th>Total Due Days</th>
			<th>Total Fine (in Rupee)</th>
			<th>Is Fine Paid/ Paid Date</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{{ $dateDiff = Helper::dateDiff($data['getBookIssueDetails']['to_date'], date('Y-m-d')) }}</td>
			<td>
				@php 
				setlocale(LC_MONETARY, 'en_IN');
				$amount = (float)$dateDiff*15;
				@endphp 
				{{ 'Rs. '.Helper::moneyFormatIndia($amount)  }} <br/> {{ strtoupper(Helper::displaywords((float)$dateDiff*15)) }}</td>
			<td>
				@if(isset($data['getBookIssueDetails']['is_paid']) && $data['getBookIssueDetails']['is_paid'] == 1)
					<span class="text-success">Paid on - {{ Helper::changeDateFormat('d M Y h:i:s', $data['getBookIssueDetails']['paid_date']) }}</span>
				@else
					<span class="text-danger">Not Paid</span>
				@endif	
			</td>
		</tr>
	</tbody>
</table>
@endsection