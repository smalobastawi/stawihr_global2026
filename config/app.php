<?php

use Carbon\Carbon;
use App\Lib\Enumerations\Gender;
use App\Lib\Enumerations\JobTypes;
use App\Models\Ethnicity;
use App\Lib\Enumerations\UserStatus;
use App\Lib\Enumerations\AnswerTypes;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\LeaveStatus;
use App\Lib\Enumerations\Nationality;
use App\Lib\Enumerations\RatingValues;
use App\Lib\Enumerations\SurveyStatus;
use Illuminate\Support\Facades\Facade;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\FeedbackStatus;
use App\Lib\Enumerations\ResidencyStatus;
use App\Lib\Enumerations\AttendanceStatus;
use App\Lib\Enumerations\StaffContractTypes;
use App\Lib\Enumerations\AttendanceEntryType;

use App\Lib\Enumerations\DegreeClassification;
use App\Lib\Enumerations\DiscipliaryCaseStatus;
use App\Lib\Enumerations\DisciplinaryCaseStatus;
use App\Lib\Enumerations\DisciplinaryActionTypes;
use App\Lib\Enumerations\EarningFrequencies;
use App\Lib\Enumerations\EmployeeBiometricStatus;
use App\Lib\Enumerations\TrainingAttendanceStatus;
use App\Lib\Enumerations\TrainingInvitationStatus;
use App\Lib\Enumerations\EmergencyContactRelationship;
use App\Lib\Enumerations\OvertimeCalculationType;
use App\Lib\Enumerations\PaymentFrequency;
use App\Lib\Enumerations\PayrollStatus;
use App\Lib\Enumerations\TerminationReasons;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool)env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Africa/Nairobi'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'password_login' => env('PASSWORD_LOGIN'),

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    /* |--------------------------------------------------------------------------
    Available locales
    |-------------------------------------------------------------------------- 
    |
    | List all locales that your application works with
    |
    */

    'available_locales' => [
        'English' => 'en',
        'Russian' => 'ru',
        'French' => 'fr',
    ],

    'SMS_Partner_ID' => env('SMS_PARTNER_ID', ''),
    'SMS_API_Key' => env('SMS_API_KEY', ''),
    'SMS_short_Code' => env('SMS_SHORT_CODE', ''),

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        // Spatie\Backup\BackupServiceProvider::class,
        //ConsoleTVs\Charts\ChartsServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'StaffContractTypes' => StaffContractTypes::class,
        'Gender' => Gender::class,
        'AttendanceStatus' => AttendanceStatus::class,
        'GeneralStatus' => GeneralStatus::class,
        'ResidencyStatus' => ResidencyStatus::class,
        'Nationality' => Nationality::class,
        'Ethnicity' => Ethnicity::class,
        'FeedbackStatus' => FeedbackStatus::class,
        'DisciplinaryCaseStatus' => DisciplinaryCaseStatus::class,
        'DisciplinaryActionTypes' => DisciplinaryActionTypes::class,
        'Carbon' => Carbon::class,
        'SurveyStatus' => SurveyStatus::class,
        'AnswerTypes' => AnswerTypes::class,
        'DegreeClassification' => DegreeClassification::class,
        'LeaveStatus' => LeaveStatus::class,
        'RatingValues' => RatingValues::class,
        'EmergencyContactRelationship' => EmergencyContactRelationship::class,
        'UserStatus' => UserStatus::class,
        'AttendanceEntryType' => AttendanceEntryType::class,
        'TrainingAttendanceStatus' => TrainingAttendanceStatus::class,
        'TrainingInvitationStatus' => TrainingInvitationStatus::class,
        'EmployeeBiometricStatus' => EmployeeBiometricStatus::class,
        'JobTypes' => JobTypes::class,
        'TerminationReasons' => TerminationReasons::class,
        'ApprovalStatus' => ApprovalStatus::class,
        'CalculationType' => OvertimeCalculationType::class,
        'PaymentFrequency' => PaymentFrequency::class,
        'EarningFrequencies' => EarningFrequencies::class,
        'PayrollStatus' => PayrollStatus::class,

    ])->toArray(),

    'license_key' => env('LICENSE_KEY', 'null'),
    'morpho_upload_key' => env('MORPHO_UPLOAD_KEY', null),
    'duplicate_clockin_check' => env('DUPLICATE_CLOKCKIN_MINUTES', 5),
    'BIOTIME_API_URL' => env('BIOTIME_API_URL', 'http://localhost:8003/biotime/api'),
    'BIOTIME_API_TOKEN' => env('BIOTIME_API_TOKEN', 'null'),



];
