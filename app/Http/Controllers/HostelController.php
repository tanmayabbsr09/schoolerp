<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Helper;
use App\Hostel;
use App\HostelRoom;
use App\HostelAllot;

class HostelController extends Controller
{
    /* ##########################  Voucher MANAGEMENT ############################*/
	/*
		info : Voucher Master
		Date ; 14-12-2017
    */
	public function hostelRoomAdd(Request $request, $eid=null)
	{
		$data[] = array();
		
		$getHostels = array('' => '-- Slect --');
		$getHostels += Hostel::pluck('hos_name', 'id')->toArray();
		/* Field specifiic Validations */	

		$getAmenities = array('WIFI' => 'WIFI', 'LANDLINE/MOBILE' => 'LANDLINE/MOBILE', 'AC' => 'AC', 'TV' => 'TV', 'FRIDGE' => 'FRIDGE');


        $validationRules = [
            'hostels_id' => 'required',
            'room_no' => 'required',
            'floor_no' => 'required',
            'total_beds' => 'required',
            'avail_beds' => 'required',
            'price_per_bed' => 'required'
        ];

    	$validator = Validator::make($request->all(), $validationRules);
       	if(isset($eid) && $eid != null){
    		$getFormAutoFillup = Voucher::whereId($eid)->first()->toArray();
    		$admission_class = $getFormAutoFillup['admission_class'];
    	}else{
    		$getFormAutoFillup = array();
    	}
		/* Submit all data */
    	if ($request->isMethod('post')){
    		if($validator->fails()){
            	return redirect('/hostel-room')->withErrors($validator)->withInput();
            }else{
            	if(isset($request->id) && $request->id != null){
		    		/* 
						Update
		    		*/
		    		$updateStudent = Voucher::find($request->id);
		    		if(isset($request->_token) && $request->_token != ''){
		    			unset($request['_token']);
		    		}
		    		/* Calculation of total Amount on the GST and Discount */
		    		$totalPrice = $request->stock * $request->price;
		    		$discountInPrice = isset($request->discount) ? $totalPrice*($request->discount/100) : 0;
		    		$total = ($totalPrice + ($totalPrice*($request->gst/100)))-$discountInPrice;
		    		$request->request->add(['total' => $total]);

		    		//dd($request->toArray());
		    		if(Voucher::where([['id', '=', $request->id]])->update($request->toArray())){
		    			$request->session()->flash('message.level', 'info');
		    			$request->session()->flash('message.content', 'Voucher Details are Updated Successfully !');
		    		}else{
		        		session()->flash('status', ['danger', 'Addition was Failed!']);
		        	}
		        	return redirect('/voucher-edit/'.$request->id);
		    	}else{
		    		$saveData = $request->toArray();
		    		/* Calculation of total Amount on the GST and Discount */
		    		$saveData['amenities'] = json_encode($request->amenities);
		    		$saveData = new HostelRoom($saveData);

		    		if($saveData->save()){
		        		$request->session()->flash('message.level', 'success');
		    			$request->session()->flash('message.content', 'Hostel Room was successfully added!');
		        	}else{
		        		session()->flash('status', ['danger', 'Updation of Hostel Room Failed!']);
		        	}
		        	return redirect('/hostel-room');
		        }
            }
			
    	}
    	
		$customFields['basic'] = array(
			'hostels_id'=>array('type' => 'select', 'label'=>'Choose a Hostel', 'value' => $getHostels, 'mandatory'=>true, 'class' => 'hostels_id'),
			'room_no'=>array('type' => 'text', 'label'=>'Room Number', 'mandatory'=>true, 'class' => 'room_no'),
			'floor_no'=>array('type' => 'text', 'label'=>'Floor Number', 'mandatory'=>true, 'class' => 'floor_no'),
			'amenities[]'=>array('type' => 'select', 'label'=>'Choose Amenities', 'value' => $getAmenities, 'mandatory'=>true, 'class' => 'amenities', 'multiple' => 'multiple'),
			'total_beds'=>array('type' => 'text', 'label'=>'Total Beds', 'mandatory'=>true, 'class' => 'total_beds'),
			'avail_beds'=>array('type' => 'text', 'label'=>'Available Beds', 'mandatory'=>true, 'class' => 'avail_beds'),
			'price_per_bed'=>array('type' => 'text', 'label'=>'Price per Bed', 'mandatory'=>true, 'class' => 'price_per_bed'),
			
    	);
    	
		
		return view('admin.Hostel.add-hostel', ['otherLinks' => array('link' => url('/').'/hostel-room-list', 'text' => 'Hostel Room List'), 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit Hostel Room':'Add Hostel Room', 'voucher_no' => isset($voucherId) ? $voucherId : 'SCVCH1'])->with($getFormAutoFillup);
	}
	public function hostelRoomListing()
	{
		/* Generate List of Available CLasses */
		$employeeList = array('' => '-- Select --', '0' => '.:: Other Person ::.');
    	$employeeList += User::pluck('name', 'id')->toArray();

		$voucher = Voucher::paginate(15);
		$customFields['basic'] = array(
			'voucher_no'=>array('type' => 'text', 'label'=>'Voucher Number','mandatory'=>true),
			'employee_id'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $employeeList, 'mandatory'=>true, 'class' => 'admission_class'),
    	);
		return view('master.voucher-list', ['otherLinks' => array('link' => url('/').'/voucher-issue', 'text' => 'Add Voucher'), 'pageTitle' => 'Voucher List', 'datas' => $voucher, 'customFields' => $customFields, 'loopInit' => '1']);
	}
	public function trashHostelRoom(Request $request, $evid = null)
	{
		if(Event::where('id', $evid)->delete()){
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Event was successfully Deleted!');
    	}else{
    		session()->flash('status', ['danger', 'Deletion of Event Failed!']);
    	}
    	return redirect(url('/').'/event');
	}
	public function validateHostelRoomNo(Request $request)
	{
		//echo $request->room_no." ".$request->hostel_id;
		$checkRoom = HostelRoom::where('hostels_id', '=', $request->hostel_id)
						->where('room_no', '=', $request->room_no)->exists();
		if(isset($checkRoom) && $checkRoom !=''){
			$return = '1';
		}else{
			$return = '0';
		}
		echo $return;
	}

	/* ##########################  Allotment of Hoste room to a student ############################*/
	/*
		info : Hostel Allotment
		Date ; 25-12-2017
    */
	public function allotHostel(Request $request, $eid=null)
	{
		$data[] = array();
		
		$getHostels = array('' => '-- Slect --');
		$getHostels += Hostel::pluck('hos_name', 'id')->toArray();
		/* Field specifiic Validations */	

		$getAmenities = array('WIFI' => 'WIFI', 'LANDLINE/MOBILE' => 'LANDLINE/MOBILE', 'AC' => 'AC', 'TV' => 'TV', 'FRIDGE' => 'FRIDGE');


        $validationRules = [
            'hostels_id' => 'required',
            'room_no_id' => 'required',
            'admission_class' => 'required',
            'admission_id' => 'required'
        ];

    	$validator = Validator::make($request->all(), $validationRules);
       	if(isset($eid) && $eid != null){
    		$getFormAutoFillup = Voucher::whereId($eid)->first()->toArray();
    		$admission_class = $getFormAutoFillup['admission_class'];
    	}else{
    		$getFormAutoFillup = array();
    	}
		/* Submit all data */
    	if ($request->isMethod('post')){
    		if($validator->fails()){
            	return redirect('/hostel-allot')->withErrors($validator)->withInput();
            }else{
            	if(isset($request->id) && $request->id != null){
		    		/* 
						Update
		    		*/
		    		$updateStudent = Voucher::find($request->id);
		    		if(isset($request->_token) && $request->_token != ''){
		    			unset($request['_token']);
		    		}
		    		/* Calculation of total Amount on the GST and Discount */
		    		$totalPrice = $request->stock * $request->price;
		    		$discountInPrice = isset($request->discount) ? $totalPrice*($request->discount/100) : 0;
		    		$total = ($totalPrice + ($totalPrice*($request->gst/100)))-$discountInPrice;
		    		$request->request->add(['total' => $total]);

		    		//dd($request->toArray());
		    		if(Voucher::where([['id', '=', $request->id]])->update($request->toArray())){
		    			$request->session()->flash('message.level', 'info');
		    			$request->session()->flash('message.content', 'Voucher Details are Updated Successfully !');
		    		}else{
		        		session()->flash('status', ['danger', 'Addition was Failed!']);
		        	}
		        	return redirect('/hostel-allot/'.$request->id);
		    	}else{
		    		$saveData = $request->toArray();
		    		$saveData = new HostelAllot($saveData);

		    		/* Get Bed Details from Bed ID */
			    	$getHostelRoomDetails = HostelRoom::find($request->room_no_id)->select('total_beds', 'avail_beds', 'price_per_bed')->first();

		    		/* save "price_per_bed" details for future refferences */
		    		$request->request->add(['price_per_bed' => $getHostelRoomDetails['price_per_bed']]);

		    		/* Save Room Allotment Details to database and then Update Beds Count in the Room Details */
		    		if($saveData->save()){

		    			/* Update the Available Beds in the Room */
			    		$getHostelRoomDetails = $getHostelRoomDetails->toArray();
			    		$availableBeds = $getHostelRoomDetails['avail_beds'] - 1;
			    		HostelRoom::where('id', $request->room_no_id)->update(['avail_beds' => $availableBeds]);

		        		$request->session()->flash('message.level', 'success');
		    			$request->session()->flash('message.content', 'Hostel Room was successfully Assigned to the Student.! Beds count has been Dudected From the Room.');
		        	}else{
		        		session()->flash('status', ['danger', 'Updation of Hostel Room Failed!']);
		        	}
		        	return redirect('/hostel-allot');
		        }
            }
			
    	}
    	/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}

		$customFields['basic'] = array(
			'hostels_id'=>array('type' => 'select', 'label'=>'Choose a Hostel', 'value' => $getHostels, 'mandatory'=>true, 'class' => 'hostels_id'),
			'room_no_id'=>array('type' => 'select', 'label'=>'Choose Room No', 'value' => array('' => '- Choose Hostel -'), 'mandatory'=>true, 'class' => 'room_no'),
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class_hostel'),
			'admission_id' => array('type' => 'select', 'label'=>'Choose Student', 'value' => array(), 'mandatory'=>true, 'class' => 'admission_id'),
			'date_of_allotment' => array('type' => 'text', 'label'=>'Date of Allotment', 'mandatory'=>true, 'id' => 'datepicker', 'class' => 'date_of_allotment'),
			'remarks' => array('type' => 'text', 'label'=>'Remark', 'mandatory'=>true, 'class' => 'remarks'),
    	);
    	
		
		return view('admin.Hostel.allot-hostel', ['otherLinks' => array('link' => url('/').'/hostel-allot-list', 'text' => 'Hostel Room Allotment'), 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit Hostel Room':'Add Hostel Room', 'voucher_no' => isset($voucherId) ? $voucherId : 'SCVCH1'])->with($getFormAutoFillup);
	}

	public function hostelAllotLists()
	{
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}

		$customFields['basic'] = array(
			'event_name'=>array('type' => 'text', 'label'=>'Event Name','mandatory'=>true),
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class'),
    	);

		$data['rows'] 			= HostelAllot::with(['admission', 'hostel_room', 'hostel'])->get()->toArray();
		$data['otherLinks'] 	= array('link' => url('/').'/hostel-allot', 'text' => 'Allot a New Room') ;
		$data['pageTitle'] 		= 'Allotted Room\'s List';
		$data['customFields'] 	= $customFields;
		$data['loopInit'] 		= '1';

		

		//dd($getAllotmentList);

		return view('admin.Hostel.allot-hostel-listing', $data); 
	}
	public function ajaxGetRoomsByHostelId(Request $request)
	{
		if(isset($request->hostels_id) && $request->hostels_id !=''){
			$getRoomDetails = HostelRoom::where('hostels_id', '=', $request->hostels_id)
								->where('avail_beds', '>', '0')
								->pluck('room_no', 'id');
			echo json_encode($getRoomDetails);

		}else{
			echo json_encode([]);
		}
	}
}
