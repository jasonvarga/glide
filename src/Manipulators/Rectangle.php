<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Interfaces\Manipulator;
use League\Glide\Request;

class Rectangle implements Manipulator
{
    /**
     * Perform rectangle image manipulation.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(Request $request, Image $image)
    {
        $coordinates = $this->getCoordinates($image, $request->getParam('rect'));

        if ($coordinates) {
            $coordinates = $this->limitCoordinatesToImageBoundaries($image, $coordinates);

            $image->crop(
                $coordinates[0],
                $coordinates[1],
                $coordinates[2],
                $coordinates[3]
            );
        }
    }

    /**
     * Resolve coordinates.
     * @param  Image  $image     The source image.
     * @param  string $rectangle The rectangle.
     * @return int[]  The resolved coordinates.
     */
    public function getCoordinates(Image $image, $rectangle)
    {
        $coordinates = explode(',', $rectangle);

        if (count($coordinates) !== 4 or
            !ctype_digit($coordinates[0]) or
            !ctype_digit($coordinates[1]) or
            !ctype_digit($coordinates[2]) or
            !ctype_digit($coordinates[3]) or
            $coordinates[0] > $image->width() or
            $coordinates[2] >= $image->width() or
            $coordinates[1] > $image->height() or
            $coordinates[3] >= $image->height()) {
            return false;
        }

        return [
            (int) $coordinates[0],
            (int) $coordinates[1],
            (int) $coordinates[2],
            (int) $coordinates[3]
        ];
    }

    /**
     * Limit coordinates to image boundaries.
     * @param  Image $image       The source image.
     * @param  int[] $coordinates The coordinates.
     * @return int[] The limited coordinates.
     */
    public function limitCoordinatesToImageBoundaries(Image $image, Array $coordinates)
    {
        if ($coordinates[0] > ($image->width() - $coordinates[2])) {
            $coordinates[0] = $image->width() - $coordinates[2];
        }

        if ($coordinates[1] > ($image->height() - $coordinates[3])) {
            $coordinates[1] = $image->height() - $coordinates[3];
        }

        return $coordinates;
    }
}
