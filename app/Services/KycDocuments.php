<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class KycDocuments
{
    public function store(UploadedFile $file): string
    {
        return basename($file->store('private/kyc', 'local'));
    }
    public function response(string $name)
    {
        abort_unless($name !== '' && basename($name) === $name, 404);
        $path = 'private/kyc/' . $name;
        abort_unless(Storage::disk('local')->exists($path), 404);
        return response()->file(Storage::disk('local')->path($path), [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }
}
