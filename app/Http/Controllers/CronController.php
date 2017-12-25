<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\AdmissionFee;
use App\Supplier;
use App\Library;
use App\Voucher;
use App\FeesMaster;
use App\MoneyFlow;
use DB;

class CronController extends Controller
{
    public function calculateInout(Request $request)
    {
    	$finalBudgetOverview = array();
    	$getAdmissionDetails = AdmissionFee::select('fees_master_id')->get()->toArray();
    	$sumOfFees = 0;
    	foreach ($getAdmissionDetails as $key => $value) {
            /* All Fees ID. Get Total Money by circulating each fees ID */
    		$fees_master_id = json_decode($value['fees_master_id'], true);
            $totalFeesOfAllStudent = 0;
    		foreach ($fees_master_id as $k => $v) {
    			$getFeesDetails = FeesMaster::select('amount')->get()->toArray();
                foreach ($getFeesDetails as $key => $value) {
                    $sumOfFees += $value['amount'];
                }
                $totalFeesOfAllStudent += $sumOfFees;
    		}
    	}
        /* Here the Sum of all Fees */
        $finalBudgetOverview['fees_total'] = $totalFeesOfAllStudent;


        /* Get Voucher Total */
        $voucherTotal = 0;
        $getVoucherDetails = Voucher::select('amount', 'flow_type')->get()->toArray();
        foreach ($getVoucherDetails as $voucher) {
            if($voucher['flow_type'] == 'OUTFLOW'){
                $voucherTotal = $voucherTotal - $voucher['amount'];
            }else if($voucher['flow_type'] == 'INFLOW'){
                $voucherTotal = $voucherTotal + $voucher['amount'];
            }
        }
        /* Here the sum of all Voucher */
        $finalBudgetOverview['voucher_total'] = $voucherTotal;
        //dd($getVoucherDetails);

        MoneyFlow::truncate();
        foreach ($finalBudgetOverview as $key => $value) {
            $insertToMoneyFlow = new MoneyFlow();
            $insertToMoneyFlow->amount = $value;
            if($key == 'fees_total'){
                $type = 'Total Admission Fees';
            }else if($key == 'voucher_total'){
                $type = 'Total Voucher Fees';
            }
            $insertToMoneyFlow->type = $type;
            $insertToMoneyFlow->save();
        }
        
        $customFields['basic'] = array(
            'voucher_no'=>array('type' => 'text', 'label'=>'Voucher Number','mandatory'=>true),
            'employee_id'=>array('type' => 'select', 'label'=>'Choose Class', 'value' => array(), 'mandatory'=>true, 'class' => 'admission_class'),
        );
    	$budgets = MoneyFlow::get()->toArray();
        return view('master.money-flow', ['otherLinks' => array('link' => url('/').'#', 'text' => ''), 'customFields' => $customFields, 'budgets' => $budgets, 'formButton' => isset($sid) ? 'Update Details' : 'Save Details', 'pageTitle' => isset($sid) && $sid != '' ? 'Edit Voucher':'Money Management Overview', 'loopInit' => '1']);
    }
}
