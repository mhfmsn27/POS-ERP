<?php

namespace App\Process\MasterData;
 
use App\Process\Process;
use Illuminate\Support\Facades\Storage;
use Laravolt\Avatar\Facade as Avatar;

class UploadImageProcess extends Process
{
    public function uploadFile($image, $name, $path_public, $resize = true)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            $image_data = substr($image, strpos($image, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image_data = str_replace(' ', '+', $image_data);
            $image_data = base64_decode($image_data);

            if ($image_data === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        if ($resize == true) {
            $image = $this->resize_image($image_data, 500, 500);
        } else {
            $image = $image_data;
        }

        $path = strtolower(preg_replace("/[^0-9a-zA-Z]/", "-", $name));
        $imageName = $path . '' . '.' . $type . '';
        Storage::disk('local')->put('/' . $path_public . $imageName, $image);
        return  $path_public . $imageName;
    }

    public function createDafaultMedia($name, $dir)
    {
        $image = $this->createImage($name);

        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            $default_image = substr($image, strpos($image, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $default_image = str_replace(' ', '+', $default_image);
            $default_image = base64_decode($default_image);

            if ($default_image === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $image = str_replace('data:image/' . $type . ';base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $path = strtolower(preg_replace("/[^0-9a-zA-Z]/", "-", $name));
        $imageName = $path . '' . '.' . $type . '';
        Storage::disk('local')->put($dir . $path . '/' . $imageName, base64_decode($image));
        return  $dir . $path . '/' . $imageName;
    }

    public function resize_image($file, $w, $h, $crop = FALSE)
    {
        $src = imagecreatefromstring($file);
        if (!$src) return false;
        $width = imagesx($src);
        $height = imagesy($src);

        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        // Buffering
        ob_start();
        imagepng($dst);
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function unlinkFile($file)
    {
        if (file_exists($file)) {
            // jalankan hapus file
            unlink($file);
        }
    }

    public function createImage($name)
    {
        return Avatar::create($name)->setDimension(200)->setTheme('colorful')->toBase64();
    }
}
