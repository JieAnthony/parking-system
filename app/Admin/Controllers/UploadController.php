<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Traits\HasUploadedFile;

class UploadController
{
    use HasUploadedFile;

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle()
    {
        $disk = $this->disk();
        // 判断是否是删除文件请求
        if ($this->isDeleteRequest()) {
            // 删除文件并响应
            return $this->deleteFileAndResponse($disk);
        }
        // 获取上传的文件
        $file = $this->file();
        $extension = $file->getClientOriginalExtension();
        $dir = in_array($extension, $this->getImageExtension())
            ? config('admin.upload.directory.image')
            : config('admin.upload.directory.file');
        $result = $disk->putFile($dir, $file);
        $url = $disk->url($result);

        return $result
            ? $this->responseUploaded($url, $url)
            : $this->responseErrorMessage('文件上传失败');
    }

    /**
     * @return string[]
     */
    private function getImageExtension()
    {
        return [
            'jpeg',
            'png',
            'bmp',
            'gif',
            'jpg',
        ];
    }
}
