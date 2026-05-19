<?php

namespace App\Services;

use Exception;
use Google\Client;
use Google_Client;
use App\Models\User;
use Google\Service\Forms;
use Google\Service\Forms\Form;
use Google\Service\Forms\Info;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GoogleFormService
{


    protected $client;
    protected $formsService;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_FORMS_REDIRECT_URI')); // <--- CHANGE THIS LINE
        $this->client->setAccessType('offline'); // Crucial for getting a refresh token
        $this->client->setPrompt('select_account consent'); // Optional: forces consent screen & account selection
        // Add all required scopes
        $this->client->addScope([
            'https://www.googleapis.com/auth/forms.body',
            'https://www.googleapis.com/auth/forms.responses.readonly'
        ]);
        // Scopes will be added dynamically before redirecting or when setting token
    }

    /**
     * Get the Google Client instance.
     * Useful for initiating auth flow outside the service.
     *
     * @return Google_Client
     */
    public function getClient(): Google_Client
    {
        return $this->client;
    }

    /**
     * Set the access token for the authenticated user.
     * Handles token refresh if necessary.
     *
     * @param array $accessTokenData // Expects ['access_token' => '...', 'refresh_token' => '...', 'expires_in' => ..., 'scope' => '...', 'created' => ...]
     * @return bool True if client is ready, false otherwise
     */
    public function setAccessToken(array $accessTokenData): bool
    {
        try {
            if (empty($accessTokenData['access_token'])) {
                Log::error('GoogleFormService: Missing access token.');
                return false;
            }

            $this->client->setAccessToken($accessTokenData);

            // Check if the token is expired and refresh if needed
            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = $this->client->getRefreshToken(); // Get refresh token stored in client

                if ($refreshToken) {
                    Log::info('GoogleFormService: Access token expired, attempting refresh.');
                    // The fetchAccessTokenWithRefreshToken method modifies the client directly
                    $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    $newAccessTokenData = $this->client->getAccessToken(); // Get potentially updated token

                    // *** IMPORTANT: Persist this new token! ***
                    // You should update the stored token (Session, Database) here or return it
                    // For now, we rely on the controller to potentially re-save it after the API call
                    Session::put('google_access_token', $newAccessTokenData); // Example: Update session immediately
                    Log::info('GoogleFormService: Token refreshed successfully.');
                } else {
                    Log::error('GoogleFormService: Access token expired, but no refresh token available for client.');
                    // Clear the potentially invalid token from the client? Maybe not necessary if fetchAccessTokenWithRefreshToken failed.
                    // $this->client->revokeToken(); // Maybe too drastic
                    // Session::forget('google_access_token'); // Clear session if refresh failed
                    return false; // Cannot proceed without a valid token
                }
            }

            // IMPORTANT: Add the necessary scope AFTER setting the token if it wasn't included during auth
            // The token itself usually contains the granted scopes.
            // If you need to ensure specific scopes for the service:
            if (!in_array(Forms::FORMS_BODY, $this->client->getScopes())) {
                // This might not work correctly if the token wasn't granted this scope initially.
                // Scopes should ideally be requested during the initial auth redirect.
                //  $this->client->addScope(Forms::FORMS_BODY);
                Log::warning('GoogleFormService: The FORMS_BODY scope might not have been granted in the current token.');
            }


            // Initialize the Forms service *after* setting the token
            $this->formsService = new Forms($this->client);
            return true;
        } catch (\Google\Exception $e) { // Catch Google specific exceptions
            Log::error('GoogleFormService Google Exception setting/refreshing token: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return false;
        } catch (Exception $e) {
            Log::error('GoogleFormService Generic Error setting/refreshing token: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return false;
        }
    }


    public function setAccessTokenFromSocialite($socialiteUser): bool
    {
        try {
            $tokenData = [
                'access_token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'expires_in' => $socialiteUser->expiresIn,
                'created' => time(),
                'scope' => implode(' ', $this->client->getScopes()) // Include scopes
            ];

            $this->client->setAccessToken($tokenData);

            // Initialize Forms service
            $this->formsService = new Forms($this->client);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to set Google access token: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Create a simple Google Form.
     *
     * @param string $title
     * @return \Google\Service\Forms\Form|null
     */
    public function createForm(string $title): ?Form
    {
     

        if (!$this->formsService) {
            Log::error('Forms service not initialized. Current client state:', [
                'has_token' => !empty($this->client->getAccessToken()),
                'is_expired' => $this->client->isAccessTokenExpired(),
                'scopes' => $this->client->getScopes()
            ]);
            return null;
        }

        try {
            $form = new Form();
            $formInfo = new Info();
            $formInfo->setTitle($title);
            $form->setInfo($formInfo);


            // Ensure FORMS_BODY scope is present before calling
            // This check is good practice but depends on accurate scope info in the client/token
            if (!in_array(Forms::FORMS_BODY, $this->client->getScopes())) {
                Log::error('GoogleFormService: Missing required scope (forms.body) to create form.');
                // Maybe throw a specific exception here?
                // throw new MissingScopeException('Requires forms.body scope.');
                return null;
            }

            $createdForm = $this->formsService->forms->create($form);
            Log::info("GoogleFormService: Form created successfully with ID: " . $createdForm->getFormId());

            return $createdForm;
        } catch (\Google\Service\Exception $e) { // Catch Google Service specific exceptions
            Log::error('GoogleFormService Google API Error creating form: ' . $e->getMessage(), [
                'errors' => $e->getErrors(), // Google errors often have more details here
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        } catch (Exception $e) {
            Log::error('GoogleFormService Generic Error creating form: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

   

    public function checkTokenStatus(): array
    {
        return [
            'has_token' => !empty($this->client->getAccessToken()),
            'is_expired' => $this->client->isAccessTokenExpired(),
            'scopes' => $this->client->getScopes(),
            'token' => $this->client->getAccessToken()
        ];
    }

    /**
     * Get a Google Form by its ID.
     *
     * @param string $formId
     * @return \Google\Service\Forms\Form|null
     */
    public function getForm(string $formId): ?Form
    {
        if (!$this->formsService) {
            Log::error('GoogleFormService: Forms service not initialized.');
            return null;
        }
        try {
            return $this->formsService->forms->get($formId);
        } catch (Exception $e) {
            Log::error('GoogleFormService Error getting form: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * List responses for a Google Form.
     *
     * @param string $formId
     * @return \Google\Service\Forms\ListFormResponsesResponse|null
     */
    public function getResponses(string $formId): ?\Google\Service\Forms\ListFormResponsesResponse
    {
        if (!$this->formsService) {
            Log::error('GoogleFormService: Forms service not initialized.');
            return null;
        }
        try {
            // Requires forms.responses.readonly scope
            return $this->formsService->forms_responses->listFormsResponses($formId);
        } catch (Exception $e) {
            Log::error('GoogleFormService Error getting responses: ' . $e->getMessage());
            return null;
        }
    }

    // Add more methods as needed (updateForm, addQuestion, etc.)

    public function refreshTokenIfNeeded(User $user)
    {
        if (now()->gte($user->token_expires_at)) {
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->refreshToken($user->refresh_token);

            $newToken = $client->getAccessToken();

            $user->update([
                'token' => $newToken['access_token'],
                'expires_in' => $newToken['expires_in'],
                'token_expires_at' => now()->addSeconds($newToken['expires_in'] - 300),
            ]);

            return $newToken;
        }

        return null;
    }

    public function updateFormTitle(string $formId, string $newTitle): ?\Google\Service\Forms\BatchUpdateFormResponse
    {
        if (!$this->formsService) {
            Log::error('GoogleFormService: Forms service not initialized.');
            return null;
        }

        try {
            // Create form info with new title
            $formInfo = new \Google\Service\Forms\Info();
            $formInfo->setTitle($newTitle);

            // Create update request
            $updateInfoRequest = new \Google\Service\Forms\UpdateFormInfoRequest();
            $updateInfoRequest->setInfo($formInfo);
            $updateInfoRequest->setUpdateMask('title');

            // Create wrapper request
            $request = new \Google\Service\Forms\Request();
            $request->setUpdateFormInfo($updateInfoRequest);

            // Create batch update
            $batchUpdateRequest = new \Google\Service\Forms\BatchUpdateFormRequest();
            $batchUpdateRequest->setRequests([$request]);

            // Execute the update
            return $this->formsService->forms->batchUpdate($formId, $batchUpdateRequest);
        } catch (\Google\Service\Exception $e) {
            Log::error('Google API Error updating form title', [
                'formId' => $formId,
                'error' => $e->getMessage(),
                'errors' => $e->getErrors(),
                'code' => $e->getCode()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('General error updating form title', [
                'formId' => $formId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return null;
        }
    }
}
