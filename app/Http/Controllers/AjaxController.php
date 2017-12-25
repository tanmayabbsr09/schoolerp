<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Admission;
use App\Library;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
		##################################################################
		-------------------------- AJAX PARTS ----------------------------
		##################################################################
	*/
	public function ajaxGetStudentListByStudentId(Request $request)
	{
		$admission_class = $request->admission_class;
		$getAllStudents = Admission::where('admission_class', $admission_class)->pluck('name', 'id');
		
		echo json_encode($getAllStudents);
	}
	public function ajaxGetBookListByClassId(Request $request)
	{
		$admission_class = $request->admission_class;
		$getAllBooks = Library::where('admission_class', $admission_class)->pluck('raw_book_name', 'id');
		
		echo json_encode($getAllBooks);
	}
}
