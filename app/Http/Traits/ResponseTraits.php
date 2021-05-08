<?php


namespace App\Http\Traits;

use App\Http\Traits\Utils;
trait ResponseTraits
{

    public function messages(){
        return [
            'required' => ':attribute енгізіңіз!',
            'unique'   => "көрсетілген :attribute базада бар",
            "min"      => ":attribute ұзындығы кемінде :min болу керек"
        ];

    }


    public function successResponse($data,  $message=null,$status_code=null){
        $output['success'] = true;
        $output['data'] = $data;
        $output['message'] = $message;

        if (!is_null($status_code)){
            $output["status_code"] = $status_code;
        }

        return response()->json($output, Utils::$SUCCESS_CODE);
    }

    public function errorResponse($status_code, $message=null, $validation_error, $data=null){
        $output['success'] = false;
        $output['status_code'] = $status_code;
        $output['message'] = $message;
        $output['validations'] = $validation_error;
        $output['data'] = $data;
        return response()->json($output, 400);
    }

}
