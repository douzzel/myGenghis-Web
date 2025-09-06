<?php
/* The PHP class IMAGE is used to compress images. */

class IMAGE
{
    /**
     * Compress images
     *
     * @param  string $src
     * @param  string $dest
     * @param  string $filename
     * @param  int|string[] $size width of image
     * @param  bool $deleteSrc delete the original image
     * @param  string $fileType png, jpg, gif…
     * @param  bool $absoluteFileName Don't happen size to the file name
     * @return string|string[]
     */
    public static function compress(string $src, string $dest, string $filename, $size = 500, bool $deleteSrc = true, $fileType = '', bool $absoluteFileName = false)
    {
        if ($fileType === "gif") {
            $path = "/{$dest}/{$filename}.gif";
            rename(getcwd()."/{$src}", getcwd()."/{$path}");
        } else {
            $image = new Imagick($_SERVER['DOCUMENT_ROOT']."/{$src}");
            $image->setInterlaceScheme(Imagick::INTERLACE_PLANE);

            if (is_array($size)) {
                $path = [];
                foreach ($size as $value) {
                    if ($image->getImageWidth() > $value)
                        $image->thumbnailImage($value, 0);
                    $p = "/{$dest}/{$filename}x{$value}.".($fileType == "png" ? "png" : "jpg");
                    $path[] = $p;
                    $image->writeImage($_SERVER['DOCUMENT_ROOT'].$p);
                }
            } else {
                if ($image->getImageWidth() > $size)
                    $image->thumbnailImage($size, 0);
                if ($absoluteFileName) {
                    $path = "/{$dest}/{$filename}.".($fileType == "png" ? "png" : "jpg");
                } else {
                    $path = "/{$dest}/{$filename}x{$size}.".($fileType == "png" ? "png" : "jpg");
                }
                $image->writeImage($_SERVER['DOCUMENT_ROOT'].$path);
            }
            if ($deleteSrc) {
                unlink($src);
            }
        }

        return $path;
    }

    /**
     * Upload and compress images
     *
     * @param  mixed $file
     * @param  string $dest
     * @param  string $id
     * @param  int|string[] $width width of image
     * @param  bool $absoluteFileName Don't happen size to the file name
     * @return string|bool
     */
    public static function upload($file, string $dest, string $id = "", $size = 500, bool $absoluteFileName = false)
    {
        if (!isset($file))
            return false;
        $handle = new upload($file, 'fr_FR');
        $id = str_replace(' ', '_', preg_replace("/[^A-Za-z0-9 ]/", '', html_entity_decode($id)));
        if ($handle->uploaded) {
            $handle->allowed = ['image/*'];
            $handle->process($dest);
            $time = time();
            return self::compress("{$dest}/{$handle->file_dst_name}", $dest, ($absoluteFileName ? $id : ($id ? "{$time}-{$id}" : "{$time}")), $size, true, $handle->image_src_type, $absoluteFileName);
        }
    }
}
