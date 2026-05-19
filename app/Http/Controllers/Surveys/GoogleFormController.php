<?php

namespace App\Http\Controllers\Surveys;

use App\Events\SurveyNotifyEvent;
use Exception;
use App\Models\Survey;
use Google\Service\Forms;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Google\Service\Forms\Form;
use App\Services\GoogleFormService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\Gender;
use App\Models\Location;
use App\Models\Employee;
use App\Models\Region;
use App\Notifications\NewSurveyNotification;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Lib\Enumerations\GeneralStatus;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\SurveyNotification;
use Maatwebsite\Excel\Concerns\FromCollection;

class GoogleFormController extends Controller
{
    //
    protected $googleFormService;

    public function __construct(GoogleFormService $googleFormService)
    {
        $this->middleware('auth');
        $this->googleFormService = $googleFormService;
    }

    public function index()
    {
        $surveyList = Survey::with(['departments', 'regions'])
            ->latest()
            ->get();
        return view('admin.survey.index', [
            'data' => $surveyList
        ]);
    }

    public function showCreateForm()
    {
        // Check if we have the access token
        if (!Session::has('google_access_token')) {
            return redirect()
                ->route('survey.google.auth')
                ->with('error', 'Please authenticate with Google first.');
        }
        $departments = Department::latest()->get();
        $locations = Location::latest()->get();
        $regions = Region::latest()->get();
        return view('admin.survey.survey.edit', [
            'departments' => $departments,
            'locations' => $locations,
            'regions' => $regions
        ]);
    }

