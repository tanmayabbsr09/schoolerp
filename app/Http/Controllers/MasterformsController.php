<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FeesCategory;
use App\FeesSubcategory;
use App\FeesMaster;
use DB;
use Helper;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Role;
use Analytics;
use App\Event;
use App\Admission;
use App\Supplier;
use App\Library;
use App\Voucher;


class MasterformsController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function feesCategory(Request $request)
    {
    	$data['feesMasterData'] = FeesMaster::get()->toArray();
    	$data['i'] = 1;
    	/* Processing the "Category Form" */
		if ($request->has('form-category')) {
			$category = new FeesCategory($request->toArray());
			if($category->save()){
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Category Name saved Successfully !');
	    	}else{
	    		session()->flash('status', ['danger', 'Saving Failed!']);
	    	}
	    	return redirect('/master-fees');
		}
		/* Processing the "Sub Category Form" */
		if ($request->has('form-subcategory')) {
			$subcategory = new FeesSubcategory($request->toArray());
			if($subcategory->save()){
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Sub-Category Name saved Successfully !');
	    	}else{
	    		session()->flash('status', ['danger', 'Saving Failed!']);
	    	}
	    	return redirect('/master-fees');
		}
		/* Processing the "Sub Category Form" */
		if ($request->has('form-fees')) {
			//dd($request->all());
			$feesMaster = new FeesMaster($request->all());
			if($feesMaster->save()){
				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', 'Fees Details saved Successfully !');
	    	}else{
	    		session()->flash('status', ['danger', 'Saving Failed!']);
	    	}
	    	return redirect('/master-fees');

		}

		/* Get Category Lists */
		$data['catList'] = ['' => '-- Select --'];
		$data['catList'] += FeesCategory::pluck('category_name', 'id')->toArray(); // = DB::table('widgets')->lists('widget', '_id')->toArray()
		
    	$classList = array('' => '-- Select --', 'NURSERY' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}

    	$getStudentDetails = array();
    	$customFields['category'] = array(
			'category'=>array('type' => 'text', 'label'=>'Total Amount','mandatory'=>true),
    	);
    	$customFields['master'] = array(
    		'category_id'=>array('type' => 'select', 'value' => $data['catList'], 'class' => 'category_id', 'btn' => array('name' => 'Add', 'class' => 'btn btn-default', 'linkTo' => '#modal-category'), 'label'=>'Fees Main Category', 'mandatory'=>true),
			'subcategory_id'=>array('type' => 'select', 'value' => array('' => '-- Select --'), 'class' => 'subcategory_name', 'btn' => array('name' => 'Add', 'class' => 'btn btn-default', 'linkTo' => '#modal-subcategory'), 'label'=>'Fees Subcategory','mandatory'=>true),
			'class'=>array('type' => 'select', 'value' => $classList, 'label'=>'Fees for Class','mandatory'=>true),
			'amount'=>array('type' => 'text', 'label'=>'Total Amount','mandatory'=>true),
			'remark'=>array('type' => 'text', 'label'=>'Remark','mandatory'=>false),
			'is_mandatory'=>array('type' => 'checkbox', 'label'=>'Is Compulsory','mandatory'=>true),
    	);
    	return view('master.fees', ['otherLinks' => array('link' => '#admission-list', 'text' => 'Fees List'), 'pageTitle' => isset($sid) && $sid != '' ? 'Master Form (Edit)':'Master Form', 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details'] )->with($getStudentDetails);
    }
    public function getSubcategories(Request $request)
    {

    	$getSubcatListFromFeesMaster = FeesMaster::select('subcategory_id')->get()->toArray();
    	$usedSubCat = array();
    	foreach ($getSubcatListFromFeesMaster as $key => $value) {
    		$usedSubCat[] = $value['subcategory_id'];
    	}
    	$usedSubCat = array_values($usedSubCat);
    	
    	/* Opt Out all Sub categries those are already Added to Fees Master */
    	$subCatDetails = FeesSubcategory::where('category_id', $request->category_id)
    	->whereNotIn('id', $usedSubCat)
    	->pluck('subcategory_name', 'id')->toArray();

    	$createSubcategoryOption = '<option value="">-- Select --</option>';
    	foreach ($subCatDetails as $key => $value) {
    		$createSubcategoryOption .= '<option value="'.$key.'">'.$value.'</option>';
    	}
    	echo $createSubcategoryOption;
    }

    /*
		info : Employee/User Master
		Date ; 14-11-2017
    */
	public function addUser(Request $request, $eid=null)
	{
		//dd($request);
		/* Generating Year List */
		$data['yearOfJoing'] = array('' => '-- Select --');
		for($i=(int)(date('Y')-10);$i<=date('Y');$i++){ $data['yearOfJoing'] += array($i => $i);}
		/* Get List of Roles */
		$getRoles = array('' => '-- Select --');
		$getRoles += Role::where('is_active', '1')->pluck('name', 'id')->toArray();
		/* Field specifiic Validations */	
		if(isset($request->id) && $request->id != null){
	        $validationRules = [
	            'name' => 'required|max:255',
	            'email' => 'required|email|max:150',
	            'confirm_password' => 'same:password'
	        ];
    	}else{
			$validationRules = [
	            'name' => 'required|max:255',
	            'email' => 'required|email|max:150|unique:users',
	            //'password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
	            'password' => 'required',
	            'confirm_password' => 'required|same:password'
	        ];
    	}
    	$validator = Validator::make($request->all(), $validationRules);
        if(isset($eid) && $eid != null){
    		$getEmployeeDetails = user::find($eid)->toArray();
    	}else{
    		$getEmployeeDetails = array();
    	}
		/* Submit all data */
    	if ($request->isMethod('post')){
    		if($validator->fails()){
            	return redirect('/employee/'.$request->id)->withErrors($validator)->withInput();
            }else{
            	if(isset($request->id) && $request->id != null){
		    		/* 
						Update
		    		*/

		    		$updateStudent = User::find($request->id);
		    		if(!isset($request->password) && $request->password == ''){
		    			unset($request['password']);
		    			unset($request['confirm_password']);
		    			unset($request['_token']);
		    		}

		    		//dd($request->toArray());
		    		if(User::where([['id', '=', $request->id], ['role_id', '!=', '1']])->update($request->toArray())){
		    			$request->session()->flash('message.level', 'info');
		    			$request->session()->flash('message.content', 'Student Details are Updated Successfully !');
		    		}else{
		        		session()->flash('status', ['danger', 'Addition was Failed!']);
		        	}
		        	return redirect('/employee-edit/'.$request->id);
		    	}else{
		    		$saveUser = $request->toArray();
		    		$saveUser['password'] = Hash::make($saveUser['password']);
		    		//dd($saveUser);
		    		$empSave = new User($saveUser);

		    		if($empSave->save()){
		        		$request->session()->flash('message.level', 'success');
		    			$request->session()->flash('message.content', 'Post was successfully added!');
		        	}else{
		        		session()->flash('status', ['danger', 'Addition of User Failed!']);
		        	}
		        	return redirect('/employee');
		        }
            }
			
    	}

		$customFields['basic'] = array(
			'name'=>array('type' => 'text', 'label'=>'Employee Name','mandatory'=>true),
			'email'=>array('type' => 'text', 'label'=>'Email ID','mandatory'=>true),
			'password'=>array('type' => 'password', 'label'=>'Password','mandatory'=>true),
			'confirm_password'=>array('type' => 'password', 'label'=>'Confirm Password','mandatory'=>true),
	        'department_name'=>array('type' => 'select', 'label' => 'Department Name', 'value' => array('' => '-- Select --','teacher' => 'Teacher', 'administrative' => 'Administrative','other' => 'Other'), 'mandatory'=>true),
			'role_id'=>array('type' => 'select', 'label'=>'Employee Designation', 'value' => $getRoles,'mandatory'=>true),
			'employee_gender'=>array('type' => 'select', 'label' => 'Gender', 'value'=> array('' =>'-- Select --','male'=>'Male','female'=> 'Female'), 'mandatory'=>true),
			'year_of_joining_school'=>array('type' => 'select', 'value' => $data['yearOfJoing'], 'label'=>'Year of Joining School','mandatory'=>true),
			'dob'=>array('type' => 'text', 'label'=>'Date of Birth', 'id' => 'datepicker', 'mandatory'=>true),
			'blood_group'=>array('type' => 'select', 'label' => 'Blood Group', 'value' => array('' => '-- Select --','A+' => 'A+', 'AB+' => 'AB+','B+' => 'B+','O+' => 'O+','A-' => 'A-','B-' => 'B-'), 'mandatory'=>true),
			'acadamic_qualification'=>array('type' => 'text', 'label'=>'Acadamic Qualification','mandatory'=>true),
			'professional_qualification'=>array('type' => 'text', 'label'=>'Professional Qualification','mandatory'=>true),
    	);
		
		return view('admin.user', ['otherLinks' => array('link' => 'employee-list', 'text' => 'Employee List'), 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit Employee':'Add Employee'])->with($getEmployeeDetails);
	}

	public function userList()
	{
		/*$analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));
		dd($analyticsData);*/
		/* Fetch User List along with corsp. Role Details ;) */
		$users = User::where('role_id', '!=', '1')->with('role')->paginate(15);
		$customFields['basic'] = array(
			'name'=>array('type' => 'text', 'label'=>'Employee Name','mandatory'=>true),
			'email'=>array('type' => 'text', 'label'=>'Email ID','mandatory'=>true),
    	);
		return view('admin.user-list', ['otherLinks' => array('link' => '/employee', 'text' => 'Add Employee'), 'pageTitle' => 'Employee List', 'users' => $users, 'customFields' => $customFields, 'loopInit' => '1']);
	}

	/*
		info : Employee/User Master
		Date ; 14-11-2017
    */
	public function eventManagement(Request $request, $eid=null)
	{
		$data[] = array();
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}

		/* Field specifiic Validations */	
		
	        $validationRules = [
	            'event_name' => 'required|max:255',
	            'admission_class' => 'required',
	            'first_position' => 'required',
	            'second_position' => 'required',
	            'third_position' => 'required'
	        ];
    	
    	$validator = Validator::make($request->all(), $validationRules);
       	if(isset($eid) && $eid != null){
    		$getEventAutoFillup = Event::whereId($eid)->first()->toArray();
    		$admission_class = $getEventAutoFillup['admission_class'];
    		$first_position = $second_position = $third_position = Admission::where('admission_class', $admission_class)->pluck('name', 'id');
    	}else{
    		$getEventAutoFillup = array();
    		$first_position = $second_position = $third_position = array();
    	}
		/* Submit all data */
    	if ($request->isMethod('post')){
    		if($validator->fails()){
            	return redirect('/event')->withErrors($validator)->withInput();
            }else{
            	if(isset($request->id) && $request->id != null){
		    		/* 
						Update
		    		*/
		    		$updateStudent = Event::find($request->id);
		    		if(isset($request->_token) && $request->_token != ''){
		    			unset($request['_token']);
		    			unset($request['event_name2']);
		    			unset($request['event_name3']);
		    		}
		    		//dd($request->toArray());
		    		if(Event::where([['id', '=', $request->id]])->update($request->toArray())){
		    			$request->session()->flash('message.level', 'info');
		    			$request->session()->flash('message.content', 'Student Details are Updated Successfully !');
		    		}else{
		        		session()->flash('status', ['danger', 'Addition was Failed!']);
		        	}
		        	return redirect('/event-edit/'.$request->id);
		    	}else{
		    		$saveEvent = $request->toArray();
		    		$eventSave = new Event($saveEvent);

		    		if($eventSave->save()){
		        		$request->session()->flash('message.level', 'success');
		    			$request->session()->flash('message.content', 'Event was successfully added!');
		        	}else{
		        		session()->flash('status', ['danger', 'Addition of Event Failed!']);
		        	}
		        	return redirect('/event');
		        }
            }
			
    	}

		$customFields['basic'] = array(
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class'),
			'event_name'=>array('type' => 'text', 'label'=>'Event Name', 'mandatory'=>true, 'class' => 'event_name'),
			'remark'=>array('type' => 'text', 'label'=>'Remark', 'mandatory'=>false, 'class' => 'remark'),
    	);
    	$customFields['position'] = array(
			'first_position'=>array('type' => 'select', 'label'=>'First Place', 'value' => isset($first_position) ? $first_position : array('' => '-- Choose --'), 'mandatory'=>true, 'class' => 'first_position'),
			'second_position'=>array('type' => 'select', 'label'=>'Second Place', 'value' => isset($second_position) ? $second_position : array('' => '-- Choose --'), 'mandatory'=>true, 'class' => 'second_position'),
			'third_position'=>array('type' => 'select', 'label'=>'Third Place', 'value' => isset($third_position) ? $third_position : array('' => '-- Choose --'), 'mandatory'=>true, 'class' => 'third_position'),
    	);
		
		return view('master.curriculum', ['otherLinks' => array('link' => url('/').'/event-list', 'text' => 'Event List'), 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit Event':'Add Event', 'first_position' => $first_position])->with($getEventAutoFillup);
	}
	public function eventListing()
	{
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}
		$events = Event::paginate(15);
		$customFields['basic'] = array(
			'event_name'=>array('type' => 'text', 'label'=>'Event Name','mandatory'=>true),
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class'),
    	);
		return view('master.event-list', ['otherLinks' => array('link' => url('/').'/event', 'text' => 'Add Event'), 'pageTitle' => 'Event List', 'events' => $events, 'customFields' => $customFields, 'loopInit' => '1']);
	}
	public function trashEvents(Request $request, $evid = null)
	{
		if(Event::where('id', $evid)->delete()){
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Event was successfully Deleted!');
    	}else{
    		session()->flash('status', ['danger', 'Deletion of Event Failed!']);
    	}
    	return redirect(url('/').'/event');
	}

	/* ##########################  LIBRARY MANAGEMENT ############################*/
	/*
		info : Employee/User Master
		Date ; 14-11-2017
    */
	public function libraryManagement(Request $request, $eid=null)
	{
		$data[] = array();
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}
		$getSuppliers = array('' => '-- Slect --');
		$getSuppliers += Supplier::pluck('name', 'id')->toArray();
		/* Field specifiic Validations */	
		
        $validationRules = [
            'book_name' => 'required|max:255',
            'suppliers_id' => 'required',
            'date_of_purchase' => 'required',
            'invoice' => 'required',
            'admission_class' => 'required'
        ];
    	
    	$validator = Validator::make($request->all(), $validationRules);
       	if(isset($eid) && $eid != null){
    		$getFormAutoFillup = Library::whereId($eid)->first()->toArray();
    		$admission_class = $getFormAutoFillup['admission_class'];
    	}else{
    		$getFormAutoFillup = array();
    	}
		/* Submit all data */
    	if ($request->isMethod('post')){
    		if($validator->fails()){
            	return redirect('/library')->withErrors($validator)->withInput();
            }else{
            	if(isset($request->id) && $request->id != null){
		    		/* 
						Update
		    		*/
		    		$updateStudent = Library::find($request->id);
		    		if(isset($request->_token) && $request->_token != ''){
		    			unset($request['_token']);
		    		}
		    		$request->merge(['raw_book_name' => $request->book_name.', by- ['.$request->author_name.' @ '.$request->publisher_name.']']);
		    		/* Calculation of total Amount on the GST and Discount */
		    		$totalPrice = $request->stock * $request->price;
		    		$discountInPrice = isset($request->discount) ? $totalPrice*($request->discount/100) : 0;
		    		$total = ($totalPrice + ($totalPrice*($request->gst/100)))-$discountInPrice;
		    		$request->request->add(['total' => $total]);

		    		//dd($request->toArray());
		    		if(Library::where([['id', '=', $request->id]])->update($request->toArray())){
		    			$request->session()->flash('message.level', 'info');
		    			$request->session()->flash('message.content', 'Library Details are Updated Successfully !');
		    		}else{
		        		session()->flash('status', ['danger', 'Addition was Failed!']);
		        	}
		        	return redirect('/library-edit/'.$request->id);
		    	}else{
		    		$saveData = $request->toArray();
		    		$request->merge(['raw_book_name' => $request->book_name.', by- ['.$request->author_name.' @ '.$request->publisher_name.']']);
		    		/* Calculation of total Amount on the GST and Discount */
		    		$totalPrice = $request->stock * $request->price;
		    		$discountInPrice = isset($request->discount) ? $totalPrice*($request->discount/100) : 0;
		    		$saveData['total'] = ($totalPrice + ($totalPrice*($request->gst/100)))-$discountInPrice;

		    		$saveData = new library($saveData);

		    		if($saveData->save()){
		        		$request->session()->flash('message.level', 'success');
		    			$request->session()->flash('message.content', 'Library was successfully added!');
		        	}else{
		        		session()->flash('status', ['danger', 'Updation of Library Failed!']);
		        	}
		        	return redirect('/library');
		        }
            }
			
    	}

		$customFields['basic'] = array(
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class'),
			'suppliers_id'=>array('type' => 'select', 'label'=>'Choose Supplier', 'value' => $getSuppliers, 'mandatory'=>true, 'class' => 'admission_class'),
			'book_name'=>array('type' => 'text', 'label'=>'Book Name', 'mandatory'=>true, 'class' => 'book_name'),
			'author_name'=>array('type' => 'text', 'label'=>'Author Name', 'mandatory'=>true, 'class' => 'author_name'),
			'publisher_name'=>array('type' => 'text', 'label'=>'Publisher Name', 'mandatory'=>true, 'class' => 'publisher_name'),
			'stock'=>array('type' => 'text', 'label'=>'Stock', 'mandatory'=>true, 'class' => 'stock'),
			'price'=>array('type' => 'text', 'label'=>'Price', 'mandatory'=>true, 'class' => 'price'),
			'gst'=>array('type' => 'text', 'label'=>'GST(%)', 'mandatory'=>true, 'class' => 'gst'),
			'discount'=>array('type' => 'text', 'label'=>'Discount(%)', 'mandatory'=>true, 'class' => 'discount'),
			'date_of_purchase'=>array('type' => 'text', 'label'=>'Date of Purchase', 'mandatory'=>true, 'id' => 'datepicker'),
			'invoice'=>array('type' => 'text', 'label'=>'Invoice #', 'mandatory'=>true, 'class' => 'invoice_no'),
			//'remark'=>array('type' => 'text', 'label'=>'Remark', 'mandatory'=>false, 'class' => 'remark'),
    	);
    	
		
		return view('master.library', ['otherLinks' => array('link' => url('/').'/library-list', 'text' => 'Event List'), 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit Book':'Add Book'])->with($getFormAutoFillup);
	}
	public function libraryListing()
	{
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}
		$events = Library::paginate(15);
		$customFields['basic'] = array(
			'book_name'=>array('type' => 'text', 'label'=>'Event Name','mandatory'=>true),
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class'),
    	);
		return view('master.library-list', ['otherLinks' => array('link' => url('/').'/event', 'text' => 'Add Event'), 'pageTitle' => 'Event List', 'events' => $events, 'customFields' => $customFields, 'loopInit' => '1']);
	}
	public function trashLibrary(Request $request, $evid = null)
	{
		if(Event::where('id', $evid)->delete()){
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Event was successfully Deleted!');
    	}else{
    		session()->flash('status', ['danger', 'Deletion of Event Failed!']);
    	}
    	return redirect(url('/').'/event');
	}


	/* ##########################  Voucher MANAGEMENT ############################*/
	/*
		info : Voucher Master
		Date ; 14-12-2017
    */
	public function voucherManagement(Request $request, $eid=null)
	{
		$data[] = array();
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}
		$getSuppliers = array('' => '-- Slect --');
		$getSuppliers += Supplier::pluck('name', 'id')->toArray();
		/* Field specifiic Validations */	



        $validationRules = [
            'voucher_no' => 'required',
            'employee_id' => 'required',
            'pay_to' => 'required|max:200',
            'voucher_date' => 'required',
            'voucher_details' => 'required|max:200',
            'payment_mode' => 'required'
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
            	return redirect('/voucher-issue')->withErrors($validator)->withInput();
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
		    		

		    		$saveData = new Voucher($saveData);

		    		if($saveData->save()){
		        		$request->session()->flash('message.level', 'success');
		    			$request->session()->flash('message.content', 'Voucher was successfully added!');
		        	}else{
		        		session()->flash('status', ['danger', 'Updation of Voucher Failed!']);
		        	}
		        	return redirect('/voucher-issue');
		        }
            }
			
    	}
    	$employeeList = array('' => '-- Select --', '0' => '.:: Other Person ::.');
    	$employeeList += User::pluck('name', 'id')->toArray();
    	$voucherMaxId = DB::table('vouchers')->max('id');
    	$voucherId = 'SCVCH'.($voucherMaxId+1);

		$customFields['basic'] = array(
			'voucher_no'=>array('type' => 'text', 'label'=>'Voucher Number', 'mandatory'=>true, 'class' => 'voucher_no'),
			'amount'=>array('type' => 'text', 'label'=>'Amount', 'mandatory'=>true, 'class' => 'amount'),
			'flow_type'=>array('type' => 'select', 'label'=>'Choose Money Flow', 'value' => array('OUTFLOW' => 'Out Flow', 'INFLOW' => 'In Flow'), 'mandatory'=>true, 'class' => 'flow_type'),
			'employee_id'=>array('type' => 'select', 'label'=>'Choose Person', 'value' => $employeeList, 'mandatory'=>true, 'class' => 'employee_id'),
			'pay_to'=>array('type' => 'text', 'label'=>'Paid To/By', 'mandatory'=>true, 'class' => 'pay_to'),
			'voucher_date'=>array('type' => 'text', 'label'=>'Voucher Date', 'mandatory'=>true, 'id' => 'datepicker'),
			'voucher_details'=>array('type' => 'text', 'label'=>'Voucher Details', 'mandatory'=>true, 'class' => 'voucher_details'),
			'payment_mode'=>array('type' => 'select', 'label'=>'Payment Mode', 'value' => ['CASH' => 'By Cash', 'CHEQUE' => 'By Cheque', 'iBanking' => 'By Internet Banking'], 'mandatory'=>true, 'class' => 'payment_mode'),
			
    	);
    	
		
		return view('master.voucher', ['otherLinks' => array('link' => url('/').'/voucher-list', 'text' => 'Voucher List'), 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit Voucher':'Add Voucher', 'voucher_no' => isset($voucherId) ? $voucherId : 'SCVCH1'])->with($getFormAutoFillup);
	}
	public function voucherListing()
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
	public function trashVoucher(Request $request, $evid = null)
	{
		if(Event::where('id', $evid)->delete()){
			$request->session()->flash('message.level', 'success');
			$request->session()->flash('message.content', 'Event was successfully Deleted!');
    	}else{
    		session()->flash('status', ['danger', 'Deletion of Event Failed!']);
    	}
    	return redirect(url('/').'/event');
	}
}
