<?php

namespace App\Http\Controllers\WebSite\Inquiry;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Barryvdh\DomPDF\Facade\Pdf;


class PdfMerge extends Controller
{
    public function pdfmerge(){
        return view('pdfmerge');

    }

    public function pdfmergesubmit(Request $request){
        $pdfname = $request->pdf_name ?? 'Merged_pdf';
        $pdf_header = $request->pdf_header ?? '';
        $pdf_footer = $request->pdf_footer ?? 0;
        if(count($request->file('pdf'))<2){
            echo count($request->file('pdf'));
            echo '<h2>Please upload more than one PDFs to Merge</h2>';exit;
        }
        $i=1;
        $files=array();
        foreach($request->file('pdf') as $file){
            $path = public_path();
            $name = rand(1000,9999)."_".$i.".pdf";
            $file->move($path, $name);
            $files[]=public_path()."/".$name;
            $i++;
        }
        $pdf = PdfMerger::init();
        foreach ($files as $f) {
            if(file_exists($f)){
                $pdf->addPDF($f, 'all','P');
            }
        }
        $pdf->merge("","",$pdf_header,$pdf_footer);
        $pdf->save($pdfname.".pdf",'browser');
        foreach ($files as $f) {
            if(file_exists($f)){
                unlink($f);
            }
        }
        echo count($request->files); exit;
    }

    public function image_to_pdf(){
        return view('image_to_pdf');

    }

    public function image_to_pdf_submit(Request $request){
        $pdfname = $request->pdf_name ?? 'Merged_pdf';
        $pdf_header = $request->pdf_header ?? '';
        $pdf_footer = $request->pdf_footer ?? 0;
        $alignment = $request->alignment ?? 'left';
        $display = $request->display ?? 'auto';
        $text_display = $request->text_display ?? 'before';
        if(count($request->file('image'))<1){
            echo count($request->file('image'));
            echo '<h2>Please upload more than one Image to convert PDF</h2>';exit;
        }
        $i=1;
        $files=$text=array();
        foreach($request->file('image') as $file){
            $path = public_path();
            $name = rand(1000,9999)."_".$i.".jpg";
            $file->move($path, $name);
            $files[]=public_path()."/".$name;
            $i++;
        }
        foreach($request->text as $val){
            $text[] = $val;
        }
        view()->share(["files"=>$files,"pdfname"=>$pdfname,"pdf_header"=>$pdf_header,"pdf_footer"=>$pdf_footer,"alignment"=>$alignment,"display"=>$display,
                        "text"=>$text,"text_display"=>$text_display]);
        $pdf = Pdf::loadView('ImagesPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        $mergerdPDF = $pdf->download($pdfname.".pdf");
        foreach ($files as $f) {
            if(file_exists($f)){
                unlink($f);
            }
        }
        return $mergerdPDF;
        echo count($request->image); exit;
    }
}
