<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('articles', ArticleController::class);
    $router->resource('users', UserController::class);

    $router->resource('categories', CategoryController::class);
    $router->resource('universities', UniversityController::class);
    $router->resource('levels', LevelController::class);
    $router->resource('roles', RoleController::class);
    $router->resource('ambulances', AmbulancesController::class);
    $router->resource('national-help-lines', NationalHelpLinesController::class);
    $router->resource('near-by-supports', NearBySupportController::class);
    $router->resource('police-contacts', PoliceContactsController::class);
    $router->resource('fire-stations', FireStationsController::class);
    $router->resource('university-help-lines', UnivesityHelplineController::class);
    $router->resource('forums', ForumController::class);
    $router->resource('forum-categories', ForumCategoriesController::class);
    $router->resource('app-carousel-pictures', AppCarouselController::class);
    $router->resource('abuse-reports', AbuseReportsController::class);
    $router->resource('frequently-asked-question-answers', FAQAnswers::class);
    $router->resource('frequently-asked-questions', FAQ::class);
    $router->resource('feed-backs', FeedBackController::class);
    $router->resource('f-a-q-categories', FAQCategoryController::class);
    $router->resource('f-a-q-subcategories', FAQSubcategoryController::class);
    $router->resource('article-comments', ArticleCommentController::class);
    $router->resource('article-comment-reports', ArticleCommentReportController::class);
    $router->resource('article-comment-reply-reports', ArticleCommentReplyReportController::class);
    $router->resource('forum-comment-reply-reports', ForumCommentReplyReportController::class);
    $router->resource('forum-comment-reports', ForumCommentReportController::class);
    $router->resource('chat-reports', ChatReportsController::class);
    $router->resource('notifires', NotifireController::class);
    $router->resource('awards', AwardsController::class);
    $router->resource('forced-updates-versions', VersionController::class);
});

// Allow roles `administrator` and `editor` access the routes under group.
Route::group([
    'middleware' => 'admin.permission:allow,administrator,editor',
], function ($router) {
});

// Deny roles `developer` and `operator` access the routes under group.
Route::group([
    'middleware' => 'admin.permission:deny,developer,operator',
], function ($router) {

    $router->resource('users', UserController::class);
});

// User has permission `edit-post`、`create-post` and `delete-post` can access routes under group.
Route::group([
    'middleware' => 'admin.permission:check,edit-post,create-post,delete-post',
], function ($router) {
    $router->resource('articles', ArticleController::class);
});
