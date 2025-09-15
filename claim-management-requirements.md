# Claim Management Module Requirements

## 1. Overview
This document outlines the requirements for the Claim Management module that will be integrated into the existing system with current role-based permissions.

## 2. Claim Management Module

### 2.1 Module Overview
- **Single Module**: Claim Management Module
- New module to be added to the existing system
- Utilize existing role-based permission system
- Create new database tables as needed

### 2.2 Module Features/Components
The following are features within the Claim Management Module:
- **Wild Card Search**: Policy number search functionality during claim creation
- **Claim Intimate Click**: Action button on claims list page to initiate claim process
- **WhatsApp Integration**: Send messages at various stages
- **Document Management**: Track required documents by insurance type
- **Stage Management**: Multi-stage tracking with history
- **Liability Management**: Handle Cashless and Reimbursement types
- **Claim Closure**: Complete claim processing

## 3. Claim Creation Workflow

### 3.1 Initial Setup
1. Access Claim Management module
2. Use Wild Card Search to find policy by:
   - Registration number (from customer_insurances table)
   - Policy number (from customer_insurances table)
   - Customer name, mobile number, or email (from customer table)
   - Auto-complete dropdown will populate with basic information
   - If customer has multiple active policies, show customer name multiple times in dropdown
   - Only show policies where due date has not passed (active policies only)
3. Click "Claim Intimate" button to create new claim
4. Capture basic claim information (optional fields that can be updated anytime):
   - Incident date
   - Description
   - Other relevant details
5. Send WhatsApp message to party asking: "Would you like to send list of documents - Yes or No?"
6. If customer responds "No" to document list - no further action required at this stage

### 3.2 Document Requirements by Insurance Type
**Note**: System supports only two insurance types: Health and Vehicle/Truck Insurance
- System will attempt to set default insurance type based on customer_insurances table data
- Final insurance type selection will be manual by user during claim creation

#### 3.2.1 Health Insurance Documents
When customer has Health Insurance policy, collect the following information:
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

#### 3.2.2 Vehicle/Truck Insurance Documents
When customer has Vehicle/Truck Insurance policy, collect the following documents:
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

## 4. Claim Number Management

### 4.1 Claim Number Assignment
- Allow manual addition of claim number (provided by insurance company)
- Claim number format is flexible - no specific format required as each insurance company has different approach
- Claim number is entered manually when received from insurance company (not required during initial claim creation)
- Once claim number is added, enable WhatsApp message functionality
- Send automated message with claim number to customer

### 4.2 Document Tracking
- Simple checkbox system: document received or not received
- No document upload functionality - only tracking status
- Display all possible documents for each insurance type (Health/Vehicle)
- Allow sending WhatsApp messages for remaining/pending documents
- Track which documents are received vs pending with checkbox interface

## 5. Stage Management System

### 5.1 Multi-Stage Tracking
- Maintain multiple stages against each claim
- Allow manual addition of new stages with completely custom stage names (no predefined templates)
- Custom stages serve as the overall claim status - no separate status field required
- Include notes/comments for each stage
- Maintain complete stage history with dates
- Enable sharing of stage information via WhatsApp
- No role restrictions - any user can create/update stages
- Manual stage transitions only - provide option to change status similar to other status updates in system

### 5.2 Stage History
- Keep chronological record of all stage transitions
- Store date and time for each stage change
- Allow notes/comments for each stage update
- Display stage history as expandable list with option to view details and add more custom stages

## 6. Liability Management

### 6.1 Cashless Type
- Claim Amount (manual entry)
- Salvage Amount (manual entry)
- Less Claim Charge (manual entry)
- Amount to be Paid by Customer (manual entry - not auto-calculated)

### 6.2 Reimbursement Type
- Claim Amount (manual entry)
- Less Salvage Amount (manual entry)
- Less deductions (manual entry)
- Claim Amount Received (manual entry - not auto-calculated)

**Note**: Only Cashless and Reimbursement types are supported - no additional liability types planned.

## 7. Claim Closure

### 7.1 Close Claim Functionality
- Provide "Close Claim" button
- Mark claim as completed/closed
- Maintain closure date and reason

## 8. Claims List View

### 8.1 Display Fields
The claims list page should display the following columns:
1. Name of Customer
2. Policy No
3. Claim No
4. Vehicle No (from insurance/customer details)
5. Claim Stage (current stage)

### 8.2 List Functionality
- **Filtering**: Include filters for status, date range, insurance type, and other relevant criteria
- **Search**: Provide search functionality to find claims by claim number, customer name, policy number, etc.
- **Claim Editing**: Allow editing of basic claim information after creation (customer details, policy details)
- **No Duplicate Prevention**: System will not prevent multiple claims for the same policy/incident at this stage

## 9. WhatsApp Integration

### 9.1 Pre-defined Message Templates

#### 9.1.1 Health Insurance Intimation
```
For health Insurance - Kindly provide below mention details for Claim intimation
[Followed by the 10-point list from section 3.2.1]
```

#### 9.1.2 Vehicle/Truck Insurance Intimation
```
For Vehicle/Truck Insurance - Kindly provide below mention details for Claim intimation
[Followed by the 20-point list from section 3.2.2]
```

#### 9.1.3 Pending Documents Reminder
```
Below are the Documents pending from your side, Send it urgently for hassle free claim service
[Dynamic list of pending documents]
```

#### 9.1.4 Claim Number Notification
```
Dear customer your Claim Number [Claim Number] is generated against your vehicle number [Vehicle Number]. For further assistance kindly contact me.
```

### 9.2 WhatsApp Integration Requirements
- Utilize existing WhatsApp integration in the project (existing trait and service available)
- Always show message preview before sending
- Use customer's mobile number from customer table as default
- Provide option to use different WhatsApp number if needed
- Store alternative WhatsApp number in claims table for consecutive messages
- Implement message sending based on specific use cases
- Support dynamic content insertion (claim numbers, vehicle numbers, document lists)
- No message delivery tracking or customer response tracking required at this stage

## 10. Database Requirements

### 10.1 New Tables Needed
- Claims master table (include fields for incident date, description, alternative WhatsApp number, email notification preference)
- Claim documents tracking table
- Claim stages history table
- Claim liability details table

### 10.2 Data Relationships
- Link to existing Customer and Family Group system (family_groups & family_members tables)
- Link to existing customer_insurances table for policy information
- Claims can be linked to individual customers or family groups
- Family members should have access to family-related claims
- Maintain referential integrity with existing system

## 11. Technical Specifications

### 11.1 System Integration
- Integrate with existing role-based permission system
- Use existing database infrastructure
- Leverage current WhatsApp integration capabilities

### 11.2 User Interface Requirements
- Consistent with existing system design
- Responsive design for mobile and desktop
- Intuitive workflow for claim processing

## 12. Email Notifications
- Each claim should have an email notification preference checkbox
- Default setting: email notifications disabled (unchecked)
- Only send email notifications if checkbox is ticked for that specific claim
- Email notifications for claim updates and stage changes

## 13. Security & Permissions
- Utilize existing Spatie role-based access control system
- No additional permissions required - keep simple
- Customer portal access: customers can view their own claims (read-only)
- Family members can only view existing family claims (read-only) - cannot create claims for other family members
- Ensure data privacy for sensitive claim information
- Audit trail for all claim-related activities using existing audit system
