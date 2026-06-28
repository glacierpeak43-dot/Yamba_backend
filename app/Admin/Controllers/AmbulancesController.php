<?php

namespace App\Admin\Controllers;

use App\Models\Ambulances;
use App\Models\University;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AmbulancesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Ambulances';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ambulances());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('contact_name', __('Contact name'));
        $grid->column('contact', __('Contact'));
        $grid->column('email', __('Email'));
        $grid->column('address', __('Address'));
        $grid->column('university_id', __('University id'))->display(function ($value) {
            $category = University::find($value);
            return $category->name;
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $user = auth()->user();
        if (!$user->isRole('administrator')) {
            $grid->model()->where('university_id', auth()->user()->university_id);
        }
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
        $show = new Show(Ambulances::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('contact_name', __('Contact name'));
        $show->field('contact', __('Contact'));
        $show->field('email', __('Email'));
        $show->field('address', __('Address'));
        $show->field('university_id', __('University id'));
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
        $form = new Form(new Ambulances());

        $form->text('title', __('Title'))->required();
        $form->text('contact_name', __('Contact name'));
        $form->text('contact', __('Contact'));
        $form->email('email', __('Email'));
        $form->text('address', __('Address'))->required();
        $user = auth()->user();
        $universities = !$user->isRole('administrator') ? University::where('id', $user->university_id) : University::all();
        $form->select('university_id', __('Select University '))->options($universities->pluck('name', 'id'))->required();

        return $form;
    }
}
