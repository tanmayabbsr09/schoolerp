<?php
namespace App\Helpers;
use App\FeesCategory;
use App\FeesSubcategory;
use App\Admission;
use DB;
use App\AdmissionFee;
use App\FeesMaster;
use App\Library;
use App\BookIssue;
use Carbon\Carbon;

class Helper
{
	/*
		School's basic Information
	*/
	public static function schoolInfo(){
		$schoolInfo['name'] = 'St. Xavier\'s High School';
		$schoolInfo['logo'] = 'public/school-logo.png';
		$schoolInfo['address'] = 'Kedargouri, bhubaneswar';
		$schoolInfo['phone'] = '9090906655/9966335544';
		$schoolInfo['email'] = 'st.xavierskedargouri@gmail.com';
		return $schoolInfo;
	}
	/* 
		Info : Find out if a Student is cleared his/her Annual paymnets or not ... 
		Date : 26-11-2017
	*/
	public static function getStudentPaymnetStatus($admId = null, $admCls = null, $admYr = null){
		/* Get all list of Paid data according to Year */
        $getPaidAllMonthDetails =AdmissionFee::where('academic_year', $admYr)
        						->where('admission_id', $admId)
        						->get()->toArray();
		if(isset($getPaidAllMonthDetails[0]['admission_class'])){        						
	        $admClass = $getPaidAllMonthDetails[0]['admission_class'];
	 
	        $getMandatoryFees = FeesMaster::where('class', $admClass)
	                            ->where('is_mandatory', 1)
	                            ->pluck('amount', 'id')->toArray();
	        /* Loop through to get Mandatory Paymnets IDs */
	        $getMandatoryFeesIds = array_keys($getMandatoryFees);
	     
	        /* Get Months that are paid */
	        $paymnetDetails = $paymentProcessedMonths = array();
	        foreach ($getPaidAllMonthDetails as $key => $payment) {
	            $paymnetDetails[$key]['academic_year'] = $payment['academic_year'];
	            $paymnetDetails[$key]['academic_month'] = $paymentProcessedMonths[] = $payment['academic_month'];
	            $paymnetDetails[$key]['admission_class'] = $payment['admission_class'];
	            
	            $feesIds = json_decode($payment['fees_master_id'], true);
	            $notPaidMandPaymentIds = array_diff($getMandatoryFeesIds, $feesIds);
	            $paymnetDetails[$key]['payment_pendings'] = $notPaidMandPaymentIds;
	            
	        }
	        
	        $return['pending_months'] = $paymentProcessedMonths;
	        $return['pending_payment_details'] = $paymnetDetails;
	        $returnJson = json_encode($return);
    	}else{
    		$return = array();
    		$returnJson = json_encode($return);
    	}
        return $returnJson;
	}
	public static function getStudentDetails($sid='', $fields=[])
	{
		if(isset($sid) && $sid !=''){
			$adm = DB::table('admissions')->where('id', $sid)->get($fields)->first();
			return $adm;
		}else{
			return 'Error Occured during selecting a Student !';
		}
	}
	public static function getStudentDetailsFromLibraryId($biid='')
	{
		if(isset($biid) && $biid !=''){
			//$adm = DB::table('book_issue')->where('id', $biid)->get('admission_id')->first();
			$adm = BookIssue::where('id', $biid)->pluck('admission_id')->toArray();
			$admission_id = $adm[0];
			$amds = Helper::getStudentDetails($admission_id, ['admission_class', 'name']);
			return $amds;
		}else{
			return 'Error Occured during selecting a Student !';
		}
	}
	public static function listAllMonths(){
        $months = array(
		    'APR04' => 'April',
		    'MAY05' => 'May',
		    'JUN06' => 'June',
		    'JUL07' => 'July',
		    'AUG08' => 'August',
		    'SEP09' => 'September',
		    'OCT10' => 'October',
		    'NOV11' => 'November',
		    'DEC12' => 'December',
		    'JAN01' => 'January',
		    'FEB02' => 'February',
		    'MAR03' => 'March'
		);
        return $months;
	}
	public static function listAllYears($count=6){
		/* generate Year Range for Academic Year */
        $max_year = date('Y');
        $current_year = $max_year - $count;
        for($i = $current_year; $i <= $max_year; $i++){
            $year_array[$i.'-'.substr( ($i+1), -2)] = $i.'-'.substr( ($i+1), -2);
        }
        return array_reverse($year_array);
	}
    public static function integerToRoman($integer)
	{
	 // Convert the integer into an integer (just to make sure)
	 $integer = intval($integer);
	 $result = '';
	 
	 // Create a lookup array that contains all of the Roman numerals.
	 $lookup = array('M' => 1000,
	 'CM' => 900,
	 'D' => 500,
	 'CD' => 400,
	 'C' => 100,
	 'XC' => 90,
	 'L' => 50,
	 'XL' => 40,
	 'X' => 10,
	 'IX' => 9,
	 'V' => 5,
	 'IV' => 4,
	 'I' => 1);
	 
	 foreach($lookup as $roman => $value){
	  // Determine the number of matches
	  $matches = intval($integer/$value);
	 
	  // Add the same number of characters to the string
	  $result .= str_repeat($roman,$matches);
	 
	  // Set the integer to be the remainder of the integer and the value
	  $integer = $integer % $value;
	 }
	 
	 // The Roman numeral should be built, return it
	 return $result;
	}
	public static function displaywords($number){
        $words = array('0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
        $digits = array('', '', 'hundred', 'thousand', 'lakh', 'crore');
       
        $number = explode(".", $number);
        $result = array("","");
        $j =0;
        foreach($number as $val){
            // loop each part of number, right and left of dot
            for($i=0;$i<strlen($val);$i++){
                // look at each part of the number separately  [1] [5] [4] [2]  and  [5] [8]
                
                $numberpart = str_pad($val[$i], strlen($val)-$i, "0", STR_PAD_RIGHT); // make 1 => 1000, 5 => 500, 4 => 40 etc.
                if($numberpart <= 20){
                    $numberpart = substr($val, $i,2);
                    $i++;
                    $result[$j] .= $words[$numberpart] ." ";
                }else{
                    //echo $numberpart . "<br>\n"; //debug
                    if($numberpart > 90){  // more than 90 and it needs a $digit.
                        $result[$j] .= $words[$val[$i]] . " " . $digits[strlen($numberpart)-1] . " "; 
                    }else if($numberpart != 0){ // don't print zero
                        $result[$j] .= $words[str_pad($val[$i], strlen($val)-$i, "0", STR_PAD_RIGHT)] ." ";
                    }
                }
            }
            $j++;
        }
        if(trim($result[0]) != "") echo $result[0] . "Rupees ";
        if($result[1] != "") echo $result[1] . "Paise";
        echo " Only";
    }
    public static function moneyFormatIndia($num) {
	    $explrestunits = "" ;
	    if(strlen($num)>3) {
	        $lastthree = substr($num, strlen($num)-3, strlen($num));
	        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
	        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
	        $expunit = str_split($restunits, 2);
	        for($i=0; $i<sizeof($expunit); $i++) {
	            // creates each of the 2's group and adds a comma to the end
	            if($i==0) {
	                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
	            } else {
	                $explrestunits .= $expunit[$i].",";
	            }
	        }
	        $thecash = $explrestunits.$lastthree;
	    } else {
	        $thecash = $num;
	    }
	    return $thecash; // writes the final format where $currency is the currency symbol.
	}
    public static function getCategoryName($catId){
    	$cat = FeesCategory::find($catId);
    	return $cat->category_name;
    }
    public static function getSubCategoryName($subCatId){
    	$subCat = FeesSubcategory::find($subCatId);
    	return $subCat->subcategory_name;
    }
    public static function getStudentInfo($sid){
		$student = Admission::where('id', $sid)->first();
    	return $student['name'];
    }
    public static function getBookInfo($bid, $type = null){
    	$data = Library::where('id', $bid)->first();
    	if(!isset($type) && $type == null){
	    	return $data['raw_book_name'];
    	}else{
    		return $data->toArray();
    	}
    }
    public static function getBookIssueInfo($sid){
		$student = BookIssue::where('id', $sid)
					->join('libraries', 'libraries.id' , '=', 'book_issues.library_id')
					->first();
    	return $student;
    }
    public static function changeDateFormat($format='d-m-Y', $originalDate){
    	return $newDate = date($format, strtotime($originalDate));
    }
    public static function dateDiff($from, $to, $unit = 'days'){
		$startTime = Carbon::parse($from);
		$finishTime = Carbon::parse($to);
		if($unit == 'days'){
			return $totalDuration = $finishTime->diffInDays($startTime)+1;
		}else if($unit == 'hours'){
			return $totalDuration = $finishTime->diffInHours($startTime);
		}else if($unit = 'minutes'){
			return $totalDuration = $finishTime->diffInMinutes($startTime);
		}
    }
    /*public static function date_diff_array(DateTime $oDate1, DateTime $oDate2) {
	    $aIntervals = array(
	        'year'   => 0,
	        'month'  => 0,
	        'week'   => 0,
	        'day'    => 0,
	        'hour'   => 0,
	        'minute' => 0,
	        'second' => 0,
	    );

	    foreach($aIntervals as $sInterval => &$iInterval) {
	        while($oDate1 <= $oDate2){ 
	            $oDate1->modify('+1 ' . $sInterval);
	            if ($oDate1 > $oDate2) {
	                $oDate1->modify('-1 ' . $sInterval);
	                break;
	            } else {
	                $iInterval++;
	            }
	        }
	    }

	    return $aIntervals;
	}*/
}