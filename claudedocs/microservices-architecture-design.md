# Microservices Architecture Design

**Project**: Insurance Management System  
**Date**: September 2024  
**Architecture**: Phased Modernization Strategy

---

## Architecture Overview

### Current State: Modular Monolith
```
┌─────────────────────────────────────────┐
│            Admin Panel (Laravel)        │
├─────────────────┬───────────────────────┤
│   Web Routes    │    API Routes (v1)    │
├─────────────────┼───────────────────────┤
│                 │                       │
│  ┌─────────────────────────────────┐    │
│  │        Service Layer            │    │
│  │  ┌─────────┐ ┌─────────────┐   │    │
│  │  │Customer │ │  Quotation  │   │    │
│  │  │Service  │ │   Service   │   │    │
│  │  └─────────┘ └─────────────┘   │    │
│  │  ┌─────────────┐ ┌───────────┐ │    │
│  │  │Notification │ │  Policy   │ │    │
│  │  │   Service   │ │  Service  │ │    │
│  │  └─────────────┘ └───────────┘ │    │
│  └─────────────────────────────────┘    │
│                 │                       │
├─────────────────┼───────────────────────┤
│  Repository Layer │   Event System      │
├─────────────────┼───────────────────────┤
│          MySQL Database                 │
└─────────────────────────────────────────┘
```

### Target State: Service-Oriented Architecture
```
                     ┌─────────────────┐
                     │   API Gateway   │
                     │   (Kong/Nginx)  │
                     └─────────┬───────┘
                               │
        ┌──────────────────────┼──────────────────────┐
        │                     │                      │
┌───────▼────────┐    ┌───────▼────────┐    ┌───────▼────────┐
│   Admin Web    │    │  Customer Web  │    │  Mobile Apps   │
│   (Laravel)    │    │   (Laravel)    │    │   (React)      │
└────────┬───────┘    └────────┬───────┘    └────────┬───────┘
         │                     │                     │
         └─────────────────────┼─────────────────────┘
                               │
    ┌─────────────────────────▼─────────────────────────┐
    │              Service Mesh                         │
    │         (Istio / Consul Connect)                  │
    └─────┬────────────┬────────────┬───────────────┬───┘
          │            │            │               │
  ┌───────▼──────┐ ┌───▼──────┐ ┌───▼─────────┐ ┌──▼─────────┐
  │ Quotation    │ │Customer  │ │Notification │ │  Policy    │
  │ Service      │ │Service   │ │  Service    │ │  Service   │
  │              │ │          │ │             │ │            │
  │ ┌──────────┐ │ │┌─────────┐│ │┌───────────┐│ │┌──────────┐│
  │ │Quote DB  │ │ ││Cust DB  ││ ││Notify DB  ││ ││Policy DB ││
  │ └──────────┘ │ │└─────────┘│ │└───────────┘│ │└──────────┘│
  └──────────────┘ └───────────┘ └─────────────┘ └────────────┘
```

---

## Service Boundaries & Domain Models

### 1. Quotation Service
**Bounded Context**: Insurance quote generation, comparison, and management

**Domain Models**:
- `Quotation` (aggregate root)
- `QuotationCompany` 
- `AddonCover`
- `PremiumCalculation`

**Database Schema**:
```sql
-- Quotation Service Database
quotations
quotation_companies  
addon_covers
premium_calculations
policy_types (read replica)
insurance_companies (read replica)
```

**API Contracts**:
```yaml
# Quotation Service API
/api/quotations:
  POST: Create quotation
  GET: List quotations
  
/api/quotations/{id}:
  GET: Get quotation details
  PUT: Update quotation
  
/api/quotations/{id}/companies:
  POST: Generate company quotes
  GET: List company quotes
```

**Events Published**:
- `QuotationRequested`
- `QuotationGenerated` 
- `QuotationUpdated`
- `QuotationExpired`

### 2. Customer Service  
**Bounded Context**: Customer lifecycle, family groups, authentication

**Domain Models**:
- `Customer` (aggregate root)
- `FamilyGroup`
- `CustomerAuditLog`
- `CustomerInsurance`

