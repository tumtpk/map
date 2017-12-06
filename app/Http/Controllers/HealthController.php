<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserType;
use App\Village;
use Illuminate\Support\Facades\DB;
use App\Patient;
use App\AppDefault;

class HealthController extends Controller
{
	
	public function volunteer(Request $request){
		
		$village = $request->input('village');
		$firstname = $request->input('firstname');
		$lastname = $request->input('lastname');
		
		$villages = AppDefault::getArrayVillage();
		$coord = Village::where('village', '=', $village)->first();
		 
		$edgeCoord;
		$stringLocation = "";
		$isNotSelect;
		$arrEdgeCoords = [];
		$arrCenterCoords = [];
		$centerCoord = AppDefault::CENTER_COORD;
		$arrEdgeCoord = [];
		$zoom = AppDefault::DEFAULT_ZOOM;
		$people = [];
		$datapatient = json_encode([]);
		$color = "";
		$arrVillage = [];
		$volunteer = [];
		
			
		if($coord != null){
			$isNotSelect = false;
			$volunteer = AppDefault::getVolunteer($village, $firstname, $lastname);
			$zoom = 15;
			$stringLocation = AppDefault::getStringLocation();
			$edgeCoord = $coord->edgecoord;
			$centerCoord = explode(",", $coord->centercoord);
			$color = $coord->color;
			if($coord->edgecoord != ""){
				$arrEdgeCoord = AppDefault::splitLatLng($edgeCoord);
			}
			$datapatient = AppDefault::getPatientByVolunteer($village, $firstname, $lastname);
		}else{
			$isNotSelect = true;
			$local = AppDefault::getArrayVillage();
		
			$index = 0;
			foreach ($local as $obj){
				$arrEdgeCoords[$index] = AppDefault::splitLatLng($obj->edgecoord);
				$arrCenterCoords[$index] = AppDefault::splitCenterLatLng($obj->centercoord);
				$color[$index] = $obj->color;
				$arrVillage[$index] = $obj->village;
				$index++;
			}
		}
		
		
		return view('health.volunteer')->with('evoluationPart', AppDefault::getEvoluationPart())->with('evoluationForm', AppDefault::getEvoluationForm())
			->with('villages', $villages)->with('select', $village)->with('firstname', $firstname)->with('lastname', $lastname)->with('zoom', $zoom)
			->with('volunteer', $volunteer)->with('centerCoord', $centerCoord)->with('edgeCoord', json_encode($arrEdgeCoord))->with('color', json_encode($color))
			->with('isNotSelect', json_encode($isNotSelect))->with('arrEdgeCoords', json_encode($arrEdgeCoords))->with('arrVillage', json_encode($arrVillage))
			->with('arrCenterCoord', json_encode($arrCenterCoords))->with('dataPatient', json_encode($datapatient))->with('stringLocation', $stringLocation);
		
	}
	
