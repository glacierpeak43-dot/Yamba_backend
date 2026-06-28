<?php

namespace App\Admin\Controllers;

use App\Models\NationalHelpLines;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class NationalHelpLinesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'NationalHelpLines';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new NationalHelpLines());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('contact_name', __('Contact name'));
        $grid->column('contact', __('Contact'));
        $grid->column('email', __('Email'));
        $grid->column('address', __('Address'));
        $grid->column('services', __('Services'));
        $grid->column('availability', __('Availability'));
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
        $show = new Show(NationalHelpLines::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('contact_name', __('Contact name'));
        $show->field('contact', __('Contact'));
        $show->field('email', __('Email'));
        $show->field('address', __('Address'));
        $show->field('services', __('Services'));
        $show->field('availability', __('Availability'));
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
        $form = new Form(new NationalHelpLines());

        $form->text('title', __('Title'))->required();
        $form->text('contact_name', __('Contact name'));
        $form->text('contact', __('Contact'));
        $form->email('email', __('Email'));
        $form->text('address', __('Address'))->required();
        $form->textarea('services', __('Services'))->required();
        $form->textarea('availability', __('Availability'))->required();

        return $form;
    }
}
