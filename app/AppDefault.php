<?php
namespace App;

use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\This;
use Carbon\Carbon;

class AppDefault
{
	const CENTER_COORD = ["99.890998", "8.665091"];
	const DEFAULT_ZOOM = 14;
	const DEFAULT_PROVCODE =  "80"; // Nakhonsithammarat
	const DEFAULT_DISTCODE = "08"; // Thasala
	const DEFAULT_SUBDISTCODE = "09"; // Thaiburi
	
	const DEFAULT_VOLUNTEER_TYPE = "09";
	
	const CIGARETTE_NEVER = 1;
	const CIGARETTE_RARELY = 2;
	const CIGARETTE_HURLY = 3;
	const CIGARETTE_ONCE = 4;
	
	const DRINK_NEVER = 1;
	const DRINK_RARELY = 2;
	const DRINK_HURLY = 3;
	const DRINK_ONCE = 4;
	
	public static function getCigaratte(){
		return array(
				self::CIGARETTE_NEVER => "ไม่สูบ",
				self::CIGARETTE_RARELY => "สูบบ้าง",
				self::CIGARETTE_HURLY => "สูบประจำ",
				self::CIGARETTE_ONCE => "เคยสูบ"
		);
	}
	
	public static function getDrink(){
		return array(
				self::DRINK_NEVER => "ไม่ดื่ม",
				self::DRINK_RARELY => "ดื่มบ้าง",
				self::DRINK_HURLY => "ดื่มประจำ",
				self::DRINK_ONCE => "เคยดื่ม"
		);
	}
	
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
	
	public static function getPatientByForm($village, $firstname, $lastname, $homeNo, $formId, $year, $time){
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
			$arrResult[(int)$obj->id]['score'] = $obj->score_start;
		}
	
