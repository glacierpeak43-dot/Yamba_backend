<?php

namespace App\Admin\Controllers;

use App\Models\FAQCategory;
use App\Models\FAQSubcategory;
use App\Models\FrequentlyAskedQuestions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FAQ extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'FrequentlyAskedQuestions';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FrequentlyAskedQuestions());

        $grid->column('id', __('Id'));
        $grid->column('question', __('Question'));
        $grid->column('key', __('Key'));
        $grid->column('f_a_q_subcategories', __('Sub Category'))->display(function ($value) {
            $category = FAQSubcategory::find($value);
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
        $show = new Show(FrequentlyAskedQuestions::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('question', __('Question'));
        $show->field('key', __('Key'));
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
        $form = new Form(new FrequentlyAskedQuestions());

        $form->text('question', __('Question'))->required();
        $form->select('f_a_q_subcategories', __('Select FAQ Subcategory '))->options(FAQSubcategory::all()->pluck('name', 'id'))->required();
        $form->number('key', __('Key'))->required();

        return $form;
    }
}