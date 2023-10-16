<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController as ApiChatController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CompanyController as ApiCompanyController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DepartmentController as ApiDepartmentController;
use App\Http\Controllers\Api\IntegrationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\TicketChatAnswerController;
use App\Http\Middleware\checkCacheForApi;
use App\Http\Middleware\CompanyApiAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('throttle:150,1')->group(function () {

    Route::prefix('integration')->group(function () {
        Route::get('{app}/company', [IntegrationController::class, 'company']);
        Route::get('{app}/company/{company}/agents',   [IntegrationController::class, 'companyAgents']);
        Route::get('{company}/access-information', [IntegrationController::class, 'accessInformation']);
    });

    Route::get('/get-departments-by-timezone', [DepartmentController::class, 'getDepartmentsByTimezone']);


    Route::prefix('helpdesk')->group(function () {
        Route::get('/content/inactivate/{uuid}', [ContentController::class, 'inactivate'])->name('helpdesk.api.inactivate');
        Route::get('/content/activate/{uuid}',   [ContentController::class, 'activate'])->name('helpdesk.api.activate');
    });

    // login - POPUP (Office)
    Route::get('login-office', [AuthController::class, 'loginPopupOffice']);

    // login -  JWT
    Route::post('login-client', [AuthController::class, 'loginClient']);

    // register
    Route::post('register-client', [AuthController::class, 'registerClient']);

    // forgot password
    Route::post('forgot-password', [HomeController::class, 'forgotPasswordClient']);

    // Termos de uso
    Route::post('privacy-policy', [UserAuthController::class, 'privacyPolicy']);

    // auth routes - JWT
    Route::middleware(checkCacheForApi::class)->group(function () {
        Route::middleware('auth:api')->group(function () {

            Route::prefix('auth')->group(function () {

                // api/auth/me - retorna dados do usuário logado
                Route::get('me', [AuthController::class, 'me']);

                // api/auth/refresh - atualiza o token do usuário por mais 6 horas
                Route::get('refresh', [AuthController::class, 'refresh']);

                // api/auth/logout - efetua o logout
                Route::get('logout', [AuthController::class, 'logout']);

                // api/auth/invalidate - efetua o logout e inválida o token
                Route::get('invalidate', [AuthController::class, 'invalidate']);

                // api/auth/get-cache - retorna dados do usuário logado
                Route::get('get-cache', [AuthController::class, 'getCacheForApi']);
            });

            Route::prefix('departments')->group(function () {

                // api/departments - pega os departamentos
                Route::get('', [ApiDepartmentController::class, 'getOpenDepartments']);

                // api/departments/get-quests/{id} - retorna as perguntas do departamento
                Route::get('get-quests/{id}', [ApiDepartmentController::class, 'getQuestions']);

                // api/departments/count-online-agents-by-department
                Route::get('/count-online-agents-by-department', [MonitoringController::class, 'countOnlineAgentsByDeparment']);

                // api/departments/get-department-settings
                Route::get('/get-department-settings', [DepartmentController::class, 'getDepartmentSettings']);
            });

            Route::prefix('chat')->group(function () {

                // api/chat/store
                Route::post('store', [ApiChatController::class, 'store']);

                // api/chat/get-queue-position/{chat_id}/{company_department_id}
                Route::get('get-queue-position/{chat_id}/{company_department_id}', [ChatController::class, 'getQueuePosition']);

                // api/chat/check-evaluation
                Route::get('check-evaluation', [ChatController::class, 'checkEvaluation']);

                //api/chat/chat-evaluation
                Route::post('chat-evaluation', [ChatController::class, 'chatEvaluation']);

                // api/chat/client/get-answers
                Route::get('client/get-answers', [TicketChatAnswerController::class, 'getTicketChatAnswers']);

                // api/chat/history/client/store
                Route::post('history/client/store',  [ApiChatController::class, 'chatHistoryStore']);

                // api/chat/history/client/get-chat-history
                Route::get('/history/client/get-chat-history', [ApiChatController::class, 'getChatHistory']);

                // api/chat/client/upload
                Route::post('client/upload', [ApiChatController::class, 'upload']);

                // api/chat/client/update-status
                Route::post('client/update-status', [ApiChatController::class, 'updateStatus']);

                //api/chat/get-last-chat
                Route::get('get-last-chat', [ApiChatController::class, 'getLastChat']);

                //api/chat/files
                Route::get('files/{chat_id}/{filename}', [ApiChatController::class, 'files']);
            });

            // api/company - validate popup and get comapny data
            Route::get('company', [ApiCompanyController::class, 'index']);
        });
    });

    // api/company - validate popup and get comapny data
    Route::get('company', [ApiCompanyController::class, 'index']);
});

Route::middleware('throttle:500,1')->group(function () {
    Route::middleware(CompanyApiAccess::class)->group(function () {
        Route::prefix('client')->group(function () {
            Route::get('{company}/account-exists/{email}', [ClientController::class, 'accountExists']);
            Route::post('{company}/account-create', [ClientController::class, 'accountCreate']);
            Route::put('{company}/account-update', [ClientController::class, 'accountUpdate']);
        });
    });
});
