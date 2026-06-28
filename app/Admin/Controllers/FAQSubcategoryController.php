<?php

namespace App\Admin\Controllers;

use App\Models\FAQCategory;
use App\Models\FAQSubcategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FAQSubcategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'FAQSubcategory';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FAQSubcategory());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('key', __('Key'));
        $grid->column('f_a_q_subcategories', __('Category'))->display(function ($value) {

            $category = FAQCategory::find($value);
            return $category->name;
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(FAQSubcategory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('key', __('Key'));
        $show->field('f_a_q_subcategories', __('F a q category id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new FAQSubcategory());

        $form->text('name', __('Name'));
        $form->number('key', __('Key'));
        $form->select('f_a_q_subcategories', __('Select Category '))->options(FAQCategory::all()->pluck('name', 'id'))->required();
        return $form;
    }
}