    public function showEditForm($id)
    {
        // Check if we have the access token
        if (!Session::has('google_access_token')) {
            return redirect()
                ->route('survey.google.auth')
                ->with('error', 'Please authenticate with Google first.');
        }

        $editModeData = Survey::findOrFail($id); // No edit mode for create
        $departments = Department::all();
        $locations = Location::all();
        $regions = Region::all();
        $genders = Gender::toArray();


        return view('admin.survey.survey.edit', [
            'departments' => $departments,
            'locations' => $locations,
            'regions' => $regions,
            'genders' => $genders,
            'editModeData' => $editModeData,

        ]);
    }
    /**
     * Redirect the user to Google's OAuth 2.0 server.
     */
    public function redirectToGoogle()
    {
        try {
            // Get the client from the service
            $client = $this->googleFormService->getClient();

            // Define scopes needed. MUST include scopes for actions you want to perform.
            $client->addScope([
                Forms::FORMS_BODY, // To create/edit form structure
            ]);

            // Generate the URL to request authorization
            $authUrl = $client->createAuthUrl();

            // Redirect the user
            return Redirect::to($authUrl);
        } catch (Exception $e) {
            Log::error('Failed to redirect to Google: ' . $e->getMessage());
            // Redirect back with an error message
            return redirect('/')->with('error', 'Could not connect to Google. Please try again later.');
            // Or show an error view: return view('errors.google_connect_failed');
        }
    }
    /**
     * Handle the callback from Google's OAuth 2.0 server.
     */
    public function handleGoogleCallback(Request $request)
    {
        // Check if the user denied access
        if ($request->has('error')) {
            Log::warning('Google Authentication denied by user.', ['error' => $request->input('error')]);
            return redirect()->route('survey.index')->with('error', 'You cancelled the Google authentication.');
        }

        // Check for the authorization code
        $authCode = $request->input('code');

        if (empty($authCode)) {
            Log::error('Google Callback: Missing authorization code.');
            return redirect()->route('survey.index')->with('error', 'Authentication failed (missing code). Please try again.');
        }

        try {
            // Get the client from the service
            $client = $this->googleFormService->getClient();

            // Exchange authorization code for an access token.
            $accessTokenData = $client->fetchAccessTokenWithAuthCode($authCode);

            // Check if fetchAccessTokenWithAuthCode returned an error array
            if (isset($accessTokenData['error'])) {
                Log::error('Google Callback: Error fetching access token.', $accessTokenData);
                return redirect()->route('survey.index')->with('error', 'Failed to get access token from Google: ' . $accessTokenData['error_description'] ?? $accessTokenData['error']);
            }

            // *** ADD THIS LOGGING ***
            if (isset($accessTokenData['scope'])) {
                Log::debug('Google granted scopes: ' . $accessTokenData['scope']);
                // Check if the required scope is in the string
                if (strpos($accessTokenData['scope'], Forms::FORMS_BODY) === false) {
                    Log::error('FATAL: The required scope Forms::FORMS_BODY was NOT granted by Google!');
                }
            } else {
                Log::warning('No scope information received in access token data.');
            }
            Session::put('google_access_token', $accessTokenData);

            // Redirect to the action that will create the form
            return redirect()->route('survey.create'); // Use a named route

        } catch (\Google\Exception $e) { // Catch Google specific exceptions
            Log::error('Google Callback Google Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('survey.index')->with('error', 'An error occurred during Google authentication (Google SDK).');
        } catch (Exception $e) {
            Log::error('Google Callback Generic Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('survey.index')->with('error', 'An unexpected error occurred during Google authentication.');
        }
    }

    /**
     * Create the Google Form using the stored token and redirect back.
     */
    public function createFormAction(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'target_gender' => 'required',
            'departments' => 'required|array',
            'departments.*' => 'exists:department,department_id',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:branch,location_id',
            'regions' => 'nullable|array',
            'regions.*' => 'exists:regions,id',
        ]);

        // Retrieve the token data from the session
        $accessTokenData = Session::get('google_access_token');

        if (!$accessTokenData) {
            Log::warning('Attempted to create form without access token in session.');
            // Token expired or user hasn't authenticated, redirect to start auth flow
            return redirect()
                ->route('survey.google.auth')
                ->with('error', 'Please authenticate with Google first.');
        }

        // Set the access token in the service. This also handles refresh if needed.
        if (!$this->googleFormService->setAccessToken($accessTokenData)) {
            // Could be expired token and no refresh token, or other setup issue
            Log::error('Failed to set or refresh Google access token.');
            // Clear potentially invalid token and restart auth
            Session::forget('google_access_token');
            return redirect()
                ->route('survey.google.auth')
                ->with('error', 'Your Google session may have expired. Please re-authenticate.');
        }

        try {
            $formTitle = $request->input('title');

            $createdForm = $this->googleFormService->createForm($formTitle);
            if ($createdForm) {
                // Form created successfully!
                $formId = $createdForm->getFormId();
                $formUrl = $createdForm->getResponderUri(); // URL for users to fill out the form
                $editFormUrl = "https://docs.google.com/forms/d/{$formId}/edit";
                // Save survey to DB
                $survey = Survey::create([
                    'title' => $formTitle,
                    'slug' => Str::slug($formTitle) . '-' . time(),
                    'google_form_id' => $formId,
                    'form_url' => $formUrl,
                    'edit_url' => $editFormUrl,
                    'target_gender' => $request->input('target_gender'),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id()
                ]);

                // Attach departments to the survey
                if ($request->has('departments')) {
                    $survey->departments()->sync($request->input('departments'));
                }

                $survey->regions()->sync($request->input('regions', []));

                $survey->locations()->sync($request->input('locations', []));

                // Get targeted employees
                $employees = $survey->getTargetedEmployees();

                // Send notifications
                foreach ($employees as $employee) {
                    if ($employee->user) { // Make sure employee has a user account
                        $employee->user->notify(new SurveyNotification($survey));
                    }
                }

                return redirect()
                    ->route('survey.index')
                    ->with('success', 'Survey created successfully!');
            } else {
                // Creation failed (error logged in service)
                Log::error('Google Form creation returned null.');
                // It's possible the token was valid but lacked permissions (scope) or API error occurred.
                return redirect()
                    ->route('survey.index')
                    ->with('error', 'Failed to create the Google Form. Please check application logs.');
            }
        } catch (Exception $e) {
            Log::error('Exception during Google Form creation action: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()
                ->route('survey.index')
                ->with('error', 'An unexpected error occurred while creating the survey.');
        }
    }

    public function getBranchesByRegions(Request $request)
    {
        $regionIds = $request->input('region_ids');
        $locations = Location::whereIn('region_id', $regionIds)->get();

        $selectedBranches = [];

        // If editing a survey, include locations from getAllBranchesAttribute
        if ($request->has('survey_id')) {
            $survey = Survey::find($request->survey_id);
            $selectedBranches = $survey->getAllBranchesAttribute()->pluck('location_id')->toArray();
        }

        return response()->json([
            'locations' => $locations,
            'selected_branches' => $selectedBranches
        ]);
    }

    public function getLocationsByRegions(Request $request)
    {
        $regionIds = $request->input('region_ids');
        $locations = Location::whereIn('region_id', $regionIds)->get();

        $selectedLocations = [];

        // If editing a survey, include branches from getAllBranchesAttribute
        if ($request->has('survey_id')) {
            $survey = Survey::find($request->survey_id);
            $selectedLocations = $survey->getAllBranchesAttribute()->pluck('location_id')->toArray();
        }

        return response()->json([
            'locations' => $locations,
            'selected_locations' => $selectedLocations
        ]);
    }


    public function showUpdateForm(Survey $survey)
    {
        if (!Session::has('google_access_token')) {
            return redirect()
                ->route('survey.google.auth')
                ->with('error', 'Please authenticate with Google first.');
        }

        $departments = Department::latest()->get();
        $locations = Location::latest()->get();
        $regions = Region::latest()->get();

        // Eager load relationships
        $survey->load(['departments', 'locations', 'regions']);

        return view('admin.survey.survey.edit', [
            'departments' => $departments,
            'locations' => $locations,
            'regions' => $regions,
            'data' => $survey
        ]);
    }

    public function updateSurvey(Request $request, $surveyId)
    {
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'target_gender' => 'required',
            'description' => 'nullable|string',
            'departments' => 'required|array',
            'departments.*' => 'exists:department,department_id',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:branch,location_id',
            'regions' => 'nullable|array',
            'regions.*' => 'exists:regions,id',
        ]);

