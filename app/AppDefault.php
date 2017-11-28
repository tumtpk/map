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
		->select('edgecoord','color','village','centercoord')
		->where('provcode', AppDefault::DEFAULT_PROVCODE)
		->where('distcode', AppDefault::DEFAULT_DISTCODE)
		->where('subdistcode', AppDefault::DEFAULT_SUBDISTCODE)
		->get();
	}
}

