<?php


/**
 * 
 */
class PDF extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i][0]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($data[$i][1]) ? $data[$i][1] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i][0],0,$a,true);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function Rox($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i][0]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($data[$i][1]) ? $data[$i][1] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i][0],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
    

    function footer()
    {
        # code...
    }
}

// Instanciation of inherited class
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->Image(base_url().'assets/dist/img/logo_pdf.png', 11, 10,'60','','png');
$pdf->SetFont('Arial','B',10);
$pdf->Ln(10);
$pdf->Cell(80,0,'PT.TRI TEGUH MANUNGGAL SEJATI',0,0,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(110,0,'Tanggal Cetak : '.date('d-m-Y'),0,1,'R');
$pdf->SetFont('Arial','',8);
$pdf->Ln(1);
$pdf->Cell(80,8,'Jl. Ir. Sutami KM. 6, Campang Raya, ',0,0,'L');
$pdf->Cell(107,8,'Jam Cetak : '.date('H:i:s'),0,1,'R');
$pdf->Cell(80,0,'Tanjung Karang Timur, Bandar Lampung',0,1,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Ln(5);
$pdf->cell(190,8,'Report Berdasarkan Grade',0,1,'C');
$pdf->SetFont('Arial','',8);
$pdf->cell(190,5,'Berisi report lembur pegawai berdasarkan grade '.$grade.' di bulan '.nama_bulan($bulan).' tahun '.$tahun,0,1,'C');
$pdf->Ln(1);
//Table with 20 rows and 4 columns
$pdf->SetWidths(array(10,40,30,40,30,40));
$pdf->Ln();
$pdf->SetFillColor(0,89,139);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(112);
$pdf->SetLineWidth(.1);
$pdf->SetFont('Arial','B',8);
$pdf->Row(array(
            array("NO"),
            array("NIK"),
            array("DEPARTEMEN"),
            array("JABATAN"),
            array("WAKTU LEMBUR"),
            array("Rp. LEMBUR")
));
$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial','',8);
foreach ($querys as $query) {
    if($query->departemen == 1) { $departemen = "FA";}
    if($query->departemen == 2) { $departemen = "IT";}
    if($query->departemen == 3) { $departemen = "PDCA";}
    if($query->departemen == 4) { $departemen = "HCS";}
    if($query->departemen == 5) { $departemen = "PPI";}
    if($query->departemen == 6) { $departemen = "QAQC";}
    if($query->departemen == 7) { $departemen = "WAREHOUSE";}
    if($query->departemen == 8) { $departemen = "BOF";}
    $pdf->Rox(array(
            array($no),
            array($query->nik_karyawan),
            array($departemen),
            array($query->nama_jabatan),
            array($query->jam." Jam"),
            array("Rp. ".number_format($query->total,0,',','.'))
    ));
    $no++;
}

$pdf->Output("RBG-".$grade."-".$bulan."-".$tahun.".pdf","I");
