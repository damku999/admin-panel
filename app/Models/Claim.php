<?php

namespace App\Models;

use App\Mail\ClaimNotificationMail;
use App\Traits\TableRecordObserver;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\Claim
 *
 * @property int $id
 * @property string $claim_number
 * @property int $customer_id
 * @property int $customer_insurance_id
 * @property string $insurance_type
 * @property string $incident_date
 * @property string|null $description
 * @property string|null $whatsapp_number
 * @property bool $send_email_notifications
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read Customer $customer
 * @property-read CustomerInsurance $customerInsurance
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ClaimStage> $stages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ClaimDocument> $documents
 * @property-read ClaimLiabilityDetail|null $liabilityDetail
 * @property-read ClaimStage|null $currentStage
 */
class Claim extends Model
{
    use HasApiTokens, HasFactory, HasRoles, SoftDeletes, TableRecordObserver, LogsActivity, WhatsAppApiTrait;

    protected $fillable = [
        'claim_number',
        'customer_id',
        'customer_insurance_id',
        'insurance_type',
        'incident_date',
        'description',
        'whatsapp_number',
        'send_email_notifications',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'send_email_notifications' => 'boolean',
        'incident_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    /**
     * Get the customer that owns the claim.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the customer insurance policy for this claim.
     */
    public function customerInsurance(): BelongsTo
    {
        return $this->belongsTo(CustomerInsurance::class);
    }

    /**
     * Get all stages for the claim.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(ClaimStage::class)->orderBy('created_at');
    }

    /**
     * Get the current active stage.
     */
    public function currentStage(): HasOne
    {
        return $this->hasOne(ClaimStage::class)->where('is_current', true);
    }

    /**
     * Get all documents for the claim.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDocument::class)->orderBy('is_required', 'desc');
    }

    /**
     * Get the liability detail for the claim.
     */
    public function liabilityDetail(): HasOne
    {
        return $this->hasOne(ClaimLiabilityDetail::class);
    }

    /**
     * Generate a unique claim number.
     */
    public static function generateClaimNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        // Get the last claim number for current month
        $lastClaim = self::where('claim_number', 'like', "CLM{$year}{$month}%")
            ->orderBy('claim_number', 'desc')
            ->first();

        if ($lastClaim) {
            $lastNumber = (int)substr($lastClaim->claim_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "CLM{$year}{$month}{$newNumber}";
    }

    /**
     * Create default documents based on insurance type.
     */
    public function createDefaultDocuments(): void
    {
        $documents = [];

        if ($this->insurance_type === 'Health') {
            // Health Insurance Documents - Exact list from requirements (10 items)
            $documents = [
                ['name' => 'Patient Name', 'description' => 'Name of the patient receiving treatment', 'required' => true],
                ['name' => 'Policy No', 'description' => 'Insurance policy number', 'required' => true],
                ['name' => 'Contact no', 'description' => 'Patient contact number', 'required' => true],
                ['name' => 'Date of Admission', 'description' => 'Hospital admission date', 'required' => true],
                ['name' => 'Treating Doctor Name', 'description' => 'Name of treating physician', 'required' => true],
                ['name' => 'Hospital Name', 'description' => 'Name of the hospital', 'required' => true],
                ['name' => 'Address of Hospital', 'description' => 'Complete hospital address', 'required' => true],
                ['name' => 'Illness', 'description' => 'Nature of illness/treatment', 'required' => true],
                ['name' => 'Approx Hospitalisation Days', 'description' => 'Expected duration of stay', 'required' => true],
                ['name' => 'Approx Cost', 'description' => 'Estimated treatment cost', 'required' => true],
            ];
        } elseif ($this->insurance_type === 'Vehicle') {
            // Vehicle/Truck Insurance Documents - Exact list from requirements (20 items)
            $documents = [
                ['name' => 'Claim form duly Signed', 'description' => 'Completed and signed claim form', 'required' => true],
                ['name' => 'Policy Copy', 'description' => 'Insurance policy document copy', 'required' => true],
                ['name' => 'RC Copy', 'description' => 'Registration Certificate copy', 'required' => true],
                ['name' => 'Driving License', 'description' => 'Valid driving license copy', 'required' => true],
                ['name' => 'Driver Contact Number', 'description' => 'Contact details of driver', 'required' => true],
                ['name' => 'Spot Location Address', 'description' => 'Exact accident location', 'required' => true],
                ['name' => 'Fitness Certificate', 'description' => 'Vehicle fitness certificate', 'required' => true],
                ['name' => 'Permit', 'description' => 'Commercial vehicle permit', 'required' => true],
                ['name' => 'Road tax', 'description' => 'Road tax receipt/certificate', 'required' => true],
                ['name' => 'Cancel Cheque', 'description' => 'Cancelled cheque for payment', 'required' => true],
                ['name' => 'Fast tag statement', 'description' => 'FASTag transaction statement', 'required' => false],
                ['name' => 'CKYC Form', 'description' => 'Central KYC form', 'required' => true],
                ['name' => 'Insured Pan and Address Proof', 'description' => 'PAN card and address proof of insured', 'required' => true],
                ['name' => 'Load Challan', 'description' => 'Goods loading receipt/challan', 'required' => false],
                ['name' => 'All side spot Photos with driver selfie', 'description' => 'Complete accident site photographs', 'required' => true],
                ['name' => 'Towing Bill', 'description' => 'Vehicle towing charges receipt', 'required' => false],
                ['name' => 'Workshop Estimate', 'description' => 'Repair cost estimate from workshop', 'required' => true],
                ['name' => 'FIR - Yes or No', 'description' => 'Police FIR status and copy if filed', 'required' => false],
                ['name' => 'Third Party Injury - Yes or No', 'description' => 'Third party injury details if any', 'required' => false],
                ['name' => 'How accident Happened?', 'description' => 'Detailed accident description', 'required' => true],
            ];
        }

        foreach ($documents as $doc) {
            $this->documents()->create([
                'document_name' => $doc['name'],
                'description' => $doc['description'],
                'is_required' => $doc['required'],
                'is_submitted' => false,
            ]);
        }
    }

    /**
     * Create initial claim stage.
     */
    public function createInitialStage(): ClaimStage
    {
        return $this->stages()->create([
            'stage_name' => 'Claim Registered',
            'description' => 'Claim has been registered and assigned claim number',
            'is_current' => true,
            'is_completed' => true,
            'stage_date' => now(),
            'notes' => 'Initial claim registration completed',
        ]);
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Check if claim is active.
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }

    /**
     * Get formatted incident date.
     */
    public function getIncidentDateFormattedAttribute(): string
    {
        return formatDateForUi($this->incident_date);
    }

    /**
     * Set incident date from UI format.
     */
    public function setIncidentDateAttribute($value): void
    {
        $this->attributes['incident_date'] = formatDateForDatabase($value);
    }

    /**
     * Get document completion percentage.
     */
    public function getDocumentCompletionPercentage(): float
    {
        $totalDocs = $this->documents()->count();
        if ($totalDocs === 0) {
            return 0;
        }

        $submittedDocs = $this->documents()->where('is_submitted', true)->count();
        return round(($submittedDocs / $totalDocs) * 100, 2);
    }

    /**
     * Get required document completion percentage.
     */
    public function getRequiredDocumentCompletionPercentage(): float
    {
        $totalRequiredDocs = $this->documents()->where('is_required', true)->count();
        if ($totalRequiredDocs === 0) {
            return 100;
        }

        $submittedRequiredDocs = $this->documents()
            ->where('is_required', true)
            ->where('is_submitted', true)
            ->count();

        return round(($submittedRequiredDocs / $totalRequiredDocs) * 100, 2);
    }

    /**
     * Get the WhatsApp number for this claim (alternative or customer mobile).
     */
    public function getWhatsAppNumber(): string
    {
        return !empty($this->whatsapp_number) ? $this->whatsapp_number : $this->customer->mobile_number;
    }

    /**
     * Get Health Insurance document list message template.
     */
    public function getHealthInsuranceDocumentListMessage(): string
    {
        return "For health Insurance - Kindly provide below mention details for Claim intimation

1. Patient Name
2. Policy No
3. Contact no
4. Date of Admission
5. Treating Doctor Name
6. Hospital Name
7. Address of Hospital
8. Illness
9. Approx Hospitalisation Days
10. Approx Cost

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }

    /**
     * Get Vehicle/Truck Insurance document list message template.
     */
    public function getVehicleInsuranceDocumentListMessage(): string
    {
        return "For Vehicle/Truck Insurance - Kindly provide below mention details for Claim intimation

1. Claim form duly Signed
2. Policy Copy
3. RC Copy
4. Driving License
5. Driver Contact Number
6. Spot Location Address
7. Fitness Certificate
8. Permit
9. Road tax
10. Cancel Cheque
11. Fast tag statement
12. CKYC Form
13. Insured Pan and Address Proof
14. Load Challan
15. All side spot Photos with driver selfie
16. Towing Bill
17. Workshop Estimate
18. FIR - Yes or No
19. Third Party Injury - Yes or No
20. How accident Happened?

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }

    /**
     * Get pending documents reminder message template.
     */
    public function getPendingDocumentsMessage(): string
    {
        $pendingDocuments = $this->documents()->where('is_submitted', false)->get();

        if ($pendingDocuments->isEmpty()) {
            return "All documents have been received for your claim. Thank you for your cooperation.";
        }

        $message = "Below are the Documents pending from your side, Send it urgently for hassle free claim service\n\n";

        $counter = 1;
        foreach ($pendingDocuments as $document) {
            $message .= $counter . ". " . $document->document_name . "\n";
            $counter++;
        }

        $message .= "\nBest regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";

        return $message;
    }

    /**
     * Get claim number notification message template.
     */
    public function getClaimNumberNotificationMessage(): string
    {
        $vehicleNumber = $this->customerInsurance->registration_no ?? 'N/A';

        return "Dear customer your Claim Number {$this->claim_number} is generated against your vehicle number {$vehicleNumber}. For further assistance kindly contact me.

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }

    /**
     * Send initial document list WhatsApp message.
     */
    public function sendDocumentListWhatsApp(): array
    {
        try {
            $message = $this->insurance_type === 'Health'
                ? $this->getHealthInsuranceDocumentListMessage()
                : $this->getVehicleInsuranceDocumentListMessage();

            $response = $this->whatsAppSendMessage($message, $this->getWhatsAppNumber());

            return [
                'success' => true,
                'message' => 'Document list sent via WhatsApp successfully',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Send pending documents reminder WhatsApp message.
     */
    public function sendPendingDocumentsWhatsApp(): array
    {
        try {
            $message = $this->getPendingDocumentsMessage();
            $response = $this->whatsAppSendMessage($message, $this->getWhatsAppNumber());

            return [
                'success' => true,
                'message' => 'Pending documents reminder sent via WhatsApp successfully',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Send claim number notification WhatsApp message.
     */
    public function sendClaimNumberWhatsApp(): array
    {
        try {
            if (empty($this->claim_number)) {
                return [
                    'success' => false,
                    'message' => 'Cannot send claim number notification - claim number not set',
                    'response' => null
                ];
            }

            $message = $this->getClaimNumberNotificationMessage();
            $response = $this->whatsAppSendMessage($message, $this->getWhatsAppNumber());

            return [
                'success' => true,
                'message' => 'Claim number notification sent via WhatsApp successfully',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Send stage update WhatsApp message.
     */
    public function sendStageUpdateWhatsApp(string $stageName, ?string $notes = null): array
    {
        try {
            $message = "Dear {$this->customer->name},

Your claim {$this->claim_number} status has been updated to: *{$stageName}*";

            if (!empty($notes)) {
                $message .= "\n\nNotes: {$notes}";
            }

            $message .= "\n\nFor further assistance, please contact us.

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";

            $response = $this->whatsAppSendMessage($message, $this->getWhatsAppNumber());

            return [
                'success' => true,
                'message' => 'Stage update sent via WhatsApp successfully',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Send email notification if enabled for this claim.
     */
    public function sendEmailNotification(string $type, array $additionalData = []): array
    {
        try {
            if (!$this->send_email_notifications) {
                return [
                    'success' => true,
                    'message' => 'Email notifications disabled for this claim',
                    'sent' => false
                ];
            }

            if (empty($this->customer->email)) {
                return [
                    'success' => false,
                    'message' => 'No email address found for customer',
                    'sent' => false
                ];
            }

            Mail::send(new ClaimNotificationMail($this, $type, $additionalData));

            return [
                'success' => true,
                'message' => 'Email notification sent successfully',
                'sent' => true
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send email notification: ' . $e->getMessage(),
                'sent' => false
            ];
        }
    }

    /**
     * Send claim created notification.
     */
    public function sendClaimCreatedNotification(): void
    {
        $this->sendEmailNotification('claim_created');
    }

    /**
     * Send stage update notification.
     */
    public function sendStageUpdateNotification(string $stageName, ?string $description = null, ?string $notes = null): void
    {
        $this->sendEmailNotification('stage_update', [
            'stage_name' => $stageName,
            'description' => $description,
            'notes' => $notes,
        ]);
    }

    /**
     * Send claim number assigned notification.
     */
    public function sendClaimNumberAssignedNotification(): void
    {
        $this->sendEmailNotification('claim_number_assigned');
    }

    /**
     * Send document request notification.
     */
    public function sendDocumentRequestNotification(): void
    {
        $pendingDocuments = $this->documents()
            ->where('is_submitted', false)
            ->select('document_name', 'description', 'is_required')
            ->get()
            ->map(function ($doc) {
                return [
                    'name' => $doc->document_name,
                    'description' => $doc->description,
                    'required' => $doc->is_required,
                ];
            })
            ->toArray();

        $this->sendEmailNotification('document_request', [
            'pending_documents' => $pendingDocuments
        ]);
    }

    /**
     * Send claim closure notification.
     */
    public function sendClaimClosureNotification(?string $reason = null): void
    {
        $this->sendEmailNotification('claim_closed', [
            'closure_reason' => $reason
        ]);
    }
}