<?php

namespace EpgClient\Resource;

use EpgClient\Context\Category;
use EpgClient\Context\CategoryImage;

class CategoryImagesResource extends AbstractResource
{
    protected static $baseLocation = '/api/category_images';

    public function addImageToCategory(CategoryImage $image, Category $category)
    {
        $image->category = $category->getLocation();

        return $this->post($image);
    }
}