	public function evoluation(Request $request, $formId)
    {	
    	
    	$village = $request->input('village');
    	$homeNo = $request->input('homeNo');
    	$firstname = $request->input('firstname');
    	$lastname = $request->input('lastname');
    	 
    	$villages = AppDefault::getArrayVillage();
    	$arrHomeNo = $this->getHome($village, $homeNo, $firstname, $lastname);
    	
    	$formName = AppDefault::getFormName($formId);
    	$numberOfHomeNo = $arrHomeNo->count();
    	
    	$coord = Village::where('village', '=', $village)->first();
    	
    	$edgeCoord;
    	$stringLocation = "";
    	$isNotSelect;
    	$arrEdgeCoords = [];
    	$arrCenterCoords = [];
    	$centerCoord = AppDefault::CENTER_COORD;
    	$arrEdgeCoord = [];
    	$zoom = AppDefault::DEFAULT_ZOOM;
    	$people = [];
    	$datapatient = json_encode([]);
    	$color = "";
    	$arrVillage = [];
    	
    	if($coord != null){
    		$stringLocation = AppDefault::getStringLocation();
    		$isNotSelect = false;
    		$edgeCoord = $coord->edgecoord;
    		$centerCoord = explode(",", $coord->centercoord);
    		$color = $coord->color;
    		if($coord->edgecoord != ""){
    			$arrEdgeCoord = AppDefault::splitLatLng($edgeCoord);
    		}
    		$zoom = 15;
    		$datapatient = AppDefault::getPatientByForm($village, $firstname, $lastname, $homeNo, $formId);
    	}else{
    		$isNotSelect = true;
    		$local = AppDefault::getArrayVillage();
    		
    		$index = 0;
    		foreach ($local as $obj){
    			$arrEdgeCoords[$index] = AppDefault::splitLatLng($obj->edgecoord);
    			$arrCenterCoords[$index] = AppDefault::splitCenterLatLng($obj->centercoord);
    			$color[$index] = $obj->color;
    			$arrVillage[$index] = $obj->village;
    			$index++;
    		}
    	}

    	return view('health.evoluation')->with('villages',$villages)->with('select', $village)->with('homeNo', $homeNo)
    		->with('firstname', $firstname)->with('lastname', $lastname)->with('evoluationPart', AppDefault::getEvoluationPart())
    		->with('evoluationForm', AppDefault::getEvoluationForm())->with('arrHomeNo', $arrHomeNo)->with('numberOfHomeNo',$numberOfHomeNo)
    		->with('formName', $formName[0])->with('isNotSelect', json_encode($isNotSelect))->with('arrEdgeCoords', json_encode($arrEdgeCoords))
    		->with('color', json_encode($color))->with('arrVillage', json_encode($arrVillage))->with('edgeCoord', json_encode($arrEdgeCoord))
    		->with('centerCoord',$centerCoord)->with('zoom', $zoom)->with('arrCenterCoord', json_encode($arrCenterCoords))->with('dataPatient', $datapatient)
    		->with('stringLocation', $stringLocation);
    }
    
    public function index(Request $request)
    {	
    	$village = $request->input('village');
    	$homeNo = $request->input('homeNo');
    	$firstname = $request->input('firstname');
    	$lastname = $request->input('lastname');
    	
    	$villages = Village::all()->sortBy('village');
    	$coord = Village::where('village', '=', $village)->first();
    	$isNotSelect;$color;
    	$datapatient = json_encode([]);
    	$arrEdgeCoord = [];
    	$arrEdgeCoords = [];
    	$arrVillage = [];
    	$arrCenterCoords = [];
    	
    	$zoom = AppDefault::DEFAULT_ZOOM;
    	$centerCoord = AppDefault::CENTER_COORD;
    	
    	$arrHomeNo = $this->getHome($village, $homeNo, $firstname, $lastname);
    	$numberOfHomeNo =  sizeof($arrHomeNo);
    	
    	if($coord != null){
    		$isNotSelect = false;
    		$edgeCoord = $coord->edgecoord;
    		$centerCoord = explode(",", $coord->centercoord);
    		$color = $coord->color;
    		if($coord->edgecoord != ""){
    			$arrEdgeCoord = AppDefault::splitLatLng($edgeCoord);
    		}
    		$zoom = 15;
    		$datapatient = $this->getPatient($village, $firstname, $lastname, $homeNo);
    	}else{
    		$isNotSelect = true;
    		$local = AppDefault::getArrayVillage();
    		
    		$index = 0;
    		foreach ($local as $obj){
    			$arrEdgeCoords[$index] = AppDefault::splitLatLng($obj->edgecoord);
    			$arrCenterCoords[$index] = AppDefault::splitCenterLatLng($obj->centercoord);
    			$color[$index] = $obj->color;
    			$arrVillage[$index] = $obj->village;
    			$index++;
    		}
    	}
    	
    	return view('health.index')->with('villages',$villages)->with('select', $village)->with('homeNo', $homeNo)
    		->with('firstname', $firstname)->with('lastname', $lastname)->with('evoluationPart', AppDefault::getEvoluationPart())
    		->with('evoluationForm', AppDefault::getEvoluationForm())->with('arrHomeNo', $arrHomeNo)->with('numberOfHomeNo',$numberOfHomeNo)
    		->with('zoom',$zoom)->with('centerCoord', $centerCoord)->with('edgeCoord', json_encode($arrEdgeCoord))->with('isNotSelect', json_encode($isNotSelect))
    		->with('color', json_encode($color))->with('arrEdgeCoords', json_encode($arrEdgeCoords))->with('arrVillage', json_encode($arrVillage))
    		->with('arrCenterCoord', json_encode($arrCenterCoords))->with('dataPatient', json_encode($datapatient))->with('stringLocation', AppDefault::getStringLocation());
    }
    
