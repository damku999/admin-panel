<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\FileUploadService;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MarketingWhatsAppController extends Controller
{
    use WhatsAppApiTrait;

    public function __construct(private FileUploadService $fileUploadService)
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-list|customer-edit', ['only' => ['index', 'show']]);
        $this->middleware('permission:customer-edit', ['only' => ['send']]);
    }

    /**
     * Display the marketing WhatsApp interface
     */
    public function index()
    {
        $customers = Customer::select('id', 'name', 'mobile_number', 'email', 'status')
            ->where('status', 1) // Only active customers
            ->orderBy('name')
            ->get();

        return view('marketing.whatsapp.index', compact('customers'));
    }

    /**
     * Send WhatsApp marketing messages
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message_type' => 'required|in:text,image',
            'message_text' => 'required|string|max:1000',
            'recipients' => 'required|in:all,selected',
            'selected_customers' => 'required_if:recipients,selected|array|min:1',
            'selected_customers.*' => 'exists:customers,id',
            'image' => 'required_if:message_type,image|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Get recipients
            if ($request->recipients === 'all') {
                $customers = Customer::where('status', 1)
                    ->whereNotNull('mobile_number')
                    ->where('mobile_number', '!=', '')
                    ->get();
            } else {
                $customers = Customer::whereIn('id', $request->selected_customers)
                    ->where('status', 1)
                    ->whereNotNull('mobile_number')
                    ->where('mobile_number', '!=', '')
                    ->get();
            }

            if ($customers->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No valid customers found with mobile numbers.');
            }

            $successCount = 0;
            $failedCount = 0;
            $failedCustomers = [];
            $imagePath = null;

            // Handle image upload if message type is image
            if ($request->message_type === 'image' && $request->hasFile('image')) {
                $uploadResult = $this->fileUploadService->uploadFile(
                    $request->file('image'),
                    'marketing/images'
                );

                if ($uploadResult['status']) {
                    $imagePath = storage_path('app/public/' . $uploadResult['file_path']);
                } else {
                    throw new \Exception('Failed to upload image: ' . $uploadResult['message']);
                }
            }

            // Send messages to each customer
            foreach ($customers as $customer) {
                try {
                    if ($request->message_type === 'text') {
                        $response = $this->whatsAppSendMessage(
                            $request->message_text,
                            $customer->mobile_number
                        );
                    } else {
                        $response = $this->whatsAppSendMessageWithAttachment(
                            $request->message_text,
                            $customer->mobile_number,
                            $imagePath
                        );
                    }

                    // Log the attempt
                    \Log::info('Marketing WhatsApp sent', [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'mobile_number' => $customer->mobile_number,
                        'message_type' => $request->message_type,
                        'response' => $response,
                        'sent_by' => auth()->user()->id
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $failedCustomers[] = $customer->name . ' (' . $customer->mobile_number . ')';
                    
                    \Log::error('Marketing WhatsApp failed', [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'mobile_number' => $customer->mobile_number,
                        'error' => $e->getMessage(),
                        'sent_by' => auth()->user()->id
                    ]);
                }
            }

            DB::commit();

            // Prepare response message
            $message = "Marketing messages sent successfully!";
            $message .= "\n✅ Success: {$successCount} messages";
            
            if ($failedCount > 0) {
                $message .= "\n❌ Failed: {$failedCount} messages";
                
                if (count($failedCustomers) <= 5) {
                    $message .= "\nFailed customers: " . implode(', ', $failedCustomers);
                } else {
                    $message .= "\nFailed customers: " . implode(', ', array_slice($failedCustomers, 0, 5)) . ' and ' . (count($failedCustomers) - 5) . ' more...';
                }
            }

            return redirect()->route('marketing.whatsapp.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Marketing WhatsApp bulk send failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'sent_by' => auth()->user()->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send marketing messages: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Preview the customer list based on selection
     */
    public function preview(Request $request)
    {
        if ($request->recipients === 'all') {
            $customers = Customer::select('name', 'mobile_number')
                ->where('status', 1)
                ->whereNotNull('mobile_number')
                ->where('mobile_number', '!=', '')
                ->orderBy('name')
                ->get();
        } else {
            $customerIds = $request->selected_customers ?? [];
            $customers = Customer::select('name', 'mobile_number')
                ->whereIn('id', $customerIds)
                ->where('status', 1)
                ->whereNotNull('mobile_number')
                ->where('mobile_number', '!=', '')
                ->orderBy('name')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'customers' => $customers,
            'count' => $customers->count()
        ]);
    }
}