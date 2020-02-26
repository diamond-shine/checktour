<?php

namespace Control\Packages\FileManager\Http\Controllers;

use Control\Http\Controllers\Controller;
use Control\Packages\FileManager\Http\Requests\Files\UpdateRequest;
use Control\Packages\FileManager\Resources\FileResource;
use Control\Tools\Meta\Message;
use Ideil\LaravelFileManager\Models\File;

class FilesController extends Controller
{
    /**
     * @param UpdateRequest $request
     * @param File $file
     * @return array
     */
    public function update(UpdateRequest $request, File $file): array
    {
        $file->update([
            'name' => $request->name,
            'alt' => $request->alt,
        ]);

        return [
            'data' => [
                'item' => FileResource::make($file),
            ],
            'meta' => [
                Message::make(_('Файл оновлено'))->success(),
            ],
        ];
    }
}