    public function getPatient($village, $firstname, $lastname, $homeNo){
    	$currentYear = date("Y-mm-dd")+543;
    	 
    	// people in vilage
    	$people = DB::table('patient')
    	->join('house','patient.HomeNo', '=', 'house.hno')
    	->where('patient.Village', '=', $village)
    	->where('house.villcode', '=', ((int)$village < 10)?'8008090'.$village: '800809'.$village)
    	->where('patient.HomeNo', 'like', ($homeNo==null)?'%':'%'.$homeNo.'%')
    	->where('patient.patient.Firstname', 'like', ($firstname==null)?'%':'%'.$firstname.'%')
    	->where('Sirname', 'like', ($lastname==null)?'%':'%'.$lastname.'%')
    	->orderBy('patient.HomeNo')
    	->get();
    	 
    	$data = [];
    	$index = 0;
    	$homeno = null;
    	$first = true;
    	foreach ($people as $obj){
    		$begin = date_create($obj->Birthday);
    		$last = date_create($currentYear);
    		$interval = date_diff($begin, $last);
    
    		if(!$first){
    			if($homeno != $obj->HomeNo){
    				$index = 0;
    			}
    		}else{
    			$first = false;
    		}
    
    		$data[$obj->HomeNo][$index]['name'] = $obj->Firstname." ".$obj->Sirname;
    		$data[$obj->HomeNo][$index]['birthday'] = $obj->Birthday;
    		$data[$obj->HomeNo][$index]['age'] = intval($interval->format('%R%a')/365);
    		$index++;
    		$homeno = $obj->HomeNo;
    	}
    	return $data;
    }
    
    
    public function getHome($village, $homeNo, $firstname, $lastname){
    	$strVillage = AppDefault::DEFAULT_PROVCODE.AppDefault::DEFAULT_DISTCODE.AppDefault::DEFAULT_SUBDISTCODE;
    	return $people = DB::table('patient')
	    	->join('house','patient.HomeNo', '=', 'house.hno')
	    	->where('patient.Village', '=', $village)
	    	->where('house.villcode', '=', ((int)$village < 10)?$strVillage.'0'.$village: $strVillage.$village)
	    	->where('patient.HomeNo', 'like', ($homeNo==null)?'%':'%'.$homeNo.'%')
	    	->where('patient.patient.Firstname', 'like', ($firstname==null)?'%':'%'.$firstname.'%')
	    	->where('Sirname', 'like', ($lastname==null)?'%':'%'.$lastname.'%')
	    	->orderBy('patient.HomeNo')
	    	->groupBy('patient.HomeNo')
	    	->get();
    }
    
    public function behavior(Request $request){
    	$villages = AppDefault::getArrayVillage();
    	$village = $request->input('village');
    	return view('health.behavior')->with('evoluationPart', AppDefault::getEvoluationPart())->with('evoluationForm', AppDefault::getEvoluationForm())
    		->with('villages', $villages)->with('select', $village);
    }
    
}
