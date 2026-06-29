<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Http\Controllers\Controller;
use Smalot\PdfParser\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Aspose\Words\WordsApi;
use Aspose\Words\Model\Requests\{ConvertDocumentRequest};

class FileController extends Controller
{
    public static function index() {
      return view('pdftotext');
    }

    public static function store_old(Request $request) {

        $file = $request->file;

        $request->validate([
            'file' => 'required|mimes:pdf',
        ]);

        // use of pdf parser to read content from pdf
        $fileName = $file->getClientOriginalName();

        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($file->path());
        $content = $pdf->getText();

        Storage::disk('local')->put('file.txt', $content);
        $filePath = storage_path('app/file.txt');
        if (! file_exists($filePath)) {
            // Some response with error message...
        }

        return response()->download($filePath);

        // return response()->streamDownload(function () use ($content) {
        //        echo $content;
        //  }, 'content.txt');

        //    $headers = [
        //      'Content-Type' => 'application/plain',
        //      'Content-Description' => 'file.txt',
        //   ];
        //   return Response::make($content, 200, $headers);

        //dd($content);
    }

    public static function store(Request $request) {

        $file = $request->file;

        $request->validate([
            'file' => 'required|mimes:pdf',
        ]);


        $wordsApi = new WordsApi('d0e880ac-5bd5-4a50-bc93-89c430df7498', 'd47236bb1d259f31c0012aef59d6e70f');

        //$doc = storage_path('app/test.pdf');
        $doc = $file->path();
        $request = new ConvertDocumentRequest(
            $doc, "docx", NULL, NULL, NULL, NULL
        );
        $convert = $wordsApi->convertDocument($request);
        // dd($convert);
        return response()->download($convert,'output.docx');

    }
}
