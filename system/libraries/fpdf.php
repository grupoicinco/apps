<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(str_replace("\\","/",APPPATH).'libraries/fpdf/fpdf'.EXT); //Por si estamos ejecutando este script en un servidor Windows


//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['V']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
////////////////////////////////////


class PDF extends FPDF{	
	function Header(){
		
	}
	function striped_table($header, $data, $w = array(40, 35, 45, 40)){
		// Colores, ancho de línea y fuente en negrita
	    $this->SetFillColor(60,60,60);
	    $this->SetTextColor(255);
	    $this->SetLineWidth(.3);
	    $this->SetFont('','B');
		$this->SetFontSize(6);
	    // Cabecera
	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],6,$header[$i],1,0,'L',true);
	    $this->Ln();
	    // Restauración de colores y fuentes
	    $this->SetFillColor(224,235,255);
	    $this->SetTextColor(0);
	    $this->SetFont('');
	    // Datos
	    $fill = false;
	    foreach($data as $row)
	    {
	    	foreach($row as $i => $cell):
	        $this->Cell($w[$i],5,$row[$i],'LR',0,'L',$fill);
			endforeach;
	        $this->Ln();
	        $fill = !$fill;
	    }
	    // Línea de cierre
	    $this->Cell(array_sum($w),0,'','T');
		$this->Ln(1);
	}
	/**
	* Draws text within a box defined by width = w, height = h, and aligns
	* the text vertically within the box ($valign = M/B/T for middle, bottom, or top)
	* Also, aligns the text horizontally ($align = L/C/R/J for left, centered, right or justified)
	* drawTextBox uses drawRows
	*
	* This function is provided by TUFaT.com
	*/
	function drawTextBox($strText, $w, $h, $align='L', $valign='T', $border=1)
	{
	    $xi=$this->GetX();
	    $yi=$this->GetY();
	    
	    $hrow=$this->FontSize;
	    $textrows=$this->drawRows($w, $hrow, $strText, 0, $align, 0, 0, 0);
	    $maxrows=floor($h/$this->FontSize);
	    $rows=min($textrows, $maxrows);
	
	    $dy=0;
	    if (strtoupper($valign)=='M')
	        $dy=($h-$rows*$this->FontSize)/2;
	    if (strtoupper($valign)=='B')
	        $dy=$h-$rows*$this->FontSize;
	
	    $this->SetY($yi+$dy);
	    $this->SetX($xi);
	
	    $this->drawRows($w, $hrow, $strText, 0, $align, 0, $rows, 1);
	
	    if ($border==1)
	        $this->Rect($xi, $yi, $w, $h);
	}
	
	function drawRows($w, $h, $txt, $border=0, $align='J', $fill=0, $maxline=0, $prn=0)
	{
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r", '', $txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $b=0;
	    if($border)
	    {
	        if($border==1)
	        {
	            $border='LTRB';
	            $b='LRT';
	            $b2='LR';
	        }
	        else
	        {
	            $b2='';
	            if(is_int(strpos($border, 'L')))
	                $b2.='L';
	            if(is_int(strpos($border, 'R')))
	                $b2.='R';
	            $b=is_int(strpos($border, 'T')) ? $b2.'T' : $b2;
	        }
	    }
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $ns=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        //Get next character
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            //Explicit line break
	            if($this->ws>0)
	            {
	                $this->ws=0;
	                if ($prn==1) $this->_out('0 Tw');
	            }
	            if ($prn==1) {
	                $this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
	            }
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $ns=0;
	            $nl++;
	            if($border and $nl==2)
	                $b=$b2;
	            if ( $maxline && $nl > $maxline )
	                return substr($s, $i);
	            continue;
	        }
	        if($c==' ')
	        {
	            $sep=$i;
	            $ls=$l;
	            $ns++;
	        }
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            //Automatic line break
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	                if($this->ws>0)
	                {
	                    $this->ws=0;
	                    if ($prn==1) $this->_out('0 Tw');
	                }
	                if ($prn==1) {
	                    $this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
	                }
	            }
	            else
	            {
	                if($align=='J')
	                {
	                    $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
	                    if ($prn==1) $this->_out(sprintf('%.3f Tw', $this->ws*$this->k));
	                }
	                if ($prn==1){
	                    $this->Cell($w, $h, substr($s, $j, $sep-$j), $b, 2, $align, $fill);
	                }
	                $i=$sep+1;
	            }
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $ns=0;
	            $nl++;
	            if($border and $nl==2)
	                $b=$b2;
	            if ( $maxline && $nl > $maxline )
	                return substr($s, $i);
	        }
	        else
	            $i++;
	    }
	    //Last chunk
	    if($this->ws>0)
	    {
	        $this->ws=0;
	        if ($prn==1) $this->_out('0 Tw');
	    }
	    if($border and is_int(strpos($border, 'B')))
	        $b.='B';
	    if ($prn==1) {
	        $this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
	    }
	    $this->x=$this->lMargin;
	    return $nl;
	}
	
	//variables of html parser
	var $B;
	var $I;
	var $U;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;
	
	function PDF_HTML($orientation='P', $unit='mm', $format='A4')
	{
	    //Call parent constructor
	    $this->FPDF($orientation,$unit,$format);
	    //Initialization
	    $this->B=0;
	    $this->I=0;
	    $this->U=0;
	    $this->HREF='';
	    $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
	    $this->issetfont=false;
	    $this->issetcolor=false;
	}
	
	function WriteHTML($html)
	{
	    //HTML parser
	    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
	    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
	    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
	    foreach($a as $i=>$e)
	    {
	        if($i%2==0)
	        {
	            //Text
	            if($this->HREF)
	                $this->PutLink($this->HREF,$e);
	            else
	                $this->Write(5,stripslashes(txtentities($e)));
	        }
	        else
	        {
	            //Tag
	            if($e[0]=='/')
	                $this->CloseTag(strtoupper(substr($e,1)));
	            else
	            {
	                //Extract attributes
	                $a2=explode(' ',$e);
	                $tag=strtoupper(array_shift($a2));
	                $attr=array();
	                foreach($a2 as $v)
	                {
	                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
	                        $attr[strtoupper($a3[1])]=$a3[2];
	                }
	                $this->OpenTag($tag,$attr);
	            }
	        }
	    }
	}
