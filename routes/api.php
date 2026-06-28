<?php

use App\Http\Controllers\AbuseReportsController;
use App\Http\Controllers\AlertPeersController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ArticlesVotesController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DeleteAccountController;
use App\Http\Controllers\EmergencyContactsController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\ForumCommentVoteController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VersionController;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) {
    return $request->user();
});
//universities
Route::resource('universities', UniversityController::class);
Route::get('faq', [FAQController::class, 'getFrequentlyAskedQuestions']);
Route::Post('search_faq', [FAQController::class, 'searchFaq']);
Route::get('versions', [VersionController::class, 'index']);

Route::get('/email/verify/{id}', [VerificationController::class, 'emailVerify'])->middleware(['signed'])->name('verification.verify');

//users
Route::group(['prefix' => 'users'], function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/refresh', [LoginController::class, 'refreshUser'])->middleware('auth:sanctum');
    Route::get('/email/verify/{id}', [VerificationController::class, 'emailVerify'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resendEmailVerification'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');


    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware(['auth:sanctum', 'verified'])->name('verification.notice');

    Route::post('/update/{user}', [UserProfileController::class, 'updateUser'])->middleware(['auth:sanctum', 'verified']);
    //profile
    Route::post('/profile', [UserProfileController::class, 'store'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/profile/level/update', [UserProfileController::class, 'updateProfileLevel'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/profile', [UserProfileController::class, 'show'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/profile/update', [UserProfileController::class, 'update'])->middleware(['auth:sanctum', 'verified']);
    Route::delete('/profile/delete', [UserProfileController::class, 'delete'])->middleware(['auth:sanctum', 'verified']);

    Route::get('contacts', [EmergencyContactsController::class, 'getContacts']);
    Route::get('counsellors', [UserController::class, 'getCounsellors'])->middleware(['auth:sanctum', 'verified']);;
    Route::get('counsellors/{user}', [UserController::class, 'getCounsellor'])->middleware(['auth:sanctum', 'verified']);
    Route::get('requestMyReferralLink',[\App\Http\Controllers\ReferralController::class, 'requestMyReferralLink'])->middleware(['auth:sanctum', 'verified']);
});

//levels
Route::resource('levels', LevelController::class)->middleware(['auth:sanctum', 'verified']);
Route::get('get_sliders', [SliderController::class, 'getSliders'])->middleware(['auth:sanctum', 'verified']);
Route::get('delete_account',[DeleteAccountController::class,'deleteAccount'])->middleware(['auth:sanctum', 'verified']);
Route::get('restore_account',[DeleteAccountController::class,'restoreAccount'])->middleware(['auth:sanctum', 'verified']);

//articles
Route::group(['prefix' => 'articles'], function () {
    Route::resource('/', ArticleController::class)->middleware(['auth:sanctum', 'verified'])->except(['show']);
    Route::post('/report_comment', [ArticleController::class, 'reportArticleComment'])->middleware(['auth:sanctum', 'verified']);;
    Route::post('/report_comment_reply', [ArticleController::class, 'reportArticleCommentReply'])->middleware(['auth:sanctum', 'verified']);;
    Route::get('/{article}', [ArticleController::class, 'show'])->middleware(['auth:sanctum', 'verified']);
    //commenting on articles
    Route::post('/{article}/comment', [ArticleController::class, 'commentArticle'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/profile/view', [UserProfileController::class, 'updateArticleViews'])->middleware(['auth:sanctum', 'verified']);
});

Route::get('article/comments/{article}', [ArticleController::class, 'comments'])->middleware(['auth:sanctum', 'verified']);
Route::post('article/comments/{articleComment}', [ArticleController::class, 'articleCommentReply'])->middleware(['auth:sanctum', 'verified']);

Route::group(['prefix' => 'comments'], function () {

    Route::post('/{articleComment}/vote', [ArticlesVotesController::class, 'voteArticleComment'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/reply/{articleCommentReply}/vote', [ArticlesVotesController::class, 'voteArticleCommentReply'])->middleware(['auth:sanctum', 'verified']);

    Route::post('/{articleComment}/unvote', [ArticlesVotesController::class, 'unvoteArticleComment'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/reply/{articleCommentReply}/unvote', [ArticlesVotesController::class, 'unvoteArticleCommentReply'])->middleware(['auth:sanctum', 'verified']);
});

//forums
Route::group(['prefix' => 'forums'], function () {
    Route::resource('/', ForumController::class)->middleware(['auth:sanctum', 'verified'])->except(['show']);
    Route::get('/verification', [ForumController::class, 'checkForumConsent'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/categories', [ForumController::class, 'getForumCategories'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/accept_terms', [UserProfileController::class, 'acceptForumTermsAndConditions'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/category/{id}', [ForumController::class, 'getForumsByCategory'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/{forum}', [ForumController::class, 'show'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/comments/{id}', [ForumController::class, 'getForumComments'])->middleware(['auth:sanctum', 'verified']);
    //commenting on forums
    Route::post('report_comment', [ForumController::class, 'reportForumComment'])->middleware(['auth:sanctum', 'verified']);
    Route::post('report_comment_reply', [ForumController::class, 'reportForumCommentReply'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/{forum}/comment', [ForumController::class, 'commentForum'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/forum/{comment}/reply', [ForumController::class, 'forumCommentReply'])->middleware(['auth:sanctum', 'verified']);

    Route::group(['prefix' => 'comments'], function () {
        Route::post('/{forumComment}/vote', [ForumCommentVoteController::class, 'voteForumComments'])->middleware(['auth:sanctum', 'verified']);
        Route::post('/reply/{forumCommentReply}/vote', [ForumCommentVoteController::class, 'voteForumCommentsReply'])->middleware(['auth:sanctum', 'verified']);

        Route::post('/{forumComment}/unvote', [ForumCommentVoteController::class, 'unvoteForumComments'])->middleware(['auth:sanctum', 'verified']);
        Route::post('/reply/{forumCommentReply}/unvote', [ForumCommentVoteController::class, 'unvoteForumCommentsReply'])->middleware(['auth:sanctum', 'verified']);
    });
});


//article categories
Route::group(['prefix' => 'categories'], function () {
    Route::get('/articles', [ArticleController::class, 'getArtcleCategories']);
    Route::get('/{category}/articles', [ArticleController::class, 'getArticleCategory']);
    Route::get('/{category}', [ArticleController::class, 'getCategory']);
});

//friends
Route::group(['prefix' => 'friends'], function () {
    Route::post('/request', [ChatController::class, 'sendFriendRequest'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/requests', [ChatController::class, 'getFriendRequests'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/accept', [ChatController::class, 'acceptFrientRequest'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/reject', [ChatController::class, 'rejectFriendRequest'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/block', [ChatController::class, 'blockFriendRequestUser'])->middleware(['auth:sanctum', 'verified']);
    Route::post('/withdraw', [ChatController::class, 'withDrawFriendRequestUser'])->middleware(['auth:sanctum', 'verified']);
});

Route::group(['prefix' => 'notifications'], function () {
    Route::get('/', [NotificationsController::class, 'getUserNotifications'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/read/{id}', [NotificationsController::class, 'markNotificationAsRead'])->middleware(['auth:sanctum', 'verified']);
    Route::get('/delete/{id}', [NotificationsController::class, 'deleteUserNotification'])->middleware(['auth:sanctum', 'verified']);
});


//chats

Route::get('/chats', [ChatController::class, 'userChats'])->middleware(['auth:sanctum', 'verified']);
Route::get('/chats/{chat}', [ChatController::class, 'singleChat'])->middleware(['auth:sanctum', 'verified']);
Route::post('/chat/{chat}/delete', [ChatController::class, 'deleteChat'])->middleware(['auth:sanctum', 'verified']);
Route::post('/chat/{chat}/report', [ChatController::class, 'blockAndReportUser'])->middleware(['auth:sanctum', 'verified']);
Route::post('/message/{message}/delete', [ChatController::class, 'deleteMessage'])->middleware(['auth:sanctum', 'verified']);
Route::post('/chat/{chat}/message', [ChatController::class, 'sendMessage'])->middleware(['auth:sanctum', 'verified']);
Route::post('/chat/{chat}/message/read', [ChatController::class, 'readUnreadedChatMessage'])->middleware(['auth:sanctum', 'verified']);
Route::post('/chat/{chat}/message/block', [ChatController::class, 'blockChat'])->middleware(['auth:sanctum', 'verified']);
Route::post('/chat/{chat}/message/unblock', [ChatController::class, 'unblockChat'])->middleware(['auth:sanctum', 'verified']);


//apointments
Route::resource('appointments', AppointmentController::class)->middleware(['auth:sanctum', 'verified']);
Route::post('appointment/reject/{appointment}', [AppointmentController::class, 'reject'])->middleware(['auth:sanctum', 'verified']);
Route::post('appointment/accept/{appointment}', [AppointmentController::class, 'accept'])->middleware(['auth:sanctum', 'verified']);
Route::post('appointment/cancel/{appointment}', [AppointmentController::class, 'cancelAppointment'])->middleware(['auth:sanctum', 'verified']);
Route::post('appointment/update/{appointment}', [AppointmentController::class, 'cancelAppointment'])->middleware(['auth:sanctum', 'verified']);
Route::post('appointment/user', [AppointmentController::class, 'getAppoinmentsWithCounsellor'])->middleware(['auth:sanctum', 'verified']);
Route::post('/report', [AbuseReportsController::class, 'report'])->middleware(['auth:sanctum', 'verified']);
Route::post('/feedback', [AbuseReportsController::class, 'feedback'])->middleware(['auth:sanctum', 'verified']);

//
Route::get('/get_comments/', [ArticleController::class, 'getArticleComments'])->middleware(['auth:sanctum', 'verified']);
Route::post('/send_message', [ChatController::class, 'sendMessage'])->middleware(['auth:sanctum', 'verified']);
Route::get('/my_chats', [ChatController::class, 'getChats'])->middleware(['auth:sanctum', 'verified']);
Route::get('/search_users', [ChatController::class, 'searchUsers'])->middleware(['auth:sanctum', 'verified']);
Route::get('/search_counsellors', [ChatController::class, 'searchUsers'])->middleware(['auth:sanctum', 'verified']);
Route::get('/device_tokens', [ChatController::class, 'tokens'])->middleware(['auth:sanctum', 'verified']);
Route::post('/init_chat', [ChatController::class, 'chatInit'])->middleware(['auth:sanctum', 'verified']);

Route::get('/article_categories', [ArticleController::class, 'getArticles'])->middleware(['auth:sanctum', 'verified']);
Route::get('/get-contacts', [EmergencyContactsController::class, 'getContacts'])->middleware(['auth:sanctum']);
Route::post('comment_article', [ArticleController::class, 'commentArticle'])->middleware(['auth:sanctum', 'verified']);
Route::post('reply_comment', [ArticlesVotesController::class, 'articleCommentReply'])->middleware(['auth:sanctum', 'verified']);
Route::post('vote_comment', [ArticlesVotesController::class, 'voteArticleComment'])->middleware(['auth:sanctum', 'verified']);
Route::post('vote_reply', [ArticlesVotesController::class, 'voteArticleCommentReply'])->middleware(['auth:sanctum', 'verified']);
Route::post('alert_peers', [AlertPeersController::class, 'alertPeers'])->middleware(['auth:sanctum', 'verified']);
Route::post('create_update_location', [AlertPeersController::class, 'createNewLocation'])->middleware(['auth:sanctum', 'verified']);

Route::post('change_password', [ChangePasswordController::class, 'changePassword'])->middleware(['auth:sanctum', 'verified']);
Route::post('forgot-password', [ForgotPasswordController::class, 'forgot'])->name('passwords.sent');


Route::get('/devices-tokens', function () {
    return DeviceToken::paginate(5);
});
