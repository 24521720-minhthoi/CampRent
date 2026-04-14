<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UploadController extends Controller
{
    /**
     * Handle file uploads and return array of public URLs.
     * Accepts multipart form-data with files[]
     */
    public function upload(Request $request)
    {
        $allowedDisks = ['product-images', 'category-images', 'evidences'];

        $validated = $request->validate([
            'folder' => ['required', 'string', Rule::in($allowedDisks)],
            'files' => 'required|array|max:20',
            'files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov,avi|max:51200',
        ]);

        $disk = $validated['folder'];

        if ($disk === 'category-images' && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($disk === 'product-images' && !in_array($request->user()->role, ['admin', 'shop'], true)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $uploadedUrls = [];

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                $path = Storage::disk($disk)->putFile('', $file, 'public');
                if ($path) {
                    $baseUrl = config("filesystems.disks.$disk.url") ?? config('filesystems.disks.s3.url') ?? env('AWS_URL');
                    $root = config("filesystems.disks.$disk.root") ?? '';
                    $segments = array_filter([
                        rtrim($baseUrl, '/'),
                        trim($root, '/'),
                        ltrim($path, '/'),
                    ]);
                    $uploadedUrls[] = implode('/', $segments);
                }
            }
        }

        return response()->json(['urls' => $uploadedUrls]);
    }
}
