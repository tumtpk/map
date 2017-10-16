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
	const CIGARETTE_NEVER = 1;
	const CIGARETTE_RARELY = 2;
	const CIGARETTE_HURLY = 3;
	const CIGARETTE_ONCE = 4;
	
	const DRINK_NEVER = 1;
	const DRINK_RARELY = 2;
	const DRINK_HURLY = 3;
	const DRINK_ONCE = 4;
	
	public function getCigaratte(){
		return array(
				self::CIGARETTE_NEVER => "ไม่สูบ",
				self::CIGARETTE_RARELY => "สูบบ้าง",
				self::CIGARETTE_HURLY => "สูบประจำ",
				self::CIGARETTE_ONCE => "เคยสูบ"
		);
	}
    
	public function getDrink(){
		return array(
				self::DRINK_NEVER => "ไม่ดื่ม",
				self::DRINK_RARELY => "ดื่มบ้าง",
				self::DRINK_HURLY => "ดื่มประจำ",
				self::DRINK_ONCE => "เคยดื่ม"
		);
	}
	
    public function index(Request $request)
    {	
    	$select = $request->input('village');
    	$homeNo = $request->input('homeNo');
    	$firstname = $request->input('firstname');
    	$lastname = $request->input('lastname');
    	
		$villages = Village::all()->sortBy('village');
		
		$coord = Village::where('village', '=', $select)->first();
		
		$edgeCoord;
		$isNotSelect;
		$arrEdgeCoords = [];
		$arrCenterCoords = [];
		$centerCoord = ["99.890998", "8.665091"];
		$arrEdgeCoord = [];
		$zoom = 14;
		$number = 0;
		$people = [];
		$province = '';
		$district = '';
		$subdistrict = '';
		$datapatient = json_encode([]);
		$color = "";
		$arrVillage = [];
		if($coord != null){
			$isNotSelect = false;
			$edgeCoord = $coord->edgecoord;
			$centerCoord = explode(",", $coord->centercoord);
			$color = $coord->color;
			if($coord->edgecoord != ""){
				$arrEdgeCoord = $this->splitLatLng($edgeCoord);
			}
			$zoom = 15;
			
			// people in vilage
			$people = DB::table('patient')
				->join('house','patient.HomeNo', '=', 'house.hno')
				->where('patient.Village', '=', $select)
				->where('house.villcode', '=', ((int)$select < 10)?'8008090'.$select: '800809'.$select)
				->where('patient.HomeNo', 'like', ($homeNo==null)?'%':'%'.$homeNo.'%')
				->where('patient.patient.Firstname', 'like', ($firstname==null)?'%':'%'.$firstname.'%')
				->where('Sirname', 'like', ($lastname==null)?'%':'%'.$lastname.'%')
				->orderBy('patient.HomeNo')
				->groupBy('patient.HomeNo')
				->get();
			
			$number = $people->count();
			
			//location of group people
			$province = DB::table('cprovince')->where('provcode', '80')->value('provname');
			$district = DB::table('cdistrict')->where('provcode', '80')->where('distcode','08')->value('distname');
			$subdistrict = DB::table('csubdistrict')->where('provcode', '80')->where('distcode','08')->where('subdistcode','09')->value('subdistname');
		
			$datapatient = $this->getPatient($select, $firstname, $lastname, $homeNo);
		}else{
			$isNotSelect = true;
			$local = DB::table('villages')
				->select('edgecoord','color','village','centercoord')
				->where('provcode', '80')
				->where('distcode', '08')
				->where('subdistcode', '09')
				->get();
			
			$index = 0;
			foreach ($local as $obj){
				$arrEdgeCoords[$index] = $this->splitLatLng($obj->edgecoord);
				$arrCenterCoords[$index] = $this->splitCenterLatLng($obj->centercoord);
				$color[$index] = $obj->color;
				$arrVillage[$index] = $obj->village;
				$index++;
			}
		}
		
    	return view('map.index')->with('villages',$villages)->with('select', $select)->with('edgeCoord', json_encode($arrEdgeCoord))->with('centerCoord',$centerCoord)->with('zoom', $zoom)->with('number', $number)->with('people', $people)
    		->with('province',$province)->with('district',$district)->with('subdistrict',$subdistrict)->with('dataPatient', $datapatient)->with('homeNo',$homeNo)->with('firstname',$firstname)->with('lastname',$lastname)
    		->with('arrEdgeCoords', json_encode($arrEdgeCoords))->with('isNotSelect', json_encode($isNotSelect))->with('color', json_encode($color))->with('arrVillage', json_encode($arrVillage))->with('arrCenterCoord', json_encode($arrCenterCoords));
    }
    
    public function splitLatLng($strLatLng){
    	$index = 0;
    	$arrEdgeCoord = [];
    	$tempEdgeCoord = explode(" ", $strLatLng);
    	foreach ($tempEdgeCoord as $key => $value) {
    		$l = explode(",", $value);
    		$arrEdgeCoord[$index]["lat"] = $l[1];
    		$arrEdgeCoord[$index]["lng"] = $l[0];
    		$index++;
    	}
    	return $arrEdgeCoord;
    }
    
    public function splitCenterLatLng($strLatLng){
    	$l = explode(",", $strLatLng);
    	$arrCenterCoord["lat"] = $l[1];
    	$arrCenterCoord["lng"] = $l[0];
    	return $arrCenterCoord;
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
    	return json_encode($data);
    }
    
    public function getPatientByBehavior($select, $pastillness, $historysurgery, $congenital){
    	$currentYear = date("Y-mm-dd")+543;
    	 
    	$query = DB::table('patient')
			->join('house','patient.HomeNo', '=', 'house.hno')
			->where('patient.Village', '=', $select)
			->where('house.villcode', '=', ((int)$select < 10)?'8008090'.$select: '800809'.$select);
			if($pastillness!=null){
				$query->where('patient.Pastillness', 'like', '%'.$pastillness.'%');
			}
			if($historysurgery != null){
				$query->where('patient.Historysurgery', 'like', '%'.$historysurgery.'%');
			}
			if($congenital != null){
				$query->where('patient.Congenital', 'like', '%'.$congenital.'%');
			}
				
    	$query->orderBy('patient.HomeNo');
			
    	$people = $query->get();
    	 
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
    		
    		$arrCigarette = $this->getCigaratte();
    		$arrDrink = $this->getDrink();
    		
    		$data[$obj->HomeNo][$index]['name'] = $obj->Firstname." ".$obj->Sirname;
    		$data[$obj->HomeNo][$index]['age'] = intval($interval->format('%R%a')/365);
    		$data[$obj->HomeNo][$index]['pastillness'] = ($obj->Pastillness == null)?'-':$obj->Pastillness;
    		$data[$obj->HomeNo][$index]['historysurgery'] = ($obj->Historysurgery == null)?'-':$obj->Historysurgery;
    		$data[$obj->HomeNo][$index]['congenital'] = ($obj->Congenital == null)?'-':$obj->Congenital;
    		$data[$obj->HomeNo][$index]['cigarette'] = ($obj->Cigarette == null)?'-':$arrCigarette[$obj->Cigarette]; 
    		$data[$obj->HomeNo][$index]['drink'] = ($obj->Drink == null)?'-':$arrDrink[$obj->Drink];
    		$index++;
    		$homeno = $obj->HomeNo;
    	}
    	return json_encode($data);
    }
    
    public function behavior(Request $request){
    	$select = $request->input('village');
    	$pastillness = $request->input('pastillness');
    	$historysurgery = $request->input('historysurgery');
    	$congenital = $request->input('congenital');
    	$cigarette = (int)$request->input('cigarette');
    	$drink = (int)$request->input('drink');
    	
		$villages = Village::all()->sortBy('village');
		
		$coord = Village::where('village', '=', $select)->first();
		
		$edgeCoord;
		$isNotSelect;
		$arrEdgeCoords = [];
		$centerCoord = ["99.890998", "8.665091"];
		$arrEdgeCoord = [];
		$arrCenterCoords = [];
		$zoom = 14;
		$number = 0;
		$people = [];
		$province = '';
		$district = '';
		$subdistrict = '';
		$datapatient = json_encode([]);
		$color;
		$arrVillage = [];
		if($coord != null){
			$isNotSelect = false;
			$edgeCoord = $coord->edgecoord;
			$centerCoord = explode(",", $coord->centercoord);
			$color = $coord->color;
			
			if($coord->edgecoord != ""){
				$arrEdgeCoord = $this->splitLatLng($edgeCoord);
			}
			$zoom = 15;
			
			// people in vilage
			$query = DB::table('patient')
				->join('house','patient.HomeNo', '=', 'house.hno')
				->where('patient.Village', '=', $select)
				->where('house.villcode', '=', ((int)$select < 10)?'8008090'.$select: '800809'.$select);
				if($pastillness!=null){
					$query->where('patient.Pastillness', 'like', '%'.$pastillness.'%');
				}
				if($historysurgery != null){
					$query->where('patient.Historysurgery', 'like', '%'.$historysurgery.'%');
				}
				if($congenital != null){
					$query->where('patient.Congenital', 'like', '%'.$congenital.'%');
				}
				if($cigarette != null){
					$query->where('patient.Cigarette', $cigarette);
				}
				if($drink != null){
					$query->where('patient.Drink', $drink);
				}
    		$query->orderBy('patient.HomeNo')
				->groupBy('patient.HomeNo');
			
    		$people = $query->get();
			$number = $people->count();
			
			//location of group people
			$province = DB::table('cprovince')->where('provcode', '80')->value('provname');
			$district = DB::table('cdistrict')->where('provcode', '80')->where('distcode','08')->value('distname');
			$subdistrict = DB::table('csubdistrict')->where('provcode', '80')->where('distcode','08')->where('subdistcode','09')->value('subdistname');
		
			$datapatient = $this->getPatientByBehavior($select, $pastillness, $historysurgery, $congenital);
		}else{
			$isNotSelect = true;
			$local = DB::table('villages')
				->select('edgecoord','color','village','centercoord')
				->where('provcode', '80')
				->where('distcode', '08')
				->where('subdistcode', '09')
				->get();
			
			$index = 0;
			foreach ($local as $obj){
				$arrEdgeCoords[$index] = $this->splitLatLng($obj->edgecoord);
				$arrCenterCoords[$index] = $this->splitCenterLatLng($obj->centercoord);
				$color[$index] = $obj->color;
				$arrVillage[$index] = $obj->village;
				$index++;
			}
		}
		
    	return view('map.behavior')->with('villages',$villages)->with('select', $select)->with('edgeCoord', json_encode($arrEdgeCoord))->with('centerCoord',$centerCoord)->with('zoom', $zoom)->with('number', $number)->with('people', $people)
    		->with('province',$province)->with('district',$district)->with('subdistrict',$subdistrict)->with('dataPatient', $datapatient)->with('pastillness', $pastillness)->with('historysurgery', $historysurgery)->with('congenital', $congenital)
    		->with('arrEdgeCoords', json_encode($arrEdgeCoords))->with('isNotSelect', json_encode($isNotSelect))->with('arrCigaratte', $this->getCigaratte())->with('cigarette', $cigarette)->with('arrDrink', $this->getDrink())->with('drink', $drink)
    		->with('color', json_encode($color))->with('arrVillage', json_encode($arrVillage))->with('arrCenterCoord', json_encode($arrCenterCoords));
    }
    
    public function getPatientByAdl($village, $firstname, $lastname, $homeNo){
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
    		$data[$obj->HomeNo][$index]['age'] = intval($interval->format('%R%a')/365);
    		$adlResult = DB::table('evoluation_result')
    			->where('form_id', 7)
    			->where('patient_id', $obj->id)
    			->orderBy('date', 'desc')
    			->first();
    		$data[$obj->HomeNo][$index]['result'] = ($adlResult == null)?'-':$adlResult->result_name;
    		$data[$obj->HomeNo][$index]['color'] = ($adlResult == null)?1:$adlResult->result_id;
    		
    		$index++;
    		$homeno = $obj->HomeNo;
    	}
    	return json_encode($data);
    }
    
    public function adl(Request $request)
    {
    	$select = $request->input('village');
    	$homeNo = $request->input('homeNo');
    	$firstname = $request->input('firstname');
    	$lastname = $request->input('lastname');
    	 
    	$villages = Village::all()->sortBy('village');
    	
    	$coord = Village::where('village', '=', $select)->first();
    	
    	$edgeCoord;
    	$isNotSelect;
    	$arrEdgeCoords = [];
    	$arrCenterCoords = [];
    	$centerCoord = ["99.890998", "8.665091"];
    	$arrEdgeCoord = [];
    	$zoom = 14;
    	$number = 0;
    	$people = [];
    	$province = '';
    	$district = '';
    	$subdistrict = '';
    	$datapatient = json_encode([]);
    	$color = "";
    	$arrVillage = [];
    	if($coord != null){
    		$isNotSelect = false;
    		$edgeCoord = $coord->edgecoord;
    		$centerCoord = explode(",", $coord->centercoord);
    		$color = $coord->color;
    		if($coord->edgecoord != ""){
    			$arrEdgeCoord = $this->splitLatLng($edgeCoord);
    		}
    		$zoom = 15;
    			
    		// people in vilage
    		$people = DB::table('patient')
    		->join('house','patient.HomeNo', '=', 'house.hno')
    		->where('patient.Village', '=', $select)
    		->where('house.villcode', '=', ((int)$select < 10)?'8008090'.$select: '800809'.$select)
    		->where('patient.HomeNo', 'like', ($homeNo==null)?'%':'%'.$homeNo.'%')
    		->where('patient.patient.Firstname', 'like', ($firstname==null)?'%':'%'.$firstname.'%')
    		->where('Sirname', 'like', ($lastname==null)?'%':'%'.$lastname.'%')
    		->orderBy('patient.HomeNo')
    		->groupBy('patient.HomeNo')
    		->get();
    			
    		$number = $people->count();
    			
    		//location of group people
    		$province = DB::table('cprovince')->where('provcode', '80')->value('provname');
    		$district = DB::table('cdistrict')->where('provcode', '80')->where('distcode','08')->value('distname');
    		$subdistrict = DB::table('csubdistrict')->where('provcode', '80')->where('distcode','08')->where('subdistcode','09')->value('subdistname');
    	
    		$datapatient = $this->getPatientByAdl($select, $firstname, $lastname, $homeNo);
    	}else{
    		$isNotSelect = true;
			$local = DB::table('villages')
				->select('edgecoord','color','village','centercoord')
				->where('provcode', '80')
				->where('distcode', '08')
				->where('subdistcode', '09')
				->get();
			
			$index = 0;
			foreach ($local as $obj){
				$arrEdgeCoords[$index] = $this->splitLatLng($obj->edgecoord);
				$arrCenterCoords[$index] = $this->splitCenterLatLng($obj->centercoord);
				$color[$index] = $obj->color;
				$arrVillage[$index] = $obj->village;
				$index++;
			}
    	}
    	
    	return view('map.adl')->with('villages',$villages)->with('select', $select)->with('edgeCoord', json_encode($arrEdgeCoord))->with('centerCoord',$centerCoord)->with('zoom', $zoom)->with('number', $number)->with('people', $people)
    	->with('province',$province)->with('district',$district)->with('subdistrict',$subdistrict)->with('dataPatient', $datapatient)->with('homeNo',$homeNo)->with('firstname',$firstname)->with('lastname',$lastname)
    	->with('arrEdgeCoords', json_encode($arrEdgeCoords))->with('isNotSelect', json_encode($isNotSelect))->with('color', json_encode($color))->with('arrVillage', json_encode($arrVillage))->with('arrCenterCoord', json_encode($arrCenterCoords));
    }
    
    public function getPatientByOsteoarthritis($village, $firstname, $lastname, $homeNo){
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
    		$data[$obj->HomeNo][$index]['age'] = intval($interval->format('%R%a')/365);
    		$dailyResult = DB::table('evoluation_result')
    		->where('form_id', 9)
    		->where('patient_id', $obj->id)
    		->orderBy('date', 'desc')
    		->first();
    		$data[$obj->HomeNo][$index]['result'] = ($dailyResult == null)?'-':$dailyResult->result_name;
    		if($dailyResult != null && ($dailyResult->result_id == 4 || $dailyResult->result_id == 5 || $dailyResult->result_id == 6)){
    			$data[$obj->HomeNo][$index]['isRed'] = 1;
    			$data[$obj->HomeNo][$index]['color'] = $dailyResult->result_id;
    		}else{
    			$data[$obj->HomeNo][$index]['isRed'] = 0;
    			$data[$obj->HomeNo][$index]['color'] = 0;
    		}
    
    		$index++;
    		$homeno = $obj->HomeNo;
    	}
    	return json_encode($data);
    }
    
    public function osteoarthritis(Request $request)
    {
    	$select = $request->input('village');
    	$homeNo = $request->input('homeNo');
    	$firstname = $request->input('firstname');
    	$lastname = $request->input('lastname');
    	
    	$villages = Village::all()->sortBy('village');
    	 
    	$coord = Village::where('village', '=', $select)->first();
    	 
    	$edgeCoord;
    	$isNotSelect;
    	$arrEdgeCoords = [];
    	$arrCenterCoords = [];
    	$centerCoord = ["99.890998", "8.665091"];
    	$arrEdgeCoord = [];
    	$zoom = 14;
    	$number = 0;
    	$people = [];
    	$province = '';
    	$district = '';
    	$subdistrict = '';
    	$datapatient = json_encode([]);
    	$color = "";
    	$arrVillage = [];
    	if($coord != null){
    		$isNotSelect = false;
    		$edgeCoord = $coord->edgecoord;
    		$centerCoord = explode(",", $coord->centercoord);
    		$color = $coord->color;
    		if($coord->edgecoord != ""){
    			$arrEdgeCoord = $this->splitLatLng($edgeCoord);
    		}
    		$zoom = 15;
    		 
    		// people in vilage
    		$people = DB::table('patient')
	    		->join('house','patient.HomeNo', '=', 'house.hno')
	    		->where('patient.Village', '=', $select)
	    		->where('house.villcode', '=', ((int)$select < 10)?'8008090'.$select: '800809'.$select)
	    		->where('patient.HomeNo', 'like', ($homeNo==null)?'%':'%'.$homeNo.'%')
	    		->where('patient.patient.Firstname', 'like', ($firstname==null)?'%':'%'.$firstname.'%')
	    		->where('Sirname', 'like', ($lastname==null)?'%':'%'.$lastname.'%')
	    		->orderBy('patient.HomeNo')
	    		->groupBy('patient.HomeNo')
	    		->get();
    		 
    		$number = $people->count();
    		 
    		//location of group people
    		$province = DB::table('cprovince')->where('provcode', '80')->value('provname');
    		$district = DB::table('cdistrict')->where('provcode', '80')->where('distcode','08')->value('distname');
    		$subdistrict = DB::table('csubdistrict')->where('provcode', '80')->where('distcode','08')->where('subdistcode','09')->value('subdistname');
    		 
    		$datapatient = $this->getPatientByOsteoarthritis($select, $firstname, $lastname, $homeNo);
    	}else{
    	$isNotSelect = true;
			$local = DB::table('villages')
				->select('edgecoord','color','village','centercoord')
				->where('provcode', '80')
				->where('distcode', '08')
				->where('subdistcode', '09')
				->get();
			
			$index = 0;
			foreach ($local as $obj){
				$arrEdgeCoords[$index] = $this->splitLatLng($obj->edgecoord);
				$arrCenterCoords[$index] = $this->splitCenterLatLng($obj->centercoord);
				$color[$index] = $obj->color;
				$arrVillage[$index] = $obj->village;
				$index++;
			}
    	}
    	 
    	return view('map.osteoarthritis')->with('villages',$villages)->with('select', $select)->with('edgeCoord', json_encode($arrEdgeCoord))->with('centerCoord',$centerCoord)->with('zoom', $zoom)->with('number', $number)->with('people', $people)
    	->with('province',$province)->with('district',$district)->with('subdistrict',$subdistrict)->with('dataPatient', $datapatient)->with('homeNo',$homeNo)->with('firstname',$firstname)->with('lastname',$lastname)
    	->with('arrEdgeCoords', json_encode($arrEdgeCoords))->with('isNotSelect', json_encode($isNotSelect))->with('color', json_encode($color))->with('arrVillage', json_encode($arrVillage))->with('arrCenterCoord', json_encode($arrCenterCoords));
    }
}