**Database Schema**:
```sql
-- Customer Service Database
customers
family_groups
customer_audit_logs
customer_insurances
customer_sessions
```

**API Contracts**:
```yaml
# Customer Service API
/api/customers:
  POST: Create customer
  GET: List customers
  
/api/customers/{id}:
  GET: Get customer details
  PUT: Update customer
  
/api/customers/{id}/family:
  GET: Get family members
  POST: Add family member
```

**Events Published**:
- `CustomerRegistered`
- `CustomerEmailVerified`
- `CustomerProfileUpdated`
- `FamilyGroupCreated`

### 3. Notification Service
**Bounded Context**: All communication channels (WhatsApp, Email, SMS)

**Domain Models**:
- `NotificationTemplate`
- `MessageQueue`
- `DeliveryStatus`
- `CommunicationPreference`

**Database Schema**:
```sql
-- Notification Service Database
notification_templates
message_queue
delivery_status
communication_preferences
notification_audit_log
```

**API Contracts**:
```yaml
# Notification Service API
/api/notifications/whatsapp:
  POST: Send WhatsApp message
  
/api/notifications/email:
  POST: Send email
  
/api/notifications/{id}/status:
  GET: Get delivery status
```

**Events Consumed**:
- `CustomerRegistered` → Send welcome message
- `QuotationGenerated` → Send quotation WhatsApp/Email
- `PolicyExpiring` → Send renewal reminders

### 4. Policy Service
**Bounded Context**: Active policy management, renewals, claims

**Domain Models**:
- `Policy` (aggregate root)
- `PolicyRenewal` 
- `Commission`
- `PolicyDocument`

**Database Schema**:
```sql
-- Policy Service Database
policies (formerly customer_insurances)
policy_renewals
policy_documents
commission_calculations
```

**API Contracts**:
```yaml
# Policy Service API  
/api/policies:
  POST: Create policy
  GET: List policies
  
/api/policies/{id}:
  GET: Get policy details
  PUT: Update policy
  
/api/policies/{id}/renew:
  POST: Renew policy
```

**Events Published**:
- `PolicyCreated`
- `PolicyRenewed` 
- `PolicyExpiring`
- `CommissionCalculated`

---

## Data Architecture

### Database-Per-Service Strategy

**Challenges Identified**:
1. **Foreign Key Constraints**: 15+ FK relationships across domains
2. **Shared Reference Data**: insurance_companies, policy_types, users
3. **Audit Trail Coupling**: created_by, updated_by references
4. **Family Group Relationships**: Cross-domain customer relationships

**Solution: Hybrid Approach**

**Shared Reference Database**:
```sql
-- Shared across all services (read-only replicas)
insurance_companies
policy_types  
users (authentication service)
audit_users (denormalized for audit trails)
```

**Service-Specific Databases**:
```sql
-- Quotation Service DB
quotations (customer_id as foreign reference, not constraint)
quotation_companies
addon_covers

-- Customer Service DB  
customers
family_groups
customer_audit_logs

-- Notification Service DB
message_queue
delivery_status
notification_templates

-- Policy Service DB
policies
policy_renewals
commission_calculations
```

### Data Consistency Patterns

**Strong Consistency**: 
- Within service boundaries
- Critical business transactions (quote calculations)

**Eventual Consistency**:
- Cross-service data synchronization
- Audit logs and notifications
- Reporting and analytics

**Saga Pattern Implementation**:
```php
// Customer Registration Saga
1. CreateCustomer (Customer Service)
2. SetupCustomerNotifications (Notification Service) 
3. InitializeCustomerQuotations (Quotation Service)
4. CompensatingTransactions on failure
```

---

## Communication Patterns

### 1. Synchronous Communication (REST APIs)

**Use Cases**:
- Real-time data validation
- Interactive user operations
- Critical business workflows

**API Gateway Configuration**:
```nginx
# Kong/Nginx API Gateway
upstream customer-service {
    server customer-service:8080;
}

upstream quotation-service {
    server quotation-service:8080;
}

# Route /api/customers/* to Customer Service
location /api/customers/ {
    proxy_pass http://customer-service;
}

# Route /api/quotations/* to Quotation Service  
location /api/quotations/ {
    proxy_pass http://quotation-service;
}
```

