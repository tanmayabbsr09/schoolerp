<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Helper;
use Illuminate\Support\Facades\Validator;
use App\Admission;
use App\Library;
use App\BookIssue;
use PDF;
use Carbon\Carbon;

class BookIssueController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    /*
		info : Employee/User Master
		Date ; 14-11-2017
    */
	public function bookIssue(Request $request, $eid=null)
	{
		$data[] = array();
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}

		/* Field specifiic Validations */	
		
	        $validationRules = [
	            'admission_class' => 'required',
	            'admission_id' => 'required',
	            'library_id' => 'required',
	            'from_date' => 'required',
	            'to_date' => 'required',
	            'from_date' => 'required',
	        ];
    	
    	$validator = Validator::make($request->all(), $validationRules);
       	if(isset($eid) && $eid != null){
    		$getEventAutoFillup = BookIssue::whereId($eid)->first()->toArray();
    		$admission_class = $getEventAutoFillup['admission_class'];
    		$first_position = $second_position = $third_position = Admission::where('admission_class', $admission_class)->pluck('name', 'id');
    	}else{
    		$getEventAutoFillup = array();
    		$first_position = $second_position = $third_position = array();
    	}
		/* Submit all data */
    	if ($request->isMethod('post')){
    		if($validator->fails()){
            	return redirect('/book-issue')->withErrors($validator)->withInput();
            }else{
            	if(isset($request->id) && $request->id != null){
		    		/* 
						Update
		    		*/
		    		$updateStudent = BookIssue::find($request->id);
		    		if(isset($request->_token) && $request->_token != ''){
		    			unset($request['_token']);
		    			unset($request['event_name2']);
		    			unset($request['event_name3']);
		    		}
		    		//dd($request->toArray());
		    		if(BookIssue::where([['id', '=', $request->id]])->update($request->toArray())){
		    			$request->session()->flash('message.level', 'info');
		    			$request->session()->flash('message.content', 'Student Details are Updated Successfully !');
		    		}else{
		        		session()->flash('status', ['danger', 'Addition was Failed!']);
		        	}
		        	return redirect('/book-issue-edit/'.$request->id);
		    	}else{
		    		$saveBookIssue = $request->toArray();
		    		$saveBookIssue = new BookIssue($saveBookIssue);

		    		if($saveBookIssue->save()){
		        		$request->session()->flash('message.level', 'success');
		    			$request->session()->flash('message.content', 'BookIssue was successfully added!');
		        	}else{
		        		session()->flash('status', ['danger', 'Addition of BookIssue Failed!']);
		        	}
		        	return redirect('/book-issue');
		        }
            }
			
    	}

		$customFields['basic'] = array(
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class'),
			'admission_id'=>array('type' => 'select', 'label'=>'Choose Student', 'value' => array(), 'mandatory'=>true, 'class' => 'admission_id'),
			'library_id'=>array('type' => 'select', 'label'=>'Choose Book', 'value' => array(), 'mandatory'=>true, 'class' => 'library_id'),
			'from_date'=>array('type' => 'text', 'label'=>'Issued from', 'mandatory'=>true, 'id' => 'datepicker-pre-past'),
			'to_date'=>array('type' => 'text', 'label'=>'Issued till', 'mandatory'=>true, 'class' => 'to_datepicker', 'id' => 'datepicker'),
			/*'return_date'=>array('type' => 'text', 'label'=>'Return Date', 'mandatory'=>false, 'class' => 'return_date', 'id' => 'datepicker-pre-past'),
			'is_any_damage'=>array('type' => 'select', 'label'=>'Is Any Damage?', 'value' => array('0' => 'No', '1' => 'Yes'), 'mandatory'=>false, 'class' => 'is_any_damage'),

			'total_fine'=>array('type' => 'text', 'label'=>'Total Fine', 'mandatory'=>false, 'class' => 'total_fine'),*/
			'remark'=>array('type' => 'text', 'label'=>'Remark(if any)', 'mandatory'=>false, 'class' => 'remark'),
    	);
		
		return view('admin.book-issue', ['otherLinks' => array('link' => url('/').'/book-issue-list', 'text' => 'Issued Books\' List'), 'customFields' => $customFields, 'data' => $data, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit a Issued Book':'Issue a Book', 'first_position' => $first_position])->with($getEventAutoFillup);
	}
	public function bookissueListing($id = null)
	{
		/* Generate List of Available CLasses */
		$classList = array('' => '-- Select --', 'Nursery' => 'Nursery', 'LKG' => 'LKG', 'UKG' => 'UKG');
		for($i = 1; $i < 13; $i ++){
			$classList[$i] = 'STD - '.$this->integerToRoman($i);
		}
		$events = BookIssue::paginate(15);
		$customFields['basic'] = array(
			'event_name'=>array('type' => 'text', 'label'=>'Event Name','mandatory'=>true),
			'admission_class'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => $classList, 'mandatory'=>true, 'class' => 'admission_class'),
    	);

    	if(isset($id) && $id !=''){
    		$data['schoolInfo'] = Helper::schoolInfo();
    		$data['studentInfo'] = Helper::getStudentDetailsFromLibraryId($id);
    		$data['bookInfo'] = Helper::getBookInfo($id);
    		$data['getBookIssueDetails'] = BookIssue::where('book_issues.id', $id)
						->join('libraries', 'libraries.id' , '=', 'book_issues.library_id')
						->select('book_issues.*', 'libraries.raw_book_name', 'libraries.invoice')
						->first()->toArray();

        	$pdf = PDF::loadView('pdf.book-issue-invoice', ['data' => $data]);
        	//return view('pdf.book-issue-invoice', ['data' => $data]);
			return $pdf->download('Student-Book-Issue-Invoice-'.date('Ymdhis').'.pdf');
        }else{
    		return view('master.book-issue-listing', ['otherLinks' => array('link' => url('/').'/book-issue', 'text' => 'Issue a Book'), 'pageTitle' => 'Issued Books\' List', 'events' => $events, 'customFields' => $customFields, 'loopInit' => '1']);
    	}


		
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
	public function saveReturnDate(Request $request)
	{
		$updateBookReturn = BookIssue::where('id', $request->issueid)->update(
							    array(
							    	'is_paid' 		=> 1,
							    	'paid_date' 	=> $request->return_date,
							    	'total_delays' 	=> $request->total_delays,
							    	'total_fine'	=> $request->total_fine,
							    	'is_any_damage'	=> $request->is_any_damage
							    )
						    );
		echo 'done';
	}
}
