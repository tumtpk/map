<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserType;
use App\Village;
use Illuminate\Support\Facades\DB;
use App\Patient;

class MapController extends Controller
{
	const CENTER_COORD = ["99.890998", "8.665091"];
	const DEFAULT_ZOOM = 14;
	const DEFAULT_PROVCODE =  "80"; // Nakhonsithammarat
	const DEFAULT_DISTCODE = "08"; // Thasala
	const DEFAULT_SUBDISTCODE = "09"; // Thaiburi
	
    public function index(Request $request)
    {	
    	$select = $request->input('village');
    	$homeNo = $request->input('homeNo');
    	$firstname = $request->input('firstname');
    	$lastname = $request->input('lastname');
    	
    	$villages = Village::all()->sortBy('village');
		
    	return view('map.index')->with('villages',$villages)->with('select', $select)->with('homeNo', $homeNo)
    		->with('firstname', $firstname)->with('lastname', $lastname)->with('evoluationPart', $this->getEvoluationPart())
    		->with('evoluationForm', $this->getEvoluationForm());
    }
     
    public function getEvoluationPart()
    {
    	$evoluationPart = DB::table('evoluation_part')->get();
    	return $evoluationPart;
    }
    
    public function getEvoluationForm()
    {
    	$evoluationForm = DB::table('evoluation_form')->get();
    	$evoluationPart = $this->getEvoluationPart();
    	
    	//crate array menu part
    	$arrEvoluationForm = [];
    	foreach ($evoluationPart as $part){
    		$arrEvoluationForm[$part->id] = [];
    	}
    	
    	foreach ($evoluationForm as $obj){
    		$arrForm = [];
    		$arrForm['id'] = $obj->id;
    		$arrForm['name'] = $obj->name;
    		array_push($arrEvoluationForm[$obj->part_id], $arrForm);
    	}
    	return $arrEvoluationForm;
    }
    
    public function evaluation($formId){
    	dd($formId);
    }
      
}
