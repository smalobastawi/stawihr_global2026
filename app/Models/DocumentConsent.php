<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'employee_id',
        'user_id',
        'consented_at',
        'ip_address',
        'user_agent',
        'acknowledgment_text',
    ];

    protected $casts = [
        'consented_at' => 'datetime',
    ];

    /**
     * Get the document associated with this consent.
     */
    public function document()
    {
        return $this->belongsTo(HrDocument::class, 'document_id');
    }

    /**
     * Get the employee who gave consent.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Get the user who gave consent.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if a specific employee has consented to a document.
     */
    public static function hasConsented($documentId, $employeeId)
    {
        return self::where('document_id', $documentId)
            ->where('employee_id', $employeeId)
            ->exists();
    }

    /**
     * Get consent record for a specific employee and document.
     */
    public static function getConsent($documentId, $employeeId)
    {
        return self::where('document_id', $documentId)
            ->where('employee_id', $employeeId)
            ->first();
    }

    /**
     * Get all consents for a document.
     */
    public static function getDocumentConsents($documentId)
    {
        return self::with(['employee', 'user'])
            ->where('document_id', $documentId)
            ->orderBy('consented_at', 'desc')
            ->get();
    }

    /**
     * Get consent statistics for a document.
     */
    public static function getDocumentConsentStats($documentId)
    {
        $totalEmployees = Employee::where('status', 1)->count();
        $consentedCount = self::where('document_id', $documentId)->count();
        $pendingCount = $totalEmployees - $consentedCount;

        return [
            'total' => $totalEmployees,
            'consented' => $consentedCount,
            'pending' => $pendingCount > 0 ? $pendingCount : 0,
            'percentage' => $totalEmployees > 0 ? round(($consentedCount / $totalEmployees) * 100, 2) : 0,
        ];
    }
}
