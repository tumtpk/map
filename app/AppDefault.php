<?php
namespace App;

use Illuminate\Support\Facades\DB;

class AppDefault
{
	const CENTER_COORD = ["99.890998", "8.665091"];
	const DEFAULT_ZOOM = 14;
	const DEFAULT_PROVCODE =  "80"; // Nakhonsithammarat
	const DEFAULT_DISTCODE = "08"; // Thasala
	const DEFAULT_SUBDISTCODE = "09"; // Thaiburi
	
	public static function getStringLocation(){
		//location of group people
		$province = DB::table('cprovince')->where('provcode', AppDefault::DEFAULT_PROVCODE)->value('provname');
		$district = DB::table('cdistrict')->where('provcode', AppDefault::DEFAULT_PROVCODE)->where('distcode',AppDefault::DEFAULT_DISTCODE)->value('distname');
		$subdistrict = DB::table('csubdistrict')->where('provcode', AppDefault::DEFAULT_PROVCODE)->where('distcode',AppDefault::DEFAULT_DISTCODE)->where('subdistcode',AppDefault::DEFAULT_SUBDISTCODE)->value('subdistname');
		return " ตำบล ".$subdistrict." อำเภอ ".$district." จังหวัด ".$province;
	}
	
	public static function splitLatLng($strLatLng){
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
	
	public static function splitCenterLatLng($strLatLng){
		$l = explode(",", $strLatLng);
		$arrCenterCoord["lat"] = $l[1];
		$arrCenterCoord["lng"] = $l[0];
		return $arrCenterCoord;
	}
	
	public static function getEvoluationPart()
	{
		$evoluationPart = DB::table('evoluation_part')->get();
		return $evoluationPart;
	}
	
	public static function getEvoluationForm()
	{
		$evoluationForm = DB::table('evoluation_form')->get();
		$evoluationPart = AppDefault::getEvoluationPart();
		 
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
	
	public static function getArrayVillage(){
		return $local = DB::table('villages')
		->where('provcode', AppDefault::DEFAULT_PROVCODE)
		->where('distcode', AppDefault::DEFAULT_DISTCODE)
		->where('subdistcode', AppDefault::DEFAULT_SUBDISTCODE)
		->orderBy('village')
		->get();
	}
	
	public static function getFormName($formId){
		return DB::table('evoluation_form')->where('id', '=', $formId)
		->pluck('name');
	}
	
	public static function getPatientByForm($village, $firstname, $lastname, $homeNo, $formId){
		$currentYear = date("Y-mm-dd")+543;
		$strVillage = AppDefault::DEFAULT_PROVCODE.AppDefault::DEFAULT_DISTCODE.AppDefault::DEFAULT_SUBDISTCODE;
		// people in village
		$people = DB::table('patient')
		->join('house','patient.HomeNo', '=', 'house.hno')
		->where('patient.Village', '=', $village)
		->where('house.villcode', '=', ((int)$village < 10)?$strVillage.'0'.$village: $strVillage.$village)
		->where('patient.HomeNo', 'like', ($homeNo==null)?'%':'%'.$homeNo.'%')
		->where('patient.patient.Firstname', 'like', ($firstname==null)?'%':'%'.$firstname.'%')
		->where('Sirname', 'like', ($lastname==null)?'%':'%'.$lastname.'%')
		->orderBy('patient.HomeNo')
		->get();
		
		$result = DB::table('result_desc')->where('form_id', '=', $formId)->get();
		$arrResult = [];
		foreach ($result as $obj){
			$arrResult[(int)$obj->id]['pin'] = $obj->pincolor;
			$arrResult[(int)$obj->id]['row'] = $obj->rowcolor;
		}
	
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
				->where('form_id', $formId)
				->where('patient_id', $obj->id)
				->orderBy('date', 'desc')
				->first();
			
			$data[$obj->HomeNo][$index]['result'] = ($dailyResult == null)?'-':$dailyResult->result_name;
			$data[$obj->HomeNo][$index]['row'] = ($dailyResult == null)?"":$arrResult[$dailyResult->result_id]['row'];
			//delete
			if($dailyResult != null && ($dailyResult->result_id == 4 || $dailyResult->result_id == 5 || $dailyResult->result_id == 6)){
				$data[$obj->HomeNo][$index]['isRed'] = 1;
// 				$data[$obj->HomeNo][$index]['color'] = $dailyResult->result_id;
			}else{
				$data[$obj->HomeNo][$index]['isRed'] = 0;
// 				$data[$obj->HomeNo][$index]['color'] = 0;
			}
			//delete
	
			$index++;
			$homeno = $obj->HomeNo;
		}
		return json_encode($data);
	}
}