### 2. Asynchronous Communication (Event-Driven)

**Message Broker**: Redis Streams / RabbitMQ

**Event Flow Example**:
```
Customer Registration Flow:
1. Customer Service publishes CustomerRegistered event
2. Notification Service consumes → sends welcome email
3. Quotation Service consumes → initializes quote templates
4. Policy Service consumes → sets up policy tracking
```

**Event Schema**:
```json
{
  "eventId": "uuid",
  "eventType": "CustomerRegistered", 
  "aggregateId": "customer-123",
  "version": 1,
  "timestamp": "2024-09-09T10:00:00Z",
  "data": {
    "customerId": "customer-123",
    "name": "John Doe",
    "email": "john@example.com"
  },
  "metadata": {
    "correlationId": "request-456",
    "userId": "user-789"
  }
}
```

### 3. Data Synchronization

**Event Sourcing Integration**:
- Already implemented in current system
- Events stored in central event store
- Services can rebuild state from events
- Cross-service data consistency via event replay

---

## Technology Stack

### Service Runtime
- **Language**: PHP 8.2+ (maintain current expertise)
- **Framework**: Laravel 10+ (microservice template)
- **Database**: MySQL 8+ (per service)
- **Caching**: Redis (shared and per-service)

### Infrastructure
- **Containerization**: Docker + Docker Compose (development)
- **Orchestration**: Kubernetes (production)
- **Service Mesh**: Istio (traffic management, security)
- **API Gateway**: Kong (rate limiting, authentication)

### Monitoring & Observability
- **Tracing**: Jaeger (distributed tracing)
- **Metrics**: Prometheus + Grafana
- **Logging**: ELK Stack (Elasticsearch, Logstash, Kibana)
- **Health Checks**: Already implemented in current system

### CI/CD Pipeline
```yaml
# Service-specific pipelines
name: Quotation Service CI/CD
on:
  push:
    paths: ['services/quotation/**']
    
jobs:
  test:
    - Unit tests
    - Integration tests  
    - Contract tests
    
  build:
    - Build Docker image
    - Security scanning
    - Push to registry
    
  deploy:
    - Deploy to staging
    - Run E2E tests
    - Deploy to production (blue-green)
```

---

## Security Architecture

### Service-to-Service Authentication

**JWT Token Flow**:
```
1. API Gateway validates user JWT
2. Gateway issues service-to-service token
3. Services validate tokens with shared secret
4. Token includes user context and permissions
```

**mTLS for Internal Communication**:
- All inter-service communication encrypted
- Certificate-based service authentication
- Managed by service mesh (Istio)

### Data Privacy & Compliance
- **Data Classification**: Customer PII vs. business data
- **Encryption**: At-rest and in-transit encryption
- **Audit Trails**: Distributed audit logging
- **GDPR Compliance**: Data deletion across services

---

## Migration Strategy

### Phase 1: Modular Monolith (6-8 months)
**Goal**: Prepare for microservices without service extraction

**Implementation**:
```php
// Current structure
app/Services/QuotationService.php

// Target modular structure
app/Modules/Quotation/
├── Services/QuotationService.php
├── Contracts/QuotationServiceInterface.php
├── Repositories/QuotationRepository.php
├── Events/QuotationGenerated.php
├── Listeners/SendQuotationNotification.php
├── Http/Controllers/Api/QuotationController.php
├── Http/Requests/CreateQuotationRequest.php
└── Database/Migrations/
```

**Benefits**:
- ✅ Clear service boundaries
- ✅ Independent development teams  
- ✅ API contract definitions
- ✅ Reduced coupling within monolith
- ✅ Testing isolation

**Deliverables**:
1. Modular directory structure
2. API contracts for each module  
3. Event-driven communication between modules
4. Module-specific tests
5. API documentation per module

### Phase 2: Service Extraction (8-12 months)
**Goal**: Extract lowest-risk services first