		$data = [];
		$index = 0;
		$homeno = null;
		$first = true;
		foreach ($people as $obj){
	
			if(!$first){
				if($homeno != $obj->HomeNo){
					$index = 0;
				}
			}else{
				$first = false;
			}
			 
			$data[$obj->HomeNo][$index]['name'] = $obj->Firstname." ".$obj->Sirname;
			$data[$obj->HomeNo][$index]['age'] = AppDefault::calAge($obj->Birthday);
			$dailyResult = DB::table('evoluation_result')
				->where('form_id', $formId)
				->where('patient_id', $obj->id)
				->where('years', $year)
				->where('times', $time)
				->orderBy('date', 'desc')
				->first();
			
			$data[$obj->HomeNo][$index]['result'] = ($dailyResult == null)?'-':$dailyResult->result_name;
			$data[$obj->HomeNo][$index]['row'] = ($dailyResult == null)?"":$arrResult[$dailyResult->result_id]['row'];
			$data[$obj->HomeNo][$index]['score'] = ($dailyResult == null)?0:$arrResult[$dailyResult->result_id]['score'];
			$data[$obj->HomeNo][$index]['pin'] = ($dailyResult == null)?"1":$arrResult[$dailyResult->result_id]['pin'];
	
			$index++;
			$homeno = $obj->HomeNo;
		}
		return json_encode($data);
	}
	
	public static function getVolunteer($village, $firstname, $lastname){
		$strVillage = AppDefault::DEFAULT_PROVCODE.AppDefault::DEFAULT_DISTCODE.AppDefault::DEFAULT_SUBDISTCODE;
		
		$people = DB::table('house')
			->leftjoin('person','person.hcode', '=', 'house.hcode')
			->where('house.villcode', '=', ((int)$village < 10)?$strVillage.'0'.$village: $strVillage.$village)
			->where('person.fname', 'like', ($firstname==null)?'%':'%'.$firstname.'%')
			->where('person.lname', 'like', ($lastname==null)?'%':'%'.$lastname.'%')
			->groupby('house.hno')
			->orderby('person.fname')
			->get();
		
		return $people;
	}
	
	public static function getPatientByVolunteer($village, $firstname, $lastname){
		$currentYear = date("Y-mm-dd")+543;
		$strVillage = AppDefault::DEFAULT_PROVCODE.AppDefault::DEFAULT_DISTCODE.AppDefault::DEFAULT_SUBDISTCODE;
		
		$volunteer = DB::table('person')
			->join('house', 'person.pid', '=', 'house.pidvola')
			->where('house.villcode', '=', ((int)$village < 10)?$strVillage.'0'.$village: $strVillage.$village);
		if($firstname != null)
			$volunteer = $volunteer->where('person.fname', 'like', ($firstname==null)?'%':'%'.$firstname.'%');
		if($lastname != null)
			$volunteer = $volunteer->where('person.lname', 'like', ($lastname==null)?'%':'%'.$lastname.'%');
		$volunteer = $volunteer->groupby('house.hno')->get();
		
		$arrVolunteer = [];
		$arrVolunteerName = [];
		foreach ($volunteer as $obj){
			$arrVolunteer[] = $obj->pidvola;
			$arrVolunteerName[$obj->hno] = $obj->fname." ".$obj->lname;
		}
		
		$data = [];
		foreach ($volunteer as $obj){
			$volaList = AppDefault::getVolunteerFromPidva($obj->pidvola, $village);
			$volaList = AppDefault::setVolunteerFromPidvala($volaList, $arrVolunteerName);
			$data[$obj->pidvola] = $volaList;
		}

		return $data;
	}
	
	public static function setVolunteerFromPidvala($volaList, $arrVolunteerName){
		$currentYear = date("Y-mm-dd")+543;
		$data = [];
		$index = 0;
		$homeno = null;
		$first = true;
		foreach ($volaList as $obj){
			
			if(!$first){
				if($homeno != $obj->HomeNo)
					$index = 0;
			}else{
				$first = false;
			}
			$data[$obj->HomeNo]['lat'] = $obj->ygis;
			$data[$obj->HomeNo]['lng'] = $obj->xgis;
			$data[$obj->HomeNo]['volunteer'] = $arrVolunteerName[$obj->HomeNo];
			$data[$obj->HomeNo]['data'][$index]['name'] = $obj->Firstname." ".$obj->Sirname;
			$data[$obj->HomeNo]['data'][$index]['birthday'] = AppDefault::getStringBirthdate($obj->Birthday);
			$data[$obj->HomeNo]['data'][$index]['age'] =AppDefault::calAge($obj->Birthday);
			$index++;
			$homeno = $obj->HomeNo;
		}
		return $data;
	}
	
	public static function getVolunteerFromPidva($pidvola, $village){
		$strVillage = AppDefault::DEFAULT_PROVCODE.AppDefault::DEFAULT_DISTCODE.AppDefault::DEFAULT_SUBDISTCODE;
		$people = DB::table('patient')
			->join('house','patient.HomeNo', '=', 'house.hno')
			->where('house.villcode', '=', ((int)$village < 10)?$strVillage.'0'.$village: $strVillage.$village)
			->where('patient.Village', '=', $village)
			->where('house.pidvola', '=', $pidvola);
		return $people->orderby('house.hno')->get();
	}
	
	public static function getRecordTime(){
		return DB::table('record_time')
			->groupby('record_years')->orderby('record_years', 'desc')->get();
	}
	
	public static function getRecordTimeFromYear($recordYear){
		return $data = DB::table('record_time')->where('record_years', '=', $recordYear)
    		->orderby('record_times', 'desc')->get();
	}
	
	public static function getAllTimeOfPetientRecord(){
		return DB::table('patient_history')->select('Time')->groupby('Time')
    		->orderby('Time', 'desc')->get();
	}
	
	public static function getHomeNumberByBehavior($village, $pastillness, $historysurgery, $congenital, $cigarette, $drink, $time){
		$strVillage = AppDefault::DEFAULT_PROVCODE.AppDefault::DEFAULT_DISTCODE.AppDefault::DEFAULT_SUBDISTCODE;
		$query = DB::table('patient')
			->join('patient_history','patient.id', '=', 'patient_history.patient_id')
			->join('house','patient.HomeNo', '=', 'house.hno')
			->where('house.villcode', '=', ((int)$village < 10)?$strVillage.'0'.$village: $strVillage.$village)
			->where('patient.Village', '=', $village)
			->where('patient.Province', '=', AppDefault::DEFAULT_PROVCODE)
			->where('patient.District', '=', AppDefault::DEFAULT_DISTCODE)
			->where('patient.Subdistrict', '=', AppDefault::DEFAULT_SUBDISTCODE)
			->where('patient_history.Time', '=', (int)$time);
			
			if($pastillness!=null){
				$arrPastillness = explode(",", $pastillness);
				$query->Where(function ($query) use($arrPastillness) {
					foreach($arrPastillness as $val) {
						$query->orWhere('patient_history.Pastillness', 'like', '%'.$val.'%');
					}
				});
			}
			if($historysurgery != null){
				$arrHistorysurgery = explode(",", $historysurgery);
				$query->Where(function ($query) use($arrHistorysurgery) {
					foreach($arrHistorysurgery as $val) {
						$query->orWhere('patient_history.Historysurgery', 'like', '%'.$val.'%');
					}
				});
			}
			if($congenital != null){
				$arrCongenital = explode(",", $congenital);
				$query->Where(function ($query) use($arrCongenital) {
					foreach($arrCongenital as $val) {
						$query->orWhere('patient_history.Congenital', 'like', '%'.$val.'%');
					}
				});
			}
			if($cigarette != null){
				$query->where('patient_history.Cigarette', $cigarette);
			}
			if($drink != null){
				$query->where('patient_history.Drink', $drink);
			}
			
			$query->orderBy('patient.HomeNo')
			->groupBy('patient.HomeNo');
			
// 			dd($query->toSql());
		return $query->get();
	
	}
	
	public static function getPatientByBehavior($village, $pastillness, $historysurgery, $congenital, $cigarette, $drink, $time){
		$strVillage = AppDefault::DEFAULT_PROVCODE.AppDefault::DEFAULT_DISTCODE.AppDefault::DEFAULT_SUBDISTCODE;
		$currentYear = date("Y-mm-dd")+543;
		$query = DB::table('patient')
		->join('patient_history','patient.id', '=', 'patient_history.patient_id')
		->join('house','patient.HomeNo', '=', 'house.hno')
		->where('house.villcode', '=', ((int)$village < 10)?$strVillage.'0'.$village: $strVillage.$village)
		->where('patient.Village', '=', $village)
		->where('patient.Province', '=', AppDefault::DEFAULT_PROVCODE)
		->where('patient.District', '=', AppDefault::DEFAULT_DISTCODE)
		->where('patient.Subdistrict', '=', AppDefault::DEFAULT_SUBDISTCODE)
		->where('patient_history.Time', '=', (int)$time);
			
		if($pastillness!=null){
			$arrPastillness = explode(",", $pastillness);
			$query->Where(function ($query) use($arrPastillness) {
				foreach($arrPastillness as $val) {
					$query->orWhere('patient_history.Pastillness', 'like', '%'.$val.'%');
				}
			});
		}
		if($historysurgery != null){
			$arrHistorysurgery = explode(",", $historysurgery);
			$query->Where(function ($query) use($arrHistorysurgery) {
				foreach($arrHistorysurgery as $val) {
					$query->orWhere('patient_history.Historysurgery', 'like', '%'.$val.'%');
				}
			});
		}
		if($congenital != null){
			$arrCongenital = explode(",", $congenital);
			$query->Where(function ($query) use($arrCongenital) {
				foreach($arrCongenital as $val) {
					$query->orWhere('patient_history.Congenital', 'like', '%'.$val.'%');
				}
			});
		}
		if($cigarette != null){
			$query->where('patient_history.Cigarette', $cigarette);
		}
		if($drink != null){
			$query->where('patient_history.Drink', $drink);
		}
			
		$query->orderBy('patient.HomeNo');
		
		$people = $query->get();
		
		$data = [];
		$index = 0;
		$homeno = null;
		$first = true;
		foreach ($people as $obj){
			 
			if(!$first){
				if($homeno != $obj->HomeNo){
					$index = 0;
				}
			}else{
				$first = false;
			}
		
			$arrCigarette = AppDefault::getCigaratte();
			$arrDrink = AppDefault::getDrink();
		
			$data[$obj->HomeNo][$index]['name'] = $obj->Firstname." ".$obj->Sirname;
			$data[$obj->HomeNo][$index]['age'] = AppDefault::calAge($obj->Birthday);
			$data[$obj->HomeNo][$index]['pastillness'] = ($obj->Pastillness == null)?'-':$obj->Pastillness;
			$data[$obj->HomeNo][$index]['historysurgery'] = ($obj->Historysurgery == null)?'-':$obj->Historysurgery;
			$data[$obj->HomeNo][$index]['congenital'] = ($obj->Congenital == null)?'-':$obj->Congenital;
			$data[$obj->HomeNo][$index]['cigarette'] = ($obj->Cigarette == null)?'-':$arrCigarette[$obj->Cigarette];
			$data[$obj->HomeNo][$index]['drink'] = ($obj->Drink == null)?'-':$arrDrink[$obj->Drink];
			$index++;
			$homeno = $obj->HomeNo;
		}
		return $data;
	}
	
	public static function calAge($date){
		if($date != null){
			$age = (new Carbon($date))->age;	
		}else{
			$age = '-';
		}
		
		return $age;
	}
	
	public static function getStringBirthdate($birthdate){
		if($birthdate != null){
			$dt = new Carbon($birthdate);
			$dt->addYears(543);
			$birthdate = $dt->format('d/m/Y');
		}else{
			$birthdate = '-';
		}
		
		return $birthdate;
	}
}

