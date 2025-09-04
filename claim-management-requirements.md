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
2. Use Wild Card Search to find policy by policy number
3. Click "Claim Intimate" button to create new claim
4. Send WhatsApp message to party asking: "Would you like to send list of documents - Yes or No?"

### 3.2 Document Requirements by Insurance Type

#### 3.2.1 Health Insurance Documents
When customer selects Health Insurance, collect the following information:
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

#### 3.2.2 Truck Insurance Documents
When customer selects Truck Insurance, collect the following documents:
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
- Allow manual addition of claim number
- Once claim number is added, enable WhatsApp message functionality
- Send automated message with claim number to customer

### 4.2 Document Tracking
- Update status of documents received from Point 4.2 (document lists)
- Allow sending WhatsApp messages for remaining/pending documents
- Track which documents are received vs pending

## 5. Stage Management System

### 5.1 Multi-Stage Tracking
- Maintain multiple stages against each claim
- Allow manual addition of new stages with custom stage names
- Include notes/comments for each stage
- Maintain complete stage history with dates
- Enable sharing of stage information via WhatsApp

### 5.2 Stage History
- Keep chronological record of all stage transitions
- Store date and time for each stage change
- Allow notes/comments for each stage update

## 6. Liability Management

### 6.1 Cashless Type
- Claim Amount
- Salvage Amount  
- Less Claim Charge
- Amount to be Paid by Customer (calculated field)

### 6.2 Reimbursement Type
- Claim Amount
- Less Salvage Amount
- Less deductions  
- Claim Amount Received (calculated field)

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

## 9. WhatsApp Integration

### 9.1 Pre-defined Message Templates

#### 9.1.1 Health Insurance Intimation
```
For health Insurance - Kindly provide below mention details for Claim intimation
[Followed by the 10-point list from section 3.2.1]
```

#### 9.1.2 Truck Insurance Intimation  
```
For Truck Insurance - Kindly provide below mention details for Claim intimation
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
- Utilize existing WhatsApp integration in the project
- Implement message sending based on specific use cases
- Support dynamic content insertion (claim numbers, vehicle numbers, document lists)

## 10. Database Requirements

### 10.1 New Tables Needed
- Claims master table
- Claim documents tracking table  
- Claim stages history table
- Claim liability details table

### 10.2 Data Relationships
- Link to existing customer/policy tables
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

## 12. Security & Permissions
- Utilize existing role-based access control
- Ensure data privacy for sensitive claim information
- Audit trail for all claim-related activities