**Order of Extraction**:
1. **Notification Service** (lowest coupling, highest independence)
2. **Quotation Service** (business-critical but well-bounded)
3. **Policy Service** (moderate complexity)
4. **Customer Service** (highest coupling, extract last)

**Per-Service Migration**:
```
1. Week 1-2: Database schema migration
2. Week 3-4: Service implementation  
3. Week 5-6: API integration testing
4. Week 7-8: Production deployment with feature flags
5. Week 9-10: Monitor and optimize
6. Week 11-12: Remove old monolith code
```

### Phase 3: Full Microservices (12+ months)
**Goal**: Complete service mesh with advanced patterns

**Advanced Features**:
- Circuit breakers and retry policies
- Distributed caching strategies
- Advanced monitoring and alerting
- Service mesh security policies
- Multi-region deployments

---

## Risk Analysis & Mitigation

### High-Risk Areas

**1. Data Consistency Failures**
- **Risk**: Cross-service transactions fail partially
- **Mitigation**: Saga pattern with compensating transactions
- **Monitoring**: Transaction success/failure rates

**2. Network Latency**  
- **Risk**: Service-to-service calls increase response time
- **Mitigation**: Service mesh with intelligent routing
- **Target**: <100ms for 95th percentile inter-service calls

**3. Service Cascade Failures**
- **Risk**: One service failure brings down entire system
- **Mitigation**: Circuit breakers, bulkhead pattern
- **Implementation**: Hystrix/resilience4j integration

**4. Operational Complexity**
- **Risk**: Too many services for small team to manage
- **Mitigation**: Start with 2-3 services maximum
- **Team Expansion**: Hire DevOps engineer before Phase 2

### Success Criteria

**Technical KPIs**:
- Service availability: >99.9% per service
- API response time: <200ms for 95th percentile
- Cross-service transaction success: >99.5%
- Service startup time: <30 seconds

**Business KPIs**:
- Development velocity: 25% improvement by month 18
- Feature deployment frequency: 2x improvement
- Time to market: 30% reduction for new features
- System reliability: 50% reduction in downtime

---

## Cost-Benefit Analysis

### Implementation Investment

**Phase 1 (Modular Monolith)**:
- Development: 4 person-months
- Infrastructure: Current (no change)
- **Total**: $40,000-50,000

**Phase 2 (Service Extraction)**:
- Development: 8 person-months  
- Infrastructure: $1,500/month additional
- DevOps tooling: $800/month
- **Total**: $80,000-100,000

**Phase 3 (Full Microservices)**:
- Development: 6 person-months
- Infrastructure: $2,500/month additional  
- Advanced tooling: $1,200/month
- **Total**: $60,000-75,000

**Total 18-Month Investment**: $180,000-225,000

### Expected Benefits

**Quantified Returns**:
- **Performance**: 40% improvement in quote generation (high-load scenarios)
- **Reliability**: 99.9% availability vs 99.5% current
- **Scalability**: 3x capacity for peak loads at same cost
- **Development Speed**: 25% faster feature delivery after stabilization

**ROI Timeline**:
- Month 1-6: Investment phase (negative ROI)
- Month 7-12: Break-even period  
- Month 13+: Positive ROI (25-35% annually)

---

## Recommendation Summary

### Recommended Approach: **Phased Modernization**

**Phase 1 Priority**: Implement modular monolith structure
- **Timeline**: 6-8 months
- **Risk**: Low
- **Investment**: $40,000-50,000
- **Team Size**: Current team (3-5 developers)

**Phase 2 Decision Point**: Evaluate after Phase 1 completion
- **Criteria**: Business growth, team expansion, performance requirements
- **Go/No-Go**: Based on Phase 1 success metrics

**Key Success Factors**:
1. **Team Expansion**: Hire DevOps engineer before Phase 2
2. **Monitoring First**: Implement comprehensive observability
3. **API Contracts**: Define clear service boundaries
4. **Gradual Migration**: Feature flags for safe rollbacks

The current codebase shows **excellent architectural foundations** for microservices evolution, but the business context suggests a **cautious, measured approach** is most appropriate.