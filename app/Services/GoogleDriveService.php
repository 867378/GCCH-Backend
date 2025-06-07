<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\StorageAttributes;
use League\Flysystem\FileAttributes;
use Illuminate\Support\Str;

class GoogleDriveService
{   
    protected function getClient()
    {
        $client = new \Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect_uri'));
        $client->addScope(\Google_Service_Drive::DRIVE);

        // Set refresh token to get access tokens automatically
        $client->refreshToken(config('services.google.refresh_token'));

        return $client;
    }


    public function uploadFile($file, $customFileName){
        $folderPath = config('filesystems.disks.google.folderPath', '/');
        $extension = $file->getClientOriginalExtension();
        $uniqueSuffix = now()->timestamp . '_' . Str::random(8);
        $fileName = $customFileName . '_' .$uniqueSuffix . '.' . $extension;

        $path = Storage::disk('google')->putFileAs(
            $folderPath,
            $file,
            $fileName   
        );

        \Log::info('Uploaded path:', ['path' => $path]);

        if($path){
            $contents = Storage::disk('google')->listContents($folderPath, false);

            \Log::info('Drive folder contents:', ['contents' => $contents]);

            foreach ($contents as $content) {
                if($content instanceof FileAttributes && basename($content->path()) === $fileName) {

                    $fileId = $content->extraMetaData()['id'] ?? null;
                    
                    return [
                        'name' => basename($content->path()),
                        'path' => $content->path(),
                        'file_id' => $fileId,
                    ];
                }
            }
        }

        return null;
    }

    public function getFileEmbedUrl($fileId){
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }
    
    public function uploadPicture($file, $customFileName){
        $folderPath = config('filesystems.disks.google.picturePath', '/');
        $extension = $file->getClientOriginalExtension();
        $uniqueSuffix = now()->timestamp . '_' . Str::random(8);
        $fileName = $customFileName . '_' .$uniqueSuffix . '.' . $extension;

        $path = Storage::disk('google')->putFileAs(
            $folderPath,
            $file,
            $fileName   
        );

        \Log::info('Uploaded path:', ['path' => $path]);

        if($path){
            $contents = Storage::disk('google')->listContents($folderPath, false);

            \Log::info('Drive folder contents:', ['contents' => $contents]);

            foreach ($contents as $content) {
                if($content instanceof FileAttributes && basename($content->path()) === $fileName) {

                    $fileId = $content->extraMetaData()['id'] ?? null;
                    
                    return [
                        'name' => basename($content->path()),
                        'path' => $content->path(),
                        'file_id' => $fileId,
                    ];
                }
            }
        }

        return null;
    }
    
    public function getPublicImageUrl($fileId)
    {
        return "https://drive.google.com/thumbnail?id={$fileId}";
    }


    public function setPublicPermission($fileId)
    {
        $client = $this->getClient();

        $service = new \Google_Service_Drive($client);

        $permission = new \Google_Service_Drive_Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');

        try {
            $service->permissions->create($fileId, $permission);
            \Log::info('Permission set to public for file: ' . $fileId);
        } catch (\Exception $e) {
            \Log::error('Failed to set permission: ' . $e->getMessage());
        }
    }

    public function listFiles(){
        return Storage::disk('google')->listContents('/', false);
    }

    public function downloadFile($filePath){
        return Storage::disk('google')->get($filePath);
    }

    public function deleteFile($filePath){
        return Storage::disk('google')->delete($filePath);
    }

    public function getFileMetaData($fileId){
        $client = new \Google_Client();
        $client->setAccessToken(session('google_token'));

        $driveService = new \Google_Service_Drive($client);

        try {
            return $driveService->files->get($fileId);
        } catch (\Exception $e) {
            \Log::error('Error retrieving file metadata from Google Drive', ['error' => $e->getMessage()]);
            return null;
        }
    }
}