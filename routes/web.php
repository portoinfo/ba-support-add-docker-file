<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\CompanyDomainsController;
use App\Http\Controllers\HelpDesk\BuilderallController;
use App\Http\Middleware\CheckSessionForAgents;
use App\Http\Middleware\CheckSessionForClient;
use App\Http\Middleware\CheckUserSession;
use App\Http\Middleware\CompanyApiAccess;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// ROTA PARA TESTAR O TELEGRAM
// Route::get('notifica', 'HomeController@telegram')->name('notifica');
Route::get('teste', 'ChartController@teste');

Route::middleware(['auth'])->group(function () {

    //ADMIN TELAS
    Route::get('/company', 'CompanyController@index')->name('company')->middleware('selectCompany')->middleware('RoutesAdmin');
    Route::post('company/create-company', 'CompanyController@createCompany');
    Route::post('company/contact/', 'CompanyController@storeContact');
    Route::get('company/contact/{id}', 'CompanyController@showContact');
    Route::post('company/update', 'CompanyController@updateCompany');
    Route::post('company/CompanyDelete', 'CompanyController@deleteCompany');
    Route::post('company/contact-delet/', 'CompanyController@deletContact');
    Route::get('company/selected-company/{id}', 'CompanyController@selectedCompany');
    Route::post('/company/get-summary-cards-company-dashboard', 'CompanyController@getSummaryCardsCompanyDashboard');
    Route::get('/company/get-info-dashboard', 'CompanyController@getInfoDashboard');
    Route::get('/company/get-info-bugs-dashboard', 'CompanyController@getInfoBugDashboard');
    Route::get('/company/get-info-topthree', 'CompanyController@getInfoTopThree');
    Route::get('/company/get-info-overdue-ct', 'CompanyController@getInfoOverdueCT');
    Route::post('/company/get-bar-chart-company-dashboard', 'CompanyController@getBarChartCompanyDashboard');
    Route::post('/company/get-departments-company-dashboard', 'CompanyController@getDepartmentsCompanyDashboard');
    Route::post('/company/get-ticket-time-cards-company-dashboard', 'CompanyController@getTicketTimeCardsCompanyDashboard');
    Route::post('/company/get-chat-time-cards-company-dashboard', 'CompanyController@getChatTimeCardsCompanyDashboard');
    Route::get('/company/get-count-member', 'CompanyController@getCountMembers');
    Route::get('/company-integration', 'CompanyController@showintegration')->name('company-integration')->middleware('RoutesAdmin');
    Route::get('/get-intagrations', 'CompanyController@getintegration');

	Route::post('company-config/domain-released', 'CompanyController@domainreleased');
	Route::get('company-config/domains-settings/{cs}', 'CompanyController@domainssettings');
	Route::post('company-config/domain-released/delete', 'CompanyController@domainreleasedDelete');
	Route::post('company-config/domain-blocked', 'CompanyController@domainblocked');
	Route::post('company-config/domain-blocked/delete', 'CompanyController@domainblockedDelete');
	Route::post('company-config/chat-ticket', 'CompanyController@ChatTicket');
	Route::post('company-config/create-category', 'CompanyController@storeCategory');
	Route::post('company-config/update-category', 'CompanyController@updateCategory');
	Route::post('company-config/update-moved-category', 'CompanyController@updateMovedCategory');
	Route::post('company-config/delete-category', 'CompanyController@deleteCategory');
	Route::get('company-config/get-category', 'CompanyController@getCategory');
	Route::get('company-config/get-category-id', 'CompanyController@getCategoryId');
	Route::post('company-config/create-faq-robot', 'CompanyController@createFaqRobotAll');
	Route::get('company-config/get-faq-robot', 'CompanyController@getFaqRobotAll');
	Route::post('company-config/set-faq-robot', 'CompanyController@setFaqRobotAll');
	Route::post('company-config/update-faq-robot', 'CompanyController@updateFaqRobotAll');
	Route::post('company-config/active-faq-robot', 'CompanyController@activeFaqRobotAll');
	Route::post('company-config/delete-faq-robot', 'CompanyController@deleteFaqRobotAll');
	Route::post('company-config/update-status-tool', 'CompanyController@updateStatusTool');
	Route::post('company-config/set-question-robot', 'CompanyController@setQuestionRobot');
	Route::post('company-config/update-robot', 'CompanyController@updateRobot');


	Route::any('company-config/any-custom-email', 'CompanyController@anyCustomEmail');
	Route::get('company-config/get-departments', 'CompanyController@getDepartments');
	Route::get('company-config/get-agents', 'CompanyController@getAgents');
	

    Route::get('company-config/general-settings', 'CompanyController@generalSettings');
    Route::get('get-config-company', 'CompanyController@getConfigCompany');
    Route::get('user/get-user-uuid', 'UserAuthController@getUserUuid');
    Route::get('agents/user/get-user-uuid', 'UserAuthController@getUserUuid');

    Route::post('set-user-dark-mode', 'UserAuthController@setUserDarkMode');
    Route::post('set-user-font-size', 'UserAuthController@setUserFontSize');


    Route::group(['middleware' => 'RoutesAdmin'], function () {
        Route::group(['middleware' => 'selectCompany'], function () {
            Route::any('department', 'DepartmentController@index')->name('department');
            Route::get('department/get-department', 'DepartmentController@showDepartment');
            Route::get('department/get-setting', 'DepartmentController@showSettings');
            Route::post('department/set-setting', 'DepartmentController@storeSettings');
            Route::post('department/department-delete', 'DepartmentController@deleteDepartment');
            Route::post('department/update', 'DepartmentController@updateDepartment');
            Route::post('department/add-user-department', 'DepartmentController@addUserDepartment');
            Route::post('department/remove-user-department', 'DepartmentController@removeUserDepartment');
            Route::get('department/get-agents/{department}', 'DepartmentController@getAgentsDepartment');
            Route::get('department/get-subsidiary', 'DepartmentController@getSubsidiary')->middleware('checkIsClient');
            Route::post('department/post-schedule', 'DepartmentController@postSchedule');
            Route::get('department/get-schedule', 'DepartmentController@getSchedule');
            Route::get('department/register-new-department', 'DepartmentController@registerNewDepartment')->name('registerNewDepartment');
            Route::get('department/list-departments', 'DepartmentController@listDepartments')->name('listDepartments');
            Route::get('department/get-robot/{department}', 'DepartmentController@showRobot');
            Route::get('department/get-my-departments', 'DepartmentController@getMyDepartments');
            Route::post('department/set-robot', 'DepartmentController@storeRobot');
            Route::post('department/delete-robot', 'DepartmentController@deleteRobot');
            Route::post('department/set-setting-questions', 'DepartmentController@setQuestions');
            Route::post('department/delete-setting-questions', 'DepartmentController@deleteQuestions');
            Route::post('department/update-setting-questions', 'DepartmentController@updateQuestions');
            Route::post('department/isValidEmail', 'DepartmentController@isValidEmail');
            Route::get('department/is-robot', 'DepartmentController@isRobot');
            Route::get('department/is-online-agent', 'DepartmentController@isOnlineAgent');
            Route::get('department/check-robot-chat-suggestion', 'DepartmentController@checkSuggestionCreateChat');


            Route::post('/department/get-departments-department-dashboard', 'DepartmentController@getDepartmentsDepartmentDashboard');
            Route::post('/department/get-totals-and-percentages-department-dashboard', 'DepartmentController@getTotalsAndPercentagesDepartmentDashboard');
            Route::post('/department/get-general-avg-for-attendances-department-dashboard', 'DepartmentController@getGeneralAvgForAttendancesDepartmentDashboard');
            Route::post('/department/get-bar-chart-department-dashboard', 'DepartmentController@getBarChartDepartmentDashboard');
            Route::post('/department/get-line-chart-department-dashboard', 'DepartmentController@getLineChartDepartmentDashboard');
            Route::post('/department/get-attendants-department-dashboard', 'DepartmentController@getAttendantsDepartmentDashboard');
            Route::post('/department/get-ticket-time-cards-department-dashboard', 'DepartmentController@getTicketTimeCardsDepartmentDashboard');
            Route::post('/department/get-chat-time-cards-department-dashboard', 'DepartmentController@getChatTimeCardsDepartmentDashboard');

            Route::any('group', 'GroupController@index')->name('group');
            Route::get('group/get-group', 'GroupController@showGroup');
            Route::get('group/get-group-by-id/{id}', 'GroupController@showGroupById');
            Route::post('group/permission', 'GroupController@saveGroupPermission');
            Route::post('group-user', 'GroupController@storeGroupUser');
            Route::post('delet-groupUser', 'GroupController@destroyGroupUser');
            Route::get('group/get-groupUsers/{id}/{company_id}', 'GroupController@showGroupUserAgents');
            Route::post('group/group-delete', 'GroupController@destroyGroup');
            Route::post('group/update', 'GroupController@updateGroup');
            Route::get('group/get-agents/{group}', 'GroupController@showGroupUser');
            Route::post('group/add-user-group', 'GroupController@addUserGroup');
            Route::post('group/remove-user-group', 'GroupController@removeUserGroup');
            Route::post('group/save-permission-group', 'GroupController@savePermissionGroup');
            Route::post('group/get-permission', 'GroupController@getPermissionGroup');

            Route::any('agents', 'AgentsController@index')->name('agents');
            Route::get('agents/agent-info-dashboard/{id}', 'AgentsController@showAgentInfoDashboard');
            Route::get('agents/get-agents', 'AgentsController@showAgents');
            Route::post('agents/agents-delete', 'AgentsController@deleteAgents');
            Route::post('agents/update', 'AgentsController@updateAgents');
            Route::get('agents/get-group-agents', 'AgentsController@getGroupAgents');
            Route::get('agents/get-department-agents/{id}', 'AgentsController@getDepartmentAgents');
            Route::get('agents/register-new-agent', 'AgentsController@registerNewAgent')->name('registerNewAgent');
            Route::get('agents/list-agents', 'AgentsController@listAgents')->name('listAgents');
            Route::post('agents/get-departments-agents-dashboard', 'AgentsController@getDepartmentsAgentsDashboard');
            Route::post('agents/get-attendants-agents-dashboard', 'AgentsController@getAttendantsAgentsDashboard');
            Route::post('agents/get-totals-and-percentages-agents-dashboard', 'AgentsController@getTotalsAndPercentagesAgentsDashboard');
            Route::post('agents/get-general-avg-for-attendances-agents-dashboard', 'AgentsController@getGeneralAvgForAttendancesAgentsDashboard');
            Route::post('agents/get-bar-chart-agents-dashboard', 'AgentsController@getBarChartAgentsDashboard');
            Route::post('agents/get-line-chart-agents-dashboard', 'AgentsController@getLineChartAgentsDashboard');
            Route::post('agents/get-ticket-time-cards-agents-dashboard', 'AgentsController@getTicketTimeCardsAgentsDashboard');
            Route::post('agents/get-chat-time-cards-agents-dashboard', 'AgentsController@getChatTimeCardsAgentsDashboard');
            Route::post('agents/get-departments-agent-info-dashboard', 'AgentsController@getDepartmentsAgentInfoDashboard');
            Route::post('agents/get-quantitative-and-time-cards-agent-info-dashboard', 'AgentsController@getQuantitativeAndTimeCardsAgentInfoDashboard');
            Route::post('agents/get-qualitative-cards-agent-info-dashboard', 'AgentsController@getQualitativeCardsAgentInfoDashboard');

            Route::get('company-user-company-department/get-department-by-agent', 'CompanyUserCompanyDepartmentController@getDepartmentsByAgent');
            Route::post('company-user-company-department/add-agent-to-department', 'CompanyUserCompanyDepartmentController@addAgentToDepartment');
            Route::post('change-status', 'UserAuthController@changeStatus');

            Route::get('/user-client', 'CompanyController@showUsersClient')->name('UsersClient');
            Route::get('/get-clients-company', 'CompanyController@getUsersClient');
            Route::post('/set-client-status', 'CompanyController@setClientStatus');

            Route::get('/analyze', 'AnalyzeController@index')->name('Analyze');
            Route::get('/analyse/get-agents', 'AnalyzeController@getAgents');
            Route::get('/analyse/generate-file-excel', 'AnalyzeController@generateFileExcel');
            Route::get('/analyse/get-departments', 'AnalyzeController@getDepartments');
            Route::get('/analyse/get-avaliations', 'AnalyzeController@getAvaliations');
            Route::get('/analyse/get-infos', 'AnalyzeController@getInfos');
            Route::get('/analyse/get-agents-time', 'AnalyzeController@getAgentsTime');
            Route::get('/analyse/get-graphic', 'AnalyzeController@getGraphic');
            Route::get('/analyse/get-status-user', 'AnalyzeController@getStatusUser');

            Route::get('/monitoring', 'MonitoringController@index')->name('Monitoring');
            Route::get('monitoring/count-tickets-chats-on-queue', 'MonitoringController@countTicketsChatsOnQueue');
            Route::get('monitoring/calc-avg-waiting-time', 'MonitoringController@calcAVGWaitingTime');
            Route::get('monitoring/get-chats-tickets-canceled', 'MonitoringController@getChatsTicketsCancelled');
            Route::get('monitoring/count-agents-by-company', 'MonitoringController@countAgentsByCompany');
            Route::get('monitoring/sum-duration-tickets-chats', 'MonitoringController@sumDurationTicketChats');
            Route::get('monitoring/get-evaluations-today', 'MonitoringController@getEvaluationsToday');
            Route::get('monitoring/get-department-by-chat-id', 'MonitoringController@getDepartmentByChatId');
            Route::get('monitoring/count-online-agents-by-department', 'MonitoringController@countOnlineAgentsByDeparment');
        });

        Route::get('builderall/domains', [BuilderallController::class, 'domains']);
    });

	Route::get('department/get-quests/{id}', 'DepartmentController@getQuestions');
	//rota para trocar a linguagem
	Route::post('agents/update-language', 'AgentsController@updateAgentsLanguage');

	Route::any('/select-company', 'HomeController@selectCompany')->name('selectcompany')->middleware('RoutesAdmin');
	Route::get('/company/get-company', 'CompanyController@showCompany');
	Route::get('/register-new-company', 'CompanyController@registerNewCompany')->name('registerNewCompany');
	Route::get('/edit-company', 'CompanyController@editCompany')->name('editCompany')->middleware('RoutesAdmin');

	Route::get('/about-me', 'UserController@aboutMe');

	//FUNCIONÁRIO TELAS
    Route::get('customer-service', 'CustomerServiceController@index')->middleware('checkIsClient')->name('customer-service');
	Route::get('get-category-graphic', 'CustomerServiceController@getCategoryGraphic')->middleware('checkIsClient');
	Route::get('get-category-info', 'CustomerServiceController@getCategoryInfo')->middleware('checkIsClient');

	Route::get('/chat', 'ChatController@index')->middleware('checkIsClient')->middleware('selectCompany')->name('chat');
	/** begin of full chats routes */
	Route::get('/full-chat', 'FullChatController@index')->middleware('checkIsClient')->middleware('selectCompany')->name('full-chat');
	/** end of full chats routes*/

	Route::get('ticket', 'TicketController@index')->middleware('checkIsClient')->middleware('selectCompany')->middleware('CheckTicketAccess')->name('ticket');

	Route::get('full-ticket', 'FullTicketController@index')->middleware('checkIsClient')->middleware('selectCompany')->middleware('CheckFullTicketAccess')->name('full-ticket');
	Route::get('tickets/get-tickets-admin', 'FullTicketController@getTicketsAdmin')->middleware('checkIsClient');
	Route::post('ticket-add-chat-admin', 'FullTicketController@addChatTicketAdmin')->middleware('checkIsClient');
	Route::get('tickets/get-my-tickets', 'FullTicketController@getMyTickets')->middleware('checkIsClient');
	Route::get('tickets/get-database', 'FullTicketController@getDatabase')->middleware('checkIsClient');
	Route::post('tickets/set-favorite', 'FullTicketController@setFavorite')->middleware('checkIsClient');
	Route::get('tickets/get-chat-history', 'FullTicketController@getChatHistory')->middleware('checkIsClient');

	Route::get('tickets/get-ticket', 'FullTicketController@getTicket')->middleware('checkIsClient');

    // novas telas tickets
    Route::get('tickets/get-counter', 'FullTicketController2@counter')->middleware('checkIsClient');

    Route::get('tickets/get-tickets-on-queue', 'FullTicketController2@getTicketsOnQueue')->middleware('checkIsClient');
    Route::get('tickets/get-tickets-in-progress', 'FullTicketController2@getTicketsInProgress')->middleware('checkIsClient');
    Route::get('tickets/get-tickets-finished', 'FullTicketController2@getTicketsFinished')->middleware('checkIsClient');
    Route::get('tickets/get-tickets-canceled', 'FullTicketController2@getTicketsCanceled')->middleware('checkIsClient');

    Route::get('tickets/counter-per-client', 'FullTicketController2@getCounterPerClient')->middleware('checkIsClient');

	Route::post('create-ticket', 'TicketController@createTicket')->middleware('checkIsClient');
	Route::post('ticket-update-date', 'TicketController@ticketUpdateDate')->middleware('checkIsClient');
	Route::post('/check-client-exists', 'TicketController@checkClientTicket');
	Route::post('update-ticket', 'TicketController@updateTicket')->middleware('checkIsClient');
	Route::post('update-ticket-group', 'TicketController@updateTicketGroup')->middleware('checkIsClient');
	Route::post('ticket-delete', 'TicketController@deleteTicket')->middleware('checkIsClient');
	Route::post('ticket-merge', 'TicketController@mergeTicket')->middleware('checkIsClient');
	Route::get('client/get-client-history-merged', 'TicketController@getClientHistoryMerge')->middleware('checkIsClient');
	Route::post('return-ticket', 'TicketController@returnTicket')->middleware('checkIsClient');
	Route::get('tickets/get-department', 'TicketController@getDepartmentUser')->middleware('checkIsClient');
	Route::get('tickets/get-agents', 'TicketController@getAgents')->middleware('checkIsClient');
	Route::get('tickets/get-tickets', 'TicketController@getTickets')->middleware('checkIsClient');
	Route::get('tickets/get-tickets-creator/{id}', 'TicketController@getTicketsCreator')->middleware('checkIsClient');
	Route::get('tickets/get-tickets-chat', 'TicketController@getTicketsChat')->middleware('checkIsClient');
	Route::post('ticket-result-chat', 'TicketController@saveTicketChat')->middleware('checkIsClient');
	Route::get('tickets/get-files-ticket/{id}', 'TicketController@getFilesTicket')->middleware('checkIsClient');
	Route::post('ticket-add-chat', 'TicketController@addChatTicket')->middleware('checkIsClient');
	Route::post('ticket-update-comment', 'TicketController@updateComment')->middleware('checkIsClient');
	Route::post('update-user-ticket', 'TicketController@updateUserTicket')->middleware('checkIsClient');
	Route::post('search-ticket', 'TicketController@searchTicket')->middleware('checkIsClient');
	Route::post('block-client-ticket', 'TicketController@setBlockClientTicket')->middleware('checkIsClient');
	Route::get('check-block-ticket', 'TicketController@checkBlockTicket')->middleware('checkIsClient');
    Route::get('get-ticket-comments', 'TicketController@getComments')->middleware('checkIsClient');
    Route::post('set-ticket-comments', 'TicketController@setComments')->middleware('checkIsClient');
    Route::post('set-category', 'TicketController@setCategory')->middleware('checkIsClient');
    Route::post('remove-category', 'TicketController@removeCategory')->middleware('checkIsClient');
    Route::get('check-category', 'TicketController@checkCategory')->middleware('checkIsClient');

	Route::get('/users', 'UserController@index')->middleware('checkIsClient');
	Route::post('user/update', 'UserAuthController@update');
	Route::get('/get/config-telegram', 'UserAuthController@getConfigTelegram');
	Route::post('user/accept-cookies', 'UserAuthController@acceptCookies');
	Route::get('/database', 'DatabaseController@index')->middleware('checkIsClient');
	Route::get('/chart', 'ChartController@index')->middleware('checkIsClient');

	Route::middleware(['Blocksystem'])->group(function () {
		//CLIENTES TELAS
		Route::any('/customer-home', 'ClientController@index')->name('customer-home')->middleware('RoutesAdmin')->middleware('LogoutClient');

		Route::any('customer-chat/{any?}', 'ClientController@index')->name('customer-chat')->middleware('RoutesAdmin')->middleware('LogoutClient');
		Route::any('customer-ticket/{any?}', 'ClientController@index')->name('customer-ticket')->middleware('RoutesAdmin')->middleware('LogoutClient');
		
		Route::any('/client-new', 'ClientController@createFastTicket')->name('client-fast-ticket');
		Route::any('/create-fast-ticket/{key}', 'ClientController@createFastTicket')->name('fast-ticket');
		
        // apagar
		Route::any('/client', 'ClientController@indexOld')->name('home-client')->middleware('RoutesAdmin')->middleware('LogoutClient');
		Route::any('/client-chat', 'ClientController@chat')->middleware('RoutesAdmin')->middleware('LogoutClient');
		Route::any('/client-ticket', 'ClientController@ticket')->middleware('RoutesAdmin')->middleware('LogoutClient');
        // apagar
	});

	Route::get('/client-ticket-department', 'ClientController@getDepartmentClient');
	Route::get('/client-get-ticket', 'ClientController@getTicketClient');
	Route::get('/client-chat-ticket/{id}', 'ClientController@getTicketClientChat');
	Route::get('get-info-category-selected/{id}', 'ClientController@getInfoCategorySelected');
	Route::post('/client-create-chat-history', 'ClientController@createTicketChatHistory');
	Route::post('/client-avaliation-ticket', 'ClientController@clientAvaliationTicket');
	Route::post('/client-status-ticket', 'ClientController@clientStatusTicket');
	Route::get('/client-get-quests-department/{id}', 'ClientController@getQuestionsClient');
	Route::get('/setting-msg-ticket', 'ClientController@settingMessageTicket');
	Route::post('/send-robots-first-message', 'ChatController@sendRobotsFirstMessage');

	Route::get('user-auth/get-agents-by-department', 'UserAuthController@getAgentsByDepartment');
	Route::get('user-auth/get-all-agents-by-department', 'UserAuthController@getAllAgentsByDepartment');


	Route::get('get-user-auth-language-by-id', 'UserAuthController@getLanguage');


	// CHAT CLIENTE
	Route::middleware(CheckSessionForClient::class)->group(function(){
		/** @GET */
		/** chat */
		Route::get('chat/get-client-chats', 'ChatController@getClientChats');
		Route::get('chat/get-client-active-chats', 'ChatController@getClientActiveChats');
		Route::get('chat/get-active-chats-from-department', 'ChatController@getActiveChatsFromDepartment');
		Route::get('chat/get-info-chat-checkout-opened', 'ChatController@getInfoChatCheckoutOpened');
		Route::get('chat/get-name-robot', 'ClientController@getNameRobot');
		Route::get('chat/get-active-chats-from-company', 'ClientController@getActiveChatsFromCompany');
		Route::get('chat/get-queue-position/{chat_id}/{company_department_id}', 'ChatController@getQueuePosition');
		Route::get('chat/check-evaluation', 'ChatController@checkEvaluation');
		Route::get('client/get-chats', 'ClientController@getChats');
		Route::get('client/get-chat-info', 'ChatController@getChatInfoClient');
		Route::get('client/get-chat-history', 'ChatHistoryController@getChatHistoryClient');
        Route::get('client/get-open-departments', 'DepartmentController@getOpenDepartments');
        Route::post('client/set-faq-user', 'TicketChatAnswerController@setFaqUser');
        Route::post('client/set-user-click-count', 'TicketChatAnswerController@setUserClickCount');
        /** ticket */
        Route::get('client/get-tickets', 'ClientController@getTickets');
        Route::get('client/get-ticket-info', 'TicketController@getTicketInfoClient');
        Route::get('ticket/check-evaluation', 'TicketController@checkEvaluation');
        Route::get('ticket/get-active-tickets-from-company', 'ClientController@getActiveTicketsFromCompany');

        Route::post('ticket/store', 'ClientController@storeTicket');
        Route::post('ticket-history/client/store', 'ChatHistoryController@clientStoreTicket');

		/** chat-history */
		Route::get('chat-history/client/get-chat-history', 'ChatHistoryController@getChatHistory');
		/** ticket-chat-answer */
		Route::get('ticket-chat-answer/client/get-ticket-chat-answers', 'TicketChatAnswerController@getTicketChatAnswers');
		/** @POST */
		/** chat */
		Route::post('chat/store', 'ChatController@store');
		Route::post('chat/storeRobot', 'ChatController@storeRobot');
		Route::get('chat/getRobot', 'ChatController@getRobot');
		Route::post('chat/evaluation', 'ChatController@chatEvaluation');
		Route::post('chat/client/upload', 'ChatController@upload');
		Route::post('chat/client/update-status', 'ChatController@updateStatus');
		Route::post('chat/client/change-to-ticket', 'ChatController@changeRobotToTicket');
		Route::post('chat/client/transfer', 'ChatController@clientTransfer');
        Route::post('chat/client/cancel', 'ChatController@cancelChat');
        Route::post('chat/client/resolve', 'ChatController@resolveChat');
		/** chat-history */
		Route::post('chat-history/client/store', 'ChatHistoryController@store');
		Route::post('chat-history/client/update', 'ChatHistoryController@update');
		Route::post('chat-history-ticket/client/store', 'ChatHistoryController@storeTicketHistory');
		Route::get('client/department/get-subsidiary', 'DepartmentController@getSubsidiary');

		Route::post('chat-history/robot/store', 'ChatHistoryController@storeRobot');
        Route::get('client/count-online-agents-by-department', 'MonitoringController@countOnlineAgentsByDeparment');

        Route::get('client/get-chat-ticket-data', 'ClientController@getChatsData');
        Route::get('client/get-config-company', 'ClientController@getConfigCompany');
        Route::get('client/get-last-2-tickets', 'ClientController@getLast2Tickets');
        Route::get('client/get-last-2-chats', 'ClientController@getLast2Chats');

        Route::get('client/get-chat-in-progress', 'ClientController@getChatInProgress');

        Route::get('/get-departments-by-client-chats', 'DepartmentController@getDepartmentsByClientChats');
        Route::get('/get-departments-by-client-tickets', 'DepartmentController@getDepartmentsByClientTickets');
	});

	// CHAT AGENTE
	Route::middleware(CheckSessionForAgents::class)->group(function(){
		/** @GET */
		/** chat */
		Route::get('chat/datatable', 'ChatController@datatable');
		Route::get('chat/get-chats-on-queue', 'ChatController@getChatsOnQueue');
		Route::get('chat/get-chats-in-progress', 'ChatController@getChatsInProgress');
		Route::get('chat/get-chats-transferred', 'ChatController@getChatsTransferred');
		Route::get('chat/get-chats-closed', 'ChatController@getChatsClosed');
		Route::get('chat/get-chats-resolved', 'ChatController@getChatsResolved');
		Route::get('chat/get-chats-finished', 'ChatController@getChatsFinished');
		Route::get('chat/get-chats-canceled', 'ChatController@getChatsCanceled');
		Route::get('chat/get-agent-active-chats', 'ChatController@getAgentActiveChats');
		Route::get('chat/get-agent-tables-count', 'ChatController@getAgentTablesCount');


		/** client */
		Route::get('client/get-client-history', 'ClientController@getClientHistory');
		/** chat-history */
		Route::get('chat-history/agent/get-chat-history', 'ChatHistoryController@getChatHistory');
		/** ticket-chat-answer */
		Route::get('ticket-chat-answer/agent/get-ticket-chat-answers', 'TicketChatAnswerController@getTicketChatAnswers');
		/** tabs */
		Route::post('tabs', 'ChatController@tabs');
		/** @POST */
		/** chat */
		Route::post('chat/set-employee', 'ChatController@setEmployee');
		Route::post('chat/verify-active-footer-chats', 'ChatController@verifyActiveFooterChats');
		Route::post('chat/agent/upload', 'ChatController@upload');
		Route::post('chat/agent/update-status', 'ChatController@updateStatus');
		Route::post('chat/agent/check-department', 'ChatController@checkDepartment');
		Route::post('chat/take-the-chat', 'ChatController@takeTheChat');
		Route::post('chat/delete', 'ChatController@delete');
		Route::post('chat/set-information-to-turn-into-ticket', 'ChatController@setInfoToTurnIntoticket');
		Route::get('chat/get-information-to-turn-into-ticket', 'ChatController@getInfoToTurnIntoTicket');
		Route::post('chat/delete-information-to-turn-into-ticket', 'ChatController@deleteInfoToTurnIntoticket');
		/** chat-history */
		Route::post('chat-history/agent/store', 'ChatHistoryController@store');
		Route::post('chat-history-ticket/agent/store', 'ChatHistoryController@storeTicketHistory');


		Route::post('chat-history/set-translated-messages', 'ChatHistoryController@setTranslatedMessage');
		Route::get('chat-history/check-translation', 'ChatHistoryController@checkTranslation');

		/** ------------------------------------------------------------------------------------------------------------------------------------ */
		/** begin of full chats routes */
		//Route::middleware(CheckFullChatAccess::class)->group(function(){
			/** @GET */
			/** full-chat */
			Route::prefix('full-chat')->group(function () {
                Route::get('/get-chats-on-queue', 'ChatController@getChatsOnQueue');
				Route::get('/get-chats-in-progress', 'FullChatController@getChatsInProgress');
				Route::get('/get-chats-closed', 'FullChatController@getChatsClosed');
				Route::get('/get-chats-resolved', 'FullChatController@getChatsResolved');
				Route::get('/get-chats-finished', 'FullChatController@getChatsFinished');
				Route::get('/get-chats-canceled', 'FullChatController@getChatsCanceled');
				Route::get('/get-agent-tables-count', 'FullChatController@getAgentTablesCount');
			});
			/** full-chat-admin */
			Route::prefix('full-chat-admin')->group(function () {
				Route::get('/get-chats-on-queue', 'FullChatAdminController@getChatsOnQueue');
				Route::get('/get-chats-in-progress', 'FullChatAdminController@getChatsInProgress');
				Route::get('/get-chats-closed', 'FullChatAdminController@getChatsClosed');
				Route::get('/get-chats-resolved', 'FullChatAdminController@getChatsResolved');
				Route::get('/get-chats-finished', 'FullChatAdminController@getChatsFinished');
				Route::get('/get-chats-canceled', 'FullChatAdminController@getChatsCanceled');
				Route::get('/get-agent-tables-count', 'FullChatAdminController@getAgentTablesCount');

				Route::post('/set-employee', 'FullChatAdminController@setEmployee');
			});
		//});
		/** end of full chats routes*/
	});
	Route::get('chat/files/{chat_id}/{filename}', 'ChatController@files');
	Route::get('ticket/files/{chat_id}/{filename}', 'TicketController@files');

	Route::get('/get-country', 'ClientController@getCountry');

	Route::get('/get-departments-by-company', 'DepartmentController@getDepartmentsByCompany');
	Route::get('/get-open-departments', 'DepartmentController@getOpenDepartments');
	Route::get('/get-departments-of-agent', 'DepartmentController@getDepartmentsOfAgent');
	Route::get('/get-department-settings', 'DepartmentController@getDepartmentSettings');

	// home admin
	Route::post('/home/get-summary-cards-home-dashboard', 'HomeController@getSummaryCardsHomeDashboard');
	Route::post('/home/get-progress-cards-home-dashboard', 'HomeController@getProgressCardsHomeDashboard');
	Route::post('/home/get-bar-chart-home-dashboard', 'HomeController@getBarChartHomeDashboard');

	// home atendente
	Route::post('/home/get-summary-cards-agent-home-dashboard', 'HomeController@getSummaryCardsAgentHomeDashboard');
	Route::post('/home/get-qualitative-cards-agent-home-dashboard', 'HomeController@getQualitativeCardsAgentHomeDashboard');
	Route::post('/home/get-bar-chart-agent-home-dashboard', 'HomeController@getBarChartAgentHomeDashboard');
	Route::post('/home/get-line-chart-agent-home-dashboard', 'HomeController@getLineChartAgentHomeDashboard');

	Route::any('/filter', 'FilterController@index')->name('filter');
	Route::get('/get-all-history', 'FilterController@filter');
	Route::get('/get-tickets-history', 'FilterController@filterTickets');
	Route::get('/get-ticket-chat-generate-excel', 'FilterController@getGenerateExcel');
	Route::post('/generate-excel-chat-ticket', 'FilterController@generateExcelChatTicket');

	Route::any('/category', 'FilterController@indexCategory')->name('category');

	Route::any('/config-terms', 'HomeController@AnyConfigTerms');

	// Domínios da companhia
	Route::get('company-domains/{comapny_hash_code}', [CompanyDomainsController::class, 'index']);
	Route::post('company-domains', [CompanyDomainsController::class, 'store']);
	Route::delete('company-domains/{company_domain}', [CompanyDomainsController::class, 'destroy']);

});

Route::get('/new', 'HomeController@indexTeste')->middleware('checkIsClient');
Route::get('/new/{route}', 'HomeController@indexTeste')->middleware('checkIsClient');
Route::get('/home', 'HomeController@indexTeste')->middleware('checkIsClient');
Route::get('/', 'HomeController@index')->name('home-idx')->middleware('checkIsClient');
// Route::get('/home', 'HomeController@index')->name('home')->middleware('checkIsClient');

Route::get('/client-new', 'HomeController@indexTeste2')->name('client-new');

Route::any('/login', 'HomeController@login')->withoutMiddleware([VerifyCsrfToken::class]);
Route::any('/login/telegram', 'HomeController@loginTelegram')->name('loginTelegram')->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/blocked', 'CompanyController@ViewBlock')->name('blocked');

Route::any('/forgot-password', 'HomeController@forgotPassword')->withoutMiddleware([VerifyCsrfToken::class]);
Route::get('/change-password/{hash}', 'HomeController@changePassword')->withoutMiddleware([VerifyCsrfToken::class]);
Route::post('/save-change-password', 'HomeController@saveChangePassword')->withoutMiddleware([VerifyCsrfToken::class]);

// acesso ao sistema pelos clientes
Route::prefix('client')->group(function(){
	Route::any('/{company}/login-new', 'HomeController@loginClient')->name('login-client-new')->withoutMiddleware([VerifyCsrfToken::class]);
	Route::any('/{company}/register-new', 'HomeController@registerClient')->name('register-client-new')->withoutMiddleware([VerifyCsrfToken::class]);
	Route::any('/{company}/login', 'HomeController@loginClient')->name('login-client')->withoutMiddleware([VerifyCsrfToken::class]);
	Route::any('/{company}/register', 'HomeController@registerClient')->name('register-client')->withoutMiddleware([VerifyCsrfToken::class]);
	Route::post('/{company}/forgot-password', 'HomeController@forgotPasswordClient')->withoutMiddleware([VerifyCsrfToken::class]);
	Route::get('/{user}/{company}/{type}', 'HomeController@clientAccessLink')->name('client-access-link');
	Route::middleware(CompanyApiAccess::class)->group(function(){
		Route::any('/{company}/access', [ClientController::class, 'access'])->name('client-access')->withoutMiddleware([VerifyCsrfToken::class]);
		Route::any('/{company}/new-access', [ClientController::class, 'newAccess'])->name('client-new-access')->withoutMiddleware([VerifyCsrfToken::class]);
    });
});

Route::get('/logout', 'HomeController@logout')->name('logout');
Route::get('/logout-client', 'HomeController@logoutClient')->name('logout-client');
Route::get('/status/user', 'HomeController@statusUser');
Route::post('/update-sessions', 'HomeController@updateSessions');

// Route::any('/register', 'HomeController@register');

Route::get('/checkout/{cscode}', 'HomeController@getInfomationCheckout')->withoutMiddleware([Cors::class]);
Route::get('/checkout2/{cscode}', 'HomeController@getInfomationCheckout')->middleware('cors');

Route::get('gravatar', 'UserAuthController@gravatar');
Route::get('get-diff', 'ChartController@getDiff');

Route::prefix('helpdesk')->group(function(){
	Route::any('/login-by-token', [BuilderallController::class, 'loginByToken'])->name('helpdesk.login.by.token');
});

Route::post('user/privacy-policy', 'UserAuthController@privacyPolicy')->withoutMiddleware([VerifyCsrfToken::class]);
Route::get('public/chat/files/{chat_id}/{filename}', 'ChatController@files2');

Route::get('public/faq/files/{comany_id}/{id}/{filename}', 'TicketChatAnswerController@files3');
Route::get('customer-chat/public/faq/files/{comany_id}/{id}/{filename}', 'TicketChatAnswerController@files3');

Route::get('public/document/agentsInfos', 'AnalyzeController@documentAgents');
Route::get('public/document/history', 'FilterController@historyCT');

Route::get('get-faq-robot-tools', 'TicketChatAnswerController@getFaqRobotTools');
Route::get('get-faq-robot-tools-id', 'TicketChatAnswerController@getFaqRobotToolsId');
Route::get('set-info-robot-finish', 'TicketChatAnswerController@setInfoRobotFinish');


