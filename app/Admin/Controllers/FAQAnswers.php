<?php

namespace App\Admin\Controllers;

use App\Models\FrequentlyAskedQuestionAnswers;
use App\Models\FrequentlyAskedQuestions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FAQAnswers extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'FrequentlyAskedQuestionAnswers';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FrequentlyAskedQuestionAnswers());

        $grid->column('id', __('Id'));
        $grid->column('answer', __('Answer'))->display(function ($value) {
            return substr(strip_tags($value), 0, 100);
        });
        $grid->column('question_id', __('Question id'))->display(function ($value) {
            $category = FrequentlyAskedQuestions::find($value);
            return $category->question;
        });;
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
        $show = new Show(FrequentlyAskedQuestionAnswers::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('answer', __('Answer'));
        $show->field('question_id', __('Question id'));
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
        $form = new Form(new FrequentlyAskedQuestionAnswers());
        $form->select('question_id', __('Select Question '))->options(FrequentlyAskedQuestions::all()->pluck('question', 'id'))->required();
        $form->ckeditor('answer', __('Answer'))->required();
        return $form;
    }
}
