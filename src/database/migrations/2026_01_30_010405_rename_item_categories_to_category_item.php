<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameItemCategoriesToCategoryItem extends Migration
{
    public function up()
    {
        Schema::rename('item_categories', 'category_item');
    }

    public function down()
    {
        Schema::rename('category_item', 'item_categories');
    }
}
