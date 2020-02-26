<?php

namespace Control\Packages\FileManager\Http\Controllers;

use Control\Http\Controllers\Controller;
use Control\Packages\FileManager\Http\Requests\Folders\UpdateRequest;
use Control\Packages\FileManager\Resources\FolderResource;
use Control\Tools\Meta\Message;
use Ideil\LaravelFileManager\Models\FileFolder;

class FoldersController extends Controller
{
    /**
     * @param UpdateRequest $request
     * @param FileFolder $fileFolder
     * @return array
     */
    public function update(UpdateRequest $request, FileFolder $fileFolder): array
    {
        $fileFolder->update([
            'name' => $request->name,
        ]);

        return [
            'data' => [
                'item' => FolderResource::make($fileFolder),
            ],
            'meta' => [
                Message::make(_('Файл оновлено'))->success(),
            ],
        ];
    }
}
