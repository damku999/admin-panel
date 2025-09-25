<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Helpers Test Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">UI Helpers Test Page</h1>

        <div class="row">
            <div class="col-md-6">
                <h3>Toast Notifications</h3>
                <div class="d-grid gap-2 mb-4">
                    <button class="btn btn-success" onclick="show_notification('success', 'Success message!')">
                        Success Toast
                    </button>
                    <button class="btn btn-danger" onclick="show_notification('error', 'Error message!')">
                        Error Toast
                    </button>
                    <button class="btn btn-warning" onclick="show_notification('warning', 'Warning message!')">
                        Warning Toast
                    </button>
                    <button class="btn btn-info" onclick="show_notification('info', 'Info message!')">
                        Info Toast
                    </button>
                </div>

                <h3>Modals</h3>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="testConfirmationModal()">
                        Test Confirmation Modal
                    </button>
                    <button class="btn btn-secondary" onclick="testPasswordModal()">
                        Test Password Modal
                    </button>
                    <button class="btn btn-info" onclick="testDeviceNameModal()">
                        Test Device Name Modal
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <h3>Test Results</h3>
                <div id="testResults" class="border rounded p-3" style="min-height: 200px; background: #f8f9fa;">
                    <p class="text-muted">Click buttons to test functionality. Results will appear here.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="{{ asset('js/ui-helpers.js') }}"></script>

    <script>
        function logResult(message) {
            const results = document.getElementById('testResults');
            const timestamp = new Date().toLocaleTimeString();
            results.innerHTML += `<div><strong>${timestamp}:</strong> ${message}</div>`;
            results.scrollTop = results.scrollHeight;
        }

        function testConfirmationModal() {
            showConfirmationModal(
                'Test Confirmation',
                'This is a test confirmation modal. Click confirm to see the result.',
                'danger',
                function() {
                    logResult('✅ Confirmation modal - User clicked CONFIRM');
                    show_notification('success', 'You confirmed the action!');
                }
            );
            logResult('🔧 Opened confirmation modal');
        }

        function testPasswordModal() {
            showPasswordModal(
                'Test Password Input',
                'Please enter a test password:',
                function(password) {
                    logResult(`✅ Password modal - Entered: "${password}"`);
                    show_notification('info', `Password entered: ${password}`);
                }
            );
            logResult('🔧 Opened password modal');
        }

        function testDeviceNameModal() {
            showDeviceNameModal(
                'Test Device Name',
                'Test Device',
                function(deviceName) {
                    logResult(`✅ Device name modal - Entered: "${deviceName}"`);
                    show_notification('info', `Device name: ${deviceName}`);
                }
            );
            logResult('🔧 Opened device name modal');
        }

        // Log when helpers are loaded
        document.addEventListener('DOMContentLoaded', function() {
            logResult('🚀 UI Helpers test page loaded');

            // Test if functions are available
            const functions = ['show_notification', 'showConfirmationModal', 'showPasswordModal', 'showDeviceNameModal'];
            functions.forEach(func => {
                if (typeof window[func] === 'function') {
                    logResult(`✅ ${func} is available`);
                } else {
                    logResult(`❌ ${func} is NOT available`);
                }
            });
        });
    </script>
</body>
</html>