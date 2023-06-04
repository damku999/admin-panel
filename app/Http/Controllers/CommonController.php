<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class CommonController extends Controller
{
    public function refreshCaptchaImage(Request $request)
    {
        return view('admin.refresh_captcha_image');
    }

    public function deleteCommon(Request $request)
    {
        if ($request->record_id != '' && $request->model != '') {
            $model_name = '\\App\\Models\\' . $request->model;
            $model_obj = new $model_name;
            $record = $model_obj->find($request->record_id);
            if ($record) {
                $record->delete();
                return response()->json(['status' => 'success', 'message' => $request->display_title . ' Has been deleted successfully.']);
            } else {
                return response()->json(['status' => 'error', 'message' => $request->display_title . ' not found.']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function activeInactiveCommon(Request $request)
    {
        if ($request->record_id != '' && $request->model != '') {
            $model_name = '\\App\\Models\\' . $request->model;
            $model_obj = new $model_name;
            $record = $model_obj->find($request->record_id);
            $temp_label = '';

            if ($record->status == 'A') {
                $record->status = 'I';
                $temp_label = ' In Activated ';
            } else {
                $record->status = 'A';
                $temp_label = ' Activated ';
            }

            if ($record->save()) {
                return response()->json(['status' => 'success', 'message' => $request->display_title . $temp_label . ' successfully.']);
            } else {
                return response()->json(['status' => 'error', 'message' => $request->display_title . $temp_label . ' not found.']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function getImage(Request $request, $file_path, $file_name)
    {
        $file = storage_path() . DIRECTORY_SEPARATOR . $file_path . DIRECTORY_SEPARATOR . $file_name;
        return response()->file($file);
    }

    public function getImage1(Request $request, $file_path1, $file_path2, $file_name)
    {
        $file = storage_path() . DIRECTORY_SEPARATOR . $file_path1 . DIRECTORY_SEPARATOR . $file_path2 . DIRECTORY_SEPARATOR . $file_name;
        return response()->file($file);
    }
}