/**
 * Crear código de barras Code39
 */
 function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){

    $wide = $baseline;
    $narrow = $baseline / 3 ; 
    $gap = $narrow;

    $barChar['0'] = 'nnnwwnwnn';
    $barChar['1'] = 'wnnwnnnnw';
    $barChar['2'] = 'nnwwnnnnw';
    $barChar['3'] = 'wnwwnnnnn';
    $barChar['4'] = 'nnnwwnnnw';
    $barChar['5'] = 'wnnwwnnnn';
    $barChar['6'] = 'nnwwwnnnn';
    $barChar['7'] = 'nnnwnnwnw';
    $barChar['8'] = 'wnnwnnwnn';
    $barChar['9'] = 'nnwwnnwnn';
    $barChar['A'] = 'wnnnnwnnw';
    $barChar['B'] = 'nnwnnwnnw';
    $barChar['C'] = 'wnwnnwnnn';
    $barChar['D'] = 'nnnnwwnnw';
    $barChar['E'] = 'wnnnwwnnn';
    $barChar['F'] = 'nnwnwwnnn';
    $barChar['G'] = 'nnnnnwwnw';
    $barChar['H'] = 'wnnnnwwnn';
    $barChar['I'] = 'nnwnnwwnn';
    $barChar['J'] = 'nnnnwwwnn';
    $barChar['K'] = 'wnnnnnnww';
    $barChar['L'] = 'nnwnnnnww';
    $barChar['M'] = 'wnwnnnnwn';
    $barChar['N'] = 'nnnnwnnww';
    $barChar['O'] = 'wnnnwnnwn'; 
    $barChar['P'] = 'nnwnwnnwn';
    $barChar['Q'] = 'nnnnnnwww';
    $barChar['R'] = 'wnnnnnwwn';
    $barChar['S'] = 'nnwnnnwwn';
    $barChar['T'] = 'nnnnwnwwn';
    $barChar['U'] = 'wwnnnnnnw';
    $barChar['V'] = 'nwwnnnnnw';
    $barChar['W'] = 'wwwnnnnnn';
    $barChar['X'] = 'nwnnwnnnw';
    $barChar['Y'] = 'wwnnwnnnn';
    $barChar['Z'] = 'nwwnwnnnn';
    $barChar['-'] = 'nwnnnnwnw';
    $barChar['.'] = 'wwnnnnwnn';
    $barChar[' '] = 'nwwnnnwnn';
    $barChar['*'] = 'nwnnwnwnn';
    $barChar['$'] = 'nwnwnwnnn';
    $barChar['/'] = 'nwnwnnnwn';
    $barChar['+'] = 'nwnnnwnwn';
    $barChar['%'] = 'nnnwnwnwn';

    $this->SetFont('Arial','',10);
    $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(0);

    $code = '*'.strtoupper($code).'*';
    for($i=0; $i<strlen($code); $i++){
        $char = $code[$i];
        if(!isset($barChar[$char])){
            $this->Error('Invalid character in barcode: '.$char);
        }
        $seq = $barChar[$char];
        for($bar=0; $bar<9; $bar++){
            if($seq[$bar] == 'n'){
                $lineWidth = $narrow;
            }else{
                $lineWidth = $wide;
            }
            if($bar % 2 == 0){
                $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
            }
            $xpos += $lineWidth;
        }
        $xpos += $gap;
    }
}
	
	function OpenTag($tag, $attr)
	{
	    //Opening tag
	    switch($tag){
	        case 'STRONG':
	            $this->SetStyle('B',true);
	            break;
	        case 'EM':
	            $this->SetStyle('I',true);
	            break;
	        case 'B':
	        case 'I':
	        case 'U':
	            $this->SetStyle($tag,true);
	            break;
	        case 'A':
	            $this->HREF=$attr['HREF'];
	            break;
	        case 'IMG':
	            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
	                if(!isset($attr['WIDTH']))
	                    $attr['WIDTH'] = 0;
	                if(!isset($attr['HEIGHT']))
	                    $attr['HEIGHT'] = 0;
	                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
	            }
	            break;
	        case 'TR':
	        case 'BLOCKQUOTE':
	        case 'BR':
	            $this->Ln(5);
	            break;
	        case 'P':
	            $this->Ln(10);
	            break;
	        case 'FONT':
	            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
	                $coul=hex2dec($attr['COLOR']);
	                $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
	                $this->issetcolor=true;
	            }
	            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
	                $this->SetFont(strtolower($attr['FACE']));
	                $this->issetfont=true;
	            }
	            break;
	    }
	}
	
	function CloseTag($tag)
	{
	    //Closing tag
	    if($tag=='STRONG')
	        $tag='B';
	    if($tag=='EM')
	        $tag='I';
	    if($tag=='B' || $tag=='I' || $tag=='U')
	        $this->SetStyle($tag,false);
	    if($tag=='A')
	        $this->HREF='';
	    if($tag=='FONT'){
	        if ($this->issetcolor==true) {
	            $this->SetTextColor(0);
	        }
	        if ($this->issetfont) {
	            $this->SetFont('arial');
	            $this->issetfont=false;
	        }
	    }
	}
	
	function SetStyle($tag, $enable)
	{
	    //Modify style and select corresponding font
	    $this->$tag+=($enable ? 1 : -1);
	    $style='';
	    foreach(array('B','I','U') as $s)
	    {
	        if($this->$s>0)
	            $style.=$s;
	    }
	    $this->SetFont('',$style);
	}
	
	function PutLink($URL, $txt)
	{
	    //Put a hyperlink
	    $this->SetTextColor(0,0,255);
	    $this->SetStyle('U',true);
	    $this->Write(5,$txt,$URL);
	    $this->SetStyle('U',false);
	    $this->SetTextColor(0);
	}
	function Rotate($angle = -1,$x=-1,$y=-1) { 

        if($x==-1) 
            $x=$this->x; 
        if($y==-1) 
            $y=$this->y; 
        if($angle<=0) 
            $this->_out('Q'); 
		
        $angle=($angle < 0)?0:$angle; 
        if($angle>0) 

        { 
            $angle*=M_PI/180; 
            $c=cos($angle); 
            $s=sin($angle); 
            $cx=$x*$this->k; 
            $cy=($this->h-$y)*$this->k; 
             
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy)); 
        } 
    }
}