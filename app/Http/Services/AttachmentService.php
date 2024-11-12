<?php

namespace App\Http\Services;

use Exception;
use Validator;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AttachmentService {

    public function upload(Request $request){
        $validator=Validator::make($request->all(),[
            'file' => 'required|file|max:5120|mimes:png,jpg,jpeg,gif,pdf,doc,docx',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>'File upload faild.'],422);
        }
        $file=$request->file('file');
        $mimeType= $file->getMimetype();
        if(! in_array($mimeType,['image/jpeg','image/png','application/pdf'])){
            return response()->json(['error' => 'Invalid file type.'],422);

        }
        $originalFilename=$request->file('file')->getClientOriginalName();
        $sanitizedFilename= preg_replace('/[^A-Za-z0-9_\-\.]/','_',$originalFilename);
        $baseFilename =strtolower(pathinfo($sanitizedFilename,PATHINFO_FILENAME));
        if(strpos($baseFilename,'.') !== false ){
            return response()->json(['error'=>'Invalid file type.'],422);
        }
        //Ensure the sanitized Filename dos notcontain any directory traversal sequences
        $sanitizedFilename= basename($sanitizedFilename);

        //Generate a unique file name with the sanitized filename
        $uniqueId =Str::uuid();
        $filename=$uniqueId.'_'.$sanitizedFilename;
        //storethe file in the 'uploads' directory using the storage facade and retrieve its path
        $path = Storage::disk('public')->putFileAs('uploads',$request->file('file'),$filename);
        return response()->json(['path' => $path,'filename'=>$filename],201);
    }
    

}