        // Retrieve the survey
        $survey = Survey::findOrFail($surveyId);

        // Check Google authentication
        $accessTokenData = Session::get('google_access_token');
        if (!$accessTokenData) {
            return redirect()
                ->route('survey.google.auth')
                ->with('error', 'Please authenticate with Google first.');
        }

        // Set access token
        if (!$this->googleFormService->setAccessToken($accessTokenData)) {
            Session::forget('google_access_token');
            return redirect()
                ->route('survey.google.auth')
                ->with('error', 'Your Google session may have expired. Please re-authenticate.');
        }

        try {
            $formTitle = $request->input('title');

            // Update Google Form title if it has changed
            if ($survey->title !== $formTitle) {
                $updateResponse = $this->googleFormService->updateFormTitle($survey->google_form_id, $formTitle);

                if (!$updateResponse) {
                    throw new \Exception('Failed to update Google Form title');
                }
            }

            // Update survey in database
            $survey->update([
                'title' => $formTitle,
                'slug' => Str::slug($formTitle) . '-' . time(),
                'target_gender' => $request->input('target_gender'),
                'updated_by' => Auth::id()
            ]);

            // Sync relationships
            $survey->departments()->sync($request->input('departments'));
            $survey->regions()->sync($request->input('regions', []));
            $survey->locations()->sync($request->input('locations', []));

            return redirect()
                ->route('survey.index')
                ->with('success', 'Survey updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating survey: ' . $e->getMessage(), [
                'survey_id' => $surveyId,
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'An error occurred while updating the survey: ' . $e->getMessage());
        }
    }

    public function showFormToUser($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);

        // Make sure the form_url exists
        if (!$survey->form_url) {
            return redirect()->back()->with('error', 'Form URL not found.');
        }

        // Redirect user to the Google Form responder page
        return redirect()->away($survey->form_url);
    }

    public function showTargetedEmployees(Survey $survey)
    {
        // Get targeted employees using the method from your Survey model
        $employees = $survey->getTargetedEmployees();

        return view('admin.survey.survey.targeted_employees', [
            'survey' => $survey,
            'employees' => $employees
        ]);
    }

    public function exportTargetedEmployees(Survey $survey)
    {
        $employees = $survey->getTargetedEmployees();

        return Excel::download(new class($employees) implements FromCollection {
            private $employees;

            public function __construct($employees)
            {
                $this->employees = $employees;
            }

            public function collection()
            {
                return $this->employees->map(function ($employee) {
                    return [
                        'Employee ID' => $employee->employee_id,
                        'Name' => $employee->full_name,
                        'Department' => $employee->department->department_name ?? 'N/A',
                        'Location' => $employee->location->location_name ?? 'N/A',
                        'Region' => $employee->location->region->name ?? 'N/A',
                        'Gender' => Gender::getName($employee->gender),
                        'Status' => $employee->status == GeneralStatus::ACTIVE ? 'Active' : 'Inactive',
                        'Email' => $employee->email,
                        'Phone' => $employee->phone
                    ];
                });
            }
        }, 'targeted_employees_' . $survey->title . '.xlsx');
    }
}
