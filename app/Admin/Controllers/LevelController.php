<?php

namespace App\Admin\Controllers;

use App\Models\Level;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LevelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Level';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Level());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('level_number', __('Level number'));
        $grid->column('color', __('Color'));
        $grid->column('points', __('Points'));
        $grid->column('min', __('Min'));
        $grid->column('max', __('Max'));
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
        $show = new Show(Level::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('level_number', __('Level number'));
        $show->field('color', __('Color'));
        $show->field('points', __('Points'));
        $show->field('min', __('Min'));
        $show->field('max', __('Max'));
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
        $form = new Form(new Level());

        $form->text('name', __('Name'));
        $form->text('description', __('Description'));
        $form->number('level_number', __('Level number'));
        $form->color('color', __('Color'));
        $form->number('points', __('Points'));
        $form->number('min', __('Min'));
        $form->number('max', __('Max'));

        return $form;
    }
}
