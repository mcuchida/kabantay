<?php

class ImageProcessor {
 
        protected $imagine;

        /**
        * Initialize the image service
        * @return void
        */
        public function __construct()
        {
                $library = Config::get('image.library', 'imagick');
                $this->imagine = new Imagine\Gd\Imagine();
        }

        public function resize($filename, $width, $height, $quality = 100, $crop = false)
        {
                $path = public_path() . '/media/' . $filename;

                $options = array(
                        'quality' => $quality
                );

                try {
                        $image = $this->imagine->open($path);
                } catch (Exception $e) {
                        return false;
                }

                $target_dir_path = public_path() . '/media/' . $width . 'x' .$height;
                $new_path        = $target_dir_path . '/'. $filename;

                if(!File::isDirectory($target_dir_path)) {
                        File::makeDirectory($target_dir_path);
                }

                $dimension = new Imagine\Image\Box($width, $height);

                $image->resize($dimension)->save($new_path, $options);
        }

        public function createDifferentSizes($filename, $quality)
        {
                $this->resize($filename, 580, 326, $quality);
                $this->resize($filename, 290, 163, $quality);
                $this->resize($filename, 1000, 563, $quality);
                return true;
        }
 
}