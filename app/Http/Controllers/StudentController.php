<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Crypt;
use App\Admission;
use DB;
use Helper;
use App\FeesMaster;
use App\AdmissionFee;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function admission(Request $request, $sid=null)
    {
        
        //Debugbar::info($object);
    	$otherLinks = '<a href="/student-list">Admission List</a>';
    	/* generate Year Range for Academic Year */
    	$max_year = date('Y');
		$current_year = $max_year - 6;
		for($i = $current_year; $i <= $max_year; $i++){
		    $year_array[$i] = $i.'-'.($i+1);
		}
		$getStudentDetails = array();
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}

    	/* Field specifiic Validations */
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'gender' => 'required',
            'father_mobile_no' => 'required|digits:10',
            'dob' => 'required',
            'aadhar_card' => 'required|digits:16',
            'academic_year' => 'required',
            'admission_class' => 'required',
            'academic_year' => 'required',
            'father_name' => 'required',
            'mother_name' => 'required',
            'mother_mobile_no' => 'required|digits:10',
            'present_at' => 'required',
            'present_pin' => 'required|digits:6',
            'permanent_at' => 'required',
            'permanent_pin' => 'required|digits:6',
            'residental_type' => 'required',

        ]);
      
    	/* Submit all data */
    	if ($request->isMethod('post')){
    		$md5Time = md5(time());
    		$uploadFilesName = array();
    		
    		/* All File Names are Generated Here */
			$blood_group_proof 	= count($request->blood_group_proof_raw) > 0 ? $md5Time.'-'.$request->blood_group_proof_raw->getClientOriginalName() : '';
			$birth_certificate 	= count($request->birth_certificate_raw) > 0 ? $md5Time.'-'.$request->birth_certificate_raw->getClientOriginalName() : '';
			$aadhar_card_proof 	= count($request->aadhar_card_proof_raw) > 0 ? $md5Time.'-'.$request->aadhar_card_proof_raw->getClientOriginalName() : '';

			/* All Images are Moved to the Corresponding Folders Here */
    		if(isset($request->blood_group_proof_raw) && count($request->blood_group_proof_raw) > 0){
    			echo "Dasdasdasdsadsadad";
	    		$request->file('blood_group_proof_raw')->move(
			        base_path() . '/public/blood_group_proof/', $blood_group_proof
			    );
			}
			if(isset($request->birth_certificate_raw)  && count($request->birth_certificate_raw) > 0){
			    $request->file('birth_certificate_raw')->move(
			        base_path() . '/public/birth_certificate/', $birth_certificate
			    );
			}

			if(isset($request->aadhar_card_proof_raw) && count($request->aadhar_card_proof_raw) > 0){
			    $request->file('aadhar_card_proof_raw')->move(
			        base_path() . '/public/aadhar_card_proof/', $aadhar_card_proof
			    );
			}
			
			/* All File Names are Set Here */
			$request->request->add(['blood_group_proof' => $blood_group_proof]);
			$request->request->add(['birth_certificate' => $birth_certificate]);
			$request->request->add(['aadhar_card_proof' => $aadhar_card_proof]);

			/* Save Object pushed to Model */
			$admissionObj = new Admission($request->toArray());
    		if($validator->fails()){
            	return redirect('/admission')->withErrors($validator)->withInput();
            }else{
            	if(isset($request->id) && $request->id != ''){
            		/* 
						Update
            		*/

            		$updateStudent = Admission::find($request->id);
            		if(Admission::where('id', $request->id)->update($request->toArray())){
            			$request->session()->flash('message.level', 'info');
	        			$request->session()->flash('message.content', 'Student Details are Updated Successfully !');
            		}else{
	            		session()->flash('status', ['danger', 'Addition was Failed!']);
	            	}
	            	return redirect('/admission/'.$request->id);
            	}else{
            		if($admissionObj->save()){
	            		$request->session()->flash('message.level', 'success');
	        			$request->session()->flash('message.content', 'Post was successfully added!');
	            	}else{
	            		session()->flash('status', ['danger', 'Addition of User Failed!']);
	            	}
	            	return redirect('/admission');
	            }
            	
            }      

    	}
    	
    	/* Generate Custom Fields Array */
    	$customFields['general'] = array(
    		'name'=>array('type' => 'text', 'label'=>'Name of the Pupil', 'class' => 'capsLock alpha', 'mandatory'=>true),
    		'gender'=>array('type' => 'select', 'value' => array('' => '-- Select --','Girl' => 'Girl', 'Boy' => 'Boy', 'Other' => 'Other'), 'label'=>'Gender','mandatory'=>true),
    		'dob'=>array('type' => 'text', 'label'=>'Date of Birth', 'id' => 'datepicker', 'mandatory'=>true),
    		'mother_language'=>array('type' => 'text', 'label'=>'Mother Language', 'mandatory'=>true),
    		'secondary_language'=>array('type' => 'select', 'value' => array('' => '-- Select --', 'Oriya' => 'Oriya', 'Hindi' => 'Hindi', 'Sanskrit' => 'Sanskrit'), 'label'=>'Secondary Language', 'mandatory'=>true),
    		'blood_group'=>array('type' => 'select', 'value' => array('' => '-- Select --','A+' => 'A+', 'B+' => 'B+', 'AB+' => 'AB+', 'O+' => 'O+', 'A-' => 'A-', 'B-' => 'B-'), 'label'=>'Blood Group', 'mandatory'=>true),
    		'blood_group_proof_raw'=>array('type' => 'file', 'label'=>'Blood Group Certificate', 'mandatory'=>true),
    		'birth_certificate_raw'=>array('type' => 'file', 'label'=>'Birth Certificate', 'optColDiv' => 'col-md-2', 'mandatory'=>true),
    		'aadhar_card_proof_raw'=>array('type' => 'file', 'label'=>'Aadhar Card Proof', 'mandatory'=>true),
    		'aadhar_card'=>array('type' => 'text', 'label'=>'Aadhar Card', 'mandatory'=>true),
    		'academic_year' => array('type' => 'select', 'value' => $year_array, 'label'=>'Academic Year', 'mandatory'=>true),
    		'admission_class' => array('type' => 'select', 'value' => $classList, 'label'=>'Choose Class', 'mandatory'=>true),
    	);
    	$customFields['father'] = array(
    		'father_name'=>array('type' => 'text', 'label'=>'Father\'s Name', 'mandatory'=>true),
    		'father_qualification'=>array('type' => 'text', 'label'=>'Qualification','mandatory'=>true),
			'father_occupation'=>array('type' => 'text', 'label'=>'Occupation','mandatory'=>true),
			'father_official_designation'=>array('type' => 'text', 'label'=>'Official Designation','mandatory'=>true),
			'father_office_no'=>array('type' => 'text', 'label'=>'Office No.', 'class' => 'mobile', 'mandatory'=>true),
			'father_residential_no'=>array('type' => 'text', 'label'=>'Residential No', 'class' => 'mobile', 'mandatory'=>true),
			'father_mobile_no'=>array('type' => 'text', 'label'=>'Mobile No.', 'class' => 'mobile', 'mandatory'=>true),
			'father_email_id'=>array('type' => 'text', 'label'=>'E-Mail Id','mandatory'=>true),
    	);
    	$customFields['mother'] = array(
    		'mother_name'=>array('type' => 'text', 'label'=>'Mother\'s Name', 'mandatory'=>true),
    		'mother_qualification'=>array('type' => 'text', 'label'=>'Qualification','mandatory'=>true),
			'mother_occupation'=>array('type' => 'text', 'label'=>'Occupation','mandatory'=>true),
			'mother_official_designation'=>array('type' => 'text', 'label'=>'Official Designation','mandatory'=>true),
			'mother_office_no'=>array('type' => 'text', 'label'=>'Office No.', 'class' => 'mobile', 'mandatory'=>true),
			'mother_residential_no'=>array('type' => 'text', 'label'=>'Residential No', 'class' => 'mobile', 'mandatory'=>true),
			'mother_mobile_no'=>array('type' => 'text', 'label'=>'Mobile No.', 'class' => 'mobile', 'mandatory'=>true),
			'mother_email_id'=>array('type' => 'text', 'label'=>'E-Mail Id','mandatory'=>true),
    	);
    	$customFields['present_address'] = array(
    		'present_plot_house_no'=>array('type' => 'text', 'label'=>'Plot or House No.', 'mandatory'=>true),
    		'present_at'=>array('type' => 'text', 'label'=>'AT','mandatory'=>true),
			'present_post'=>array('type' => 'text', 'label'=>'POST','mandatory'=>true),
			'present_ps'=>array('type' => 'text', 'label'=>'PS','mandatory'=>true),
			'present_dist'=>array('type' => 'text', 'label'=>'DIST','mandatory'=>true),
			'present_state'=>array('type' => 'text', 'label'=>'STATE','mandatory'=>true),
			'present_pin'=>array('type' => 'text', 'label'=>'PIN', 'class' => 'mobile', 'mandatory'=>true),
    	);
    	$customFields['permanent_address'] = array(
    		'permanent_plot_house_no'=>array('type' => 'text', 'label'=>'Plot or House No.', 'mandatory'=>true),
    		'permanent_at'=>array('type' => 'text', 'label'=>'AT','mandatory'=>true),
			'permanent_post'=>array('type' => 'text', 'label'=>'POST','mandatory'=>true),
			'permanent_ps'=>array('type' => 'text', 'label'=>'PS','mandatory'=>true),
			'permanent_dist'=>array('type' => 'text', 'label'=>'DIST','mandatory'=>true),
			'permanent_state'=>array('type' => 'text', 'label'=>'STATE','mandatory'=>true),
			'permanent_pin'=>array('type' => 'text', 'label'=>'PIN', 'class' => 'mobile', 'mandatory'=>true),
    	);
    	$customFields['residental_type'] = array(
    		'residental_type'=>array('type' => 'select', 'value' => array('' => '-- Select --','transport' => 'Transport', 'dayboarding' => 'Dayboarding', 'hostel' => 'Hostel', 'localised' => 'Localised'), 'label'=>'Resident Type', 'mandatory'=>true),
			'residental_type_amount'=>array('type' => 'text', 'label'=>'Amount','mandatory'=>true),
    	);
    	$customFields['other_informations'] = array(
    		'serious_illness'=>array('type' => 'text', 'label'=>'Serious Illness', 'mandatory'=>true),
    		'identified_allergies'=>array('type' => 'text', 'label'=>'Identified Allergies','mandatory'=>true),
			'previous_edication'=>array('type' => 'text', 'label'=>'Previous Education','mandatory'=>true),
			'special_intrest'=>array('type' => 'text', 'label'=>'Special Intrest','mandatory'=>true),
			'two_person_allowed'=>array('type' => 'text', 'label'=>'Only Two Person Allow to Visit','mandatory'=>true),
			'mode_of_transport'=>array('type' => 'select', 'label' => 'Mode of Transport', 'value' => array('' => '-- Select --','bus' => 'BUS', 'own_arrangement' => 'Own Arrangement'), 'mandatory'=>true),
			'caste'=>array('type' => 'select', 'label' => 'Category', 'value' => array('' => '-- Select --','general' => 'General', 'sc' => 'SC','st' => 'ST', 'obc' => 'OBC','handicpped' => 'Handicapped'), 'mandatory'=>true),
			'whether_child_of_staff'=>array('type' => 'select', 'label' => 'Wheater Staff Child', 'value' => array('' => '-- Select --','1' => 'YES', '0' => 'NO'), 'mandatory'=>true),
			//'application_submit_date'=>array('type' => 'text', 'label'=>'Application Submit Date','mandatory'=>true),
			'application_fee_receipt_no'=>array('type' => 'text', 'label'=>'Application Fee Receipt No','mandatory'=>true),
			'photo_copy_front_side'=>array('type' => 'file', 'label'=>'Photo Copy of Application Form (Front Side)', 'mandatory'=>true),
    		'photo_copy_back_side'=>array('type' => 'file', 'label'=>'Photo Copy of Application Form (Back Side)', 'mandatory'=>true),   		
    	);

    	if(isset($sid) && $sid != null){
    		$getStudentDetails = Admission::find($sid)->toArray();
            //dd($getStudentDetails);
    	}

    	return view('admin.admission', ['otherLinks' => array('link' => url('/').'/admission-list', 'text' => 'Admision List'), 'pageTitle' => isset($sid) && $sid != '' ? 'Application Form (Edit)':'Application Form', 'customFields' => $customFields, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details'] )->with($getStudentDetails);
    }

    public function admissionList(Request $request)
    {
    	
        /* Generate List of Available CLasses */
        $classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
        for($i = 1; $i < 13; $i ++){
            $classList[$i] = 'STD - '.$this->integerToRoman($i);
        }
        $admission = Admission::where('is_active', '1');

        /* Gathering up All Filter Conditions */
        $conditions = array();
        $appends = array();
        if ($request->has('name')){
            $admission->where('name', 'like', '%'.$request->name.'%');
            $appends += array('name' => $request->name);
        }
        if($request->has('s_father_name')){
            $admission->where('father_name', 'like', '%'.$request->s_father_name.'%');
            $appends += array('s_father_name' => $request->s_father_name);
        }
        if($request->has('admission_class')){
            $admission->where('admission_class', '=', $request->admission_class);
            $appends += array('admission_class' => $request->admission_class);
        }
        /* Final Statement */
        $getList = $admission->paginate(15)->appends($appends);
        
        $customFields['search'] = array(            
            'name'=>array('type' => 'text', 'label'=>'Student Name', 'col_num' => '3', 'mandatory'=>true),
            's_father_name'=>array('type' => 'text', 'label'=>'Father', 'col_num' => '3', 'mandatory'=>true),
            'admission_class'=>array('type' => 'select', 'value' => $classList, 'label'=>'Class', 'col_num' => '2', 'mandatory'=>true),
            'from'=>array('type' => 'text', 'label'=>'From Date', 'col_num' => '2', 'id' => 'datepicker', 'mandatory'=>true),
            'to'=>array('type' => 'text', 'label'=>'To Date', 'col_num' => '2', 'id' => 'datepicker2', 'mandatory'=>true),
        );
    	return view('admin.admission-list', ['otherLinks' => array('link' => url('/').'/admission', 'text' => 'New Admision'), 'admissionList' => $getList, 'pageTitle' => 'Admission List', 'loopInit' => '1', 'customFields' => $customFields])->with($request->all());
    }
    public function admissionDelete(Request $request, $sid=null)
    {
    	if(Admission::where('id', $sid)->delete()){
			$request->session()->flash('message.level', 'warning');
			$request->session()->flash('message.content', 'Admission Record Was Delted from Server!');
    	}else{
    		session()->flash('status', ['danger', 'Deletion Failed!']);
    	}
    	return redirect('/admission');
    	
    }
    /*
        Info : Student Fees payment part
        Date : 14-11-2017
    */
    public function feesPayment(Request $request, $sid=null)
    {
        /* Check payment staus */
        $getCurrentYearRange = Helper::listAllYears(); 
        $getCurrentYearRange = reset($getCurrentYearRange);
        $returnDetails = 0;
        $getListOfMonth = Helper::listAllMonths();

        $getStudentPaymentDetails =  Helper::getStudentPaymnetStatus('1', '5', $getCurrentYearRange);
        $getStudentPaymentDetails = json_decode($getStudentPaymentDetails, true); 
        //dd($getStudentPaymentDetails);
       // exit;

       


        /*dd($getListOfMonth);
        exit;*/




        $data[] = '';
        $formAutoFill = array(); $cnt = 0; $data['submitBtnName'] = 'Save payment Details';
        $getPaymentHistory = array();
        $data['months'] = array('' => '-- Select --');
        $data['months'] += Helper::listAllMonths();
        $data['studentDetails'] = Helper::getStudentDetails($sid, ['id', 'name','father_name', 'mother_name', 'admission_class']);
        $studentClass = isset($data['studentDetails']->admission_class) ? $data['studentDetails']->admission_class : '';
        /* Get all Payment Structures of the Coressp. Class */
        $data['getPayments'] = FeesMaster::where('class', $studentClass)->get()->toArray();
    
        /* generate Year Range for Academic Year */
        $data['year_array'] = Helper::listAllYears();

        /* 
            Check if there any Previous payments Exists
        */
        $admission_id = isset($sid) ? $sid : $request->sid;
    


        /* Form Processing Starts */
        if ($request->isMethod('post')){
            $checkExistanceOfAdmId = AdmissionFee::where('admission_id', $request->sid)
            ->where('admission_class', $request->s_class)
            ->where('academic_year', $request->academic_year)
            ->where('academic_month', $request->academic_month)
            ->first();

            if(isset($checkExistanceOfAdmId) && is_array($checkExistanceOfAdmId) && count($checkExistanceOfAdmId) > 0){
                /*
                    UPDATE PAYMNET
                */

                $updatePaymnets['fees_master_id'] = json_encode($request->payment);
                if(AdmissionFee::where('id', '=', $checkExistanceOfAdmId['id'])->update($updatePaymnets)){
                    $request->session()->flash('message.level', 'info');
                    $request->session()->flash('message.content', 'Paymnet Details are Updated Successfully !');
                }else{
                    session()->flash('status', ['danger', 'Addition was Failed!']);
                }

                
               return redirect('/payment/'.$request->sid);
            
            }else{
                /*
                    SAVE PAYMNET
                */
                /* @@@ Calculations for Fines and ETC @@@ */


                /* @@ Ends @@*/

                DB::beginTransaction();
                //foreach ($request->academic_month as $key => $month) {
                    $savePaymnets = new AdmissionFee();
                    $savePaymnets['admission_class'] = $request->s_class;
                    $savePaymnets['admission_id'] = $request->sid;
                    $savePaymnets['academic_year'] = $request->academic_year;
                    $savePaymnets['academic_month'] = $request->academic_month;//json_encode(array_values($request->academic_month));
                    $savePaymnets['fees_master_id'] = json_encode(array_values($request->payment));
                    //dd($savePaymnets);
                //}
                if($savePaymnets->save()){
                    DB::commit();
                    $request->session()->flash('message.level', 'info');
                    $request->session()->flash('message.content', 'Student Paymnet Details are Saved Successfully !');
                }else{
                    DB::rollBack();
                    session()->flash('status', ['danger', 'Addition was Failed!']);
                }
                //return redirect('/payment/'.$request->sid);
                //return redirect('/admission-list');
                return redirect('/payment/'.$request->sid);
            }
        }
        $customFields['master'] = array(
            'residental_type'=>array('type' => 'select', 'value' => array('' => '-- Select --','transport' => 'Transport', 'dayboarding' => 'Dayboarding', 'hostel' => 'Hostel', 'localised' => 'Localised'), 'label'=>'Resident Type', 'mandatory'=>true),
            'residental_type_amount'=>array('type' => 'text', 'label'=>'Amount','mandatory'=>true),
        );
        /* pass student admission id to the payment details page */
        $admId = isset($sid)?$sid:'';
        return view('admin.payment', ['otherLinks' => array('link' => url('/').'/get-payment-overview/'.$admId, 'text' => 'Payment List'), 'pageTitle' => isset($sid) && $sid != '' ? 'Student Payment Details':'Application Payment Form', 'data' => $data, 'customFields' => $customFields, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'counter' => '1'] )->with($formAutoFill);
    }
    /*
        Student's Payment Overview of various Years
    */
    public function paymentOverview($sid=null, $year=null)
    {
            $data['submitBtnName'] = 'Show Payment Overview';

            $data['year_array']   = Helper::listAllYears();
            $getDefYear = isset($year) ? $year : reset($data['year_array']);
            $getListOfMonth = Helper::listAllMonths();
            $data['studentDetails'] = Admission::where('id', $sid)->select('admission_class', 'father_name', 'mother_name', 'name')->first()->toArray();
            $getStudentClass = $data['studentDetails']['admission_class'];

            //dd($getListOfMonth);
            $getStudentDetails = Helper::getStudentPaymnetStatus($sid, $getStudentClass, $getDefYear);
            $data['allMonthOverview'] = json_decode($getStudentDetails, true);
               // dd($getStudentDetails);
            $customFields['search'] = array(            
                'name'=>array('type' => 'text', 'label'=>'Student Name', 'col_num' => '3', 'mandatory'=>true),
                's_father_name'=>array('type' => 'text', 'label'=>'Father', 'col_num' => '3', 'mandatory'=>true),
                'admission_class'=>array('type' => 'select', 'value' => array(), 'label'=>'Class', 'col_num' => '2', 'mandatory'=>true),
                'from'=>array('type' => 'text', 'label'=>'From Date', 'col_num' => '2', 'id' => 'datepicker', 'mandatory'=>true),
                'to'=>array('type' => 'text', 'label'=>'To Date', 'col_num' => '2', 'id' => 'datepicker2', 'mandatory'=>true),
            );
            
           return view('admin.student-payment-overview', ['otherLinks' => array('link' => url('/').'/payment/'.$sid, 'text' => 'Go Back'), 'pageTitle' => isset($sid) && $sid != '' ? "Student's Payment Overview":'Student\'s Payment Overview', 'data' => $data, 'customFields' => $customFields, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'counter' => '1', 'sid' => $sid, 'year' => isset($year) ? $year : null] );
       

    }
    public function ajaxGetPaidDetails(Request $request)
    {
        $academic_year = $request->academic_year;
        $academic_month = $request->academic_month;
        $sid = $request->sid;
        $s_class = $request->s_class;

        //echo $academic_month." ".$academic_year;
        $getDetails = AdmissionFee::where('academic_year', $academic_year)
                                  ->where('academic_month', $academic_month)
                                  ->where('admission_id', $sid)
                                  ->where('admission_class', $s_class)
                                  ->first();
        if(isset($getDetails)){
            $getPaymentDetails = $getDetails->toArray();
            //print_r($getDetails->toArray()); exit;
            $return['json_payment'] = $getPaymentDetails['fees_master_id'];
            echo json_encode($return);
        }else{
            $return['json_payment'] = array();
            echo json_encode($return);
        }
    }
}
