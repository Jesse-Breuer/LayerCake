<?php
/*
Layercake 1.0 Copyright(c) 2010 by Jesse Breuer distributed under the terms of the GNU General Public License.
*/

require('process/phpQuery-onefile.php');

$doc = phpQuery::newDocumentFileXHTML('./input.htm');

function array_push_assoc($array, $key, $value){//needed to add associative attr=>value pairs to the array
 $array[$key] = $value;
 return $array;
}//close function

$attrs=array("id","class","bgc","txc", "bdc", "fts", "flh","ilh","wid","cmn","amn","tmn","rmn","bmn","lmn","abd","tbd","rbd","bbd","lbd","apd","tpd","rpd","bpd","lpd","innerWidth"); //list all of the attributes.

$attrsLength=count($attrs)-1;
$removables = array_slice($attrs, 2); //the ones we will strip out at the end
$removablesLength=count($removables)-1;

$divs=array();//make the divs an associative array containing attr=>value pairs
$numberOfDivs=$doc["div"]->size()-1;

for ($i=0;$i<=$numberOfDivs;$i++){//loop through the divs to build an array
$divs[$i]=array();

for ($j=0;$j<=$attrsLength;$j++){//loop through the attribute => value pairs for each div in the array
$valueOfAttr=$doc["div:eq($i)"]->attr("$attrs[$j]");
$divs[$i] = array_push_assoc($divs[$i], $attrs[$j], $valueOfAttr);

}//close j
}//close i

//print_r($divs);

function edgeGetter($thisNum,$thisEdge){
	/*returns the value of the parameter specified for the div specified*/ 
	global $divs;
	global $defaults;
	$allEdge="a".substr($thisEdge,-2);//the name of the all version, amn, abd, apd for this edge
	if ($divs[$thisNum][$thisEdge]!=null){
		return $divs[$thisNum][$thisEdge]; //this edge if set
	}else if($divs[$thisNum][$allEdge]!=null){
		return $divs[$thisNum][$allEdge]; //all edge if set
	}else if ($defaults[$thisEdge]!=null){
		return $defaults[$thisEdge]; //default for this edge if set
	}else if ($defaults[$allEdge]!=null){
		return $defaults[$allEdge]; //default for this edge if set
	}else{
		return 0;
	}
} /*close edgeGetter*/

function edgeTotals($thisNum){
	/*returns the value of the parameter specified for the div specified*/ 
	global $divs;
	$edgeTotal=edgeGetter($thisNum,lmn)+edgeGetter($thisNum,rmn)+edgeGetter($thisNum,lbd)+edgeGetter($thisNum,rbd)+edgeGetter($thisNum,lpd)+edgeGetter($thisNum,rpd);
	return $edgeTotal;
} /*close edgeTotals*/


function getOuterWidth($thisNum){
	global $doc;
	global $divs;
	$parentid=$doc["div:eq($thisNum)"]->parent()->attr("id");
	 /*echo "$parentid<br />";*/
	 if($thisNum==0){
	$parentInnerWidth=$doc["div:eq($thisNum)"]->parent()->attr("wid"); //get the inner width of the element containing this one
	}else{
	$parentInnerWidth=$doc["div:eq($thisNum)"]->parent()->attr("innerWidth");
	}//end if else
//division
$fraction=$divs[$thisNum][wid];
	try{
if($fraction!=null){ //in case you have a div without width
$focFraction = explode("/",$fraction);
$focNumerator=$focFraction[0];
$focDenominator=$focFraction[1];
$outerwidth=$parentInnerWidth*($focNumerator / $focDenominator);
return $outerwidth;
}//end if
else{
	
	throw new Exception("Div without wid, add to output instead of input, to avoid a mistake in the css");
	
}//end else
}//end try
		catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
    }
}//end function


$defaults=array();
for ($i=0;$i<=$attrsLength;$i++){//loop through the attribute => value pairs for each div in the array
$valueOfAttr=$doc["span:eq(0)"]->attr("$attrs[$i]");
$defaults = array_push_assoc($defaults, $attrs[$i], $valueOfAttr);
}//close defaults


function cssEdges($divNum,$edgeType){
return 
edgeGetter($divNum,"t$edgeType")."px ".
edgeGetter($divNum,"r$edgeType")."px ".
edgeGetter($divNum,"b$edgeType")."px ".
edgeGetter($divNum,"l$edgeType")."px;";
}

function cssAdd($i, $attribute, $tag, $fallback){//assign attribute to tag or use default
global $i;
global $divs;
global $defaults;
global $css;
$css.=" $attribute:";
if ($divs[$i]["$tag"]!=null){
$css.=$divs[$i]["$tag"];
	}else if($defaults["$tag"]!=null){
$css.=$defaults["$tag"];
	}else{
$css.=$fallback;		
	}
$css.=";
";
}


/*write css*/
for ($i=0;$i<=$numberOfDivs;$i++){//loop through the divs to build an array


for ($j=0;$j<=$numberOfDivs;$j++){//find multiple divs with same class
if($i>$j&&$divs[$i]["class"]==$divs[$j]["class"]){
$repeater= $divs[$i]["class"];
}
}
 
if($divs[$i]["class"]!=$repeater||$divs[$i]["id"]!=null){//only first div with the class


if ($divs[$i][id]!=null) {
	$css.="#".$divs[$i][id]."{
";
}else if ($divs[$i]["class"]!=null) 
{

	$css.=".".$divs[$i]["class"]."{
";
}//end else if


cssAdd($i, "background-color", "bgc", "#ffffff");
cssAdd($i, "color", "txc", "#000000");
cssAdd($i, "border-color", "bdc", "#000000");
cssAdd($i, "font-size", "fts", "12px");

$css.=" margin:";
if ($divs[$i]["cmn"]!=null){
$css.=$divs[$i]["cmn"]."px auto;";
	}else{
$css.=cssEdges($i,mn);
	}

$css.="
 padding:";
$css.=cssEdges($i,pd);

$css.="
 border-width:";
$css.=cssEdges($i,bd);

$css.="
 border-style:solid;";

$css.="
 width:"; 
 
 if ($i!=0){
$innerWidth=getOuterWidth($i)-edgeTotals($i);

}else{
$innerWidth=$divs[0][wid];

}

$css.="$innerWidth"."px;";

$doc["div:eq($i)"]->attr("innerWidth",$innerWidth);
 
$css.="
 overflow:hidden;";

if ($divs[$i]["cmn"]==null){ //float left if not centered
$css.="
 float:left;";
}

if ($divs[$i]["flh"]!=null){
$css.="
 position:absolute;
 display:none;
 z-index:3;";
}

if ($divs[$i]["ilh"]!=null){
$css.="
 display:none;";
}

//close definition
$css.="
}

";
}//end check repeaters
}//end for loop
/*end write css*/


$myCss = "style.css"; //outputs css
$fh = fopen($myCss, 'w') or die("can't open file");
$stringData = $css;
fwrite($fh, $stringData);
fclose($fh);

$myOutput = "process/output.htm"; //outputs a text file with original values, default values, and computed widths
$fh = fopen($myOutput, 'w') or die("can't open file");
$stringData = $doc;
fwrite($fh, $stringData);
fclose($fh);


/*removing the attrs before printing*/
for ($i=0;$i<=$removablesLength;$i++){
$doc['div']->removeAttr("$removables[$i]");
$doc['.defaults']->remove();
}//end for
//$css="test";
//print_r($defaults);
print $doc;

?>
