<?php

namespace App\Http\Services;

use App\Models\Attachment;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AttachmentService {

    public function storefile($request)
    {
        $file = $request->file;
        $originalName = $file->getClientOriginalName();

        // Ensure the file extension is valid and there is no path traversal in the file name
        if (preg_match('/\.[^.]+\./', $originalName)) {
            throw new Exception(trans('general.notAllowedAction'), 403);
        }


        // Check for path traversal attack (e.g., using ../ or ..\ or / to go up directories)
        if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new Exception(trans('general.pathTraversalDetected'), 403);
        }

        // Validate the MIME type to ensure it's an image
        $allowedMimeTypes = ['file/pdf', 'file/png', 'file/doc', 'file/docx'];
        $mime_type = $file->getClientMimeType();

        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new FileException(trans('general.invalidFileType'), 403);
        }

        // Generate a safe, random file name
        $fileName = Str::random(32);

        $extension = $file->getClientOriginalExtension(); // Safe way to get file extension
        $filePath = "Files/{$fileName}.{$extension}";

        // Store the file securely
        $path = Attachment::disk('local')->putFileAs('Files', $file, $fileName . '.' . $extension);

        // Get the full URL path of the stored file
        $url = Storage::disk('local')->url($path);

        // Store image metadata in the database
        $file = Attachment::create([
            'name' => $fileDTO->name ?? $originalName,
            'path' => $url,
            'mime_type' => $mime_type,
            'alt_text' => $fileDTO->alt_text ?? null,
        ]);

        return $file;
    }

}
