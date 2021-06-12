<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use Spatie\PdfToImage\Pdf;
use Org_Heigl\Ghostscript\Ghostscript;




class FileController extends BaseController
{
    use  ValidatesRequests;

    public function fileUpload()
    {
        return view('fileUpload');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUploadPost(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
        ]);
        

try{
        Ghostscript::setGsPath("C:\Program Files\gs\gs9.54.0\bin\gswin64c.exe");
        $pdf = new Pdf($request->file);
        $name= uniqid();
        $totalPages = $this->countPages($request->file);
        $pathToWhereImageShouldBeStored = public_path() . "\pdf-upload\\$name-%d";
        $pdf->setOutputFormat('png')->saveImage($pathToWhereImageShouldBeStored);
        $html = ''; 

        foreach (range(1,$totalPages) as $pageNumber) {
           
            $html = "<div class='row'>";
            $html.="<div class='col-md-12'>";
            $html.="<div class='form-group'>";
             $src= url('/public/pdf-upload/').'/'.$name.'-'.$pageNumber.'.png';
            $html .= '<img src="' . $src
            .'"  class="form-control"  style="max-height:200px;max-width:200px" /> ';

            $html.="</div></div></div>"; 
            echo $html;
           
        }
       
          // return redirect()->back()->with('success','You have successfully upload file'); 
            }


       catch(\Exception $e){
        return redirect()->back()->with('error', $e->getMessage());
    }
    }


    function countPages($path) {
      $pdftext = file_get_contents($path);
      $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
      return $num;
  }


}
