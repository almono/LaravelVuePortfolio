<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Barryvdh\DomPDF\Facade as PDF;

use App\Models\Curriculum;
use Tymon\JWTAuth\JWTAuth;

class PersonalController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCurriculumData(Request $request)
    {
        $result['cv_data'] = Curriculum::first();
        $status = '400';
        if(!is_null($result['cv_data'])) {
            $status = '200';
        }

        return response()->json($result, $status);
    }

    public function downloadCV(Request $request)
    {
        $params = $request->all();
        $cvData = Curriculum::first();
        //$cvData = json_decode($cvData);

        if(isset($cvData) && !is_null($cvData)) {
            $cvLang = 'EN';
        }
        //dd($_SERVER['DOCUMENT_ROOT']);
        $data = [
            'mainInfo' => true,
            'cvData' => $cvData,
            'educationEnabled' => true,
            'experienceEnabled' => true,
            'skillsEnabled' => true,
            'interestsEnabled' => true,
            'otherEnabled' => true
        ];

        $pdf = PDF::loadView('Personal.cv', $data);
        //PDF::set_base_path($_SERVER['DOCUMENT_ROOT'] . '\public\css\\');
       //$pdf->setPaper('a4' , 'portrait');
        return $pdf->download('invoice.pdf');
        return $pdf->output();

    }
}
