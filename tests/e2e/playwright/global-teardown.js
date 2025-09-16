async function globalTeardown() {
  console.log('🧹 Starting global teardown...');

  // Clean up test artifacts
  const fs = require('fs');
  const path = require('path');

  try {
    // Clean up authentication files
    const authDir = './tests/e2e/playwright/auth';
    if (fs.existsSync(authDir)) {
      const files = fs.readdirSync(authDir);
      files.forEach(file => {
        if (file.endsWith('.json')) {
          fs.unlinkSync(path.join(authDir, file));
        }
      });
    }

    // Clean up temporary test files
    const tempFiles = [
      './tests/e2e/test-results',
      './tests/e2e/playwright-report'
    ];

    tempFiles.forEach(dir => {
      if (fs.existsSync(dir) && process.env.CLEANUP_ARTIFACTS === 'true') {
        fs.rmSync(dir, { recursive: true, force: true });
      }
    });

    console.log('✅ Cleanup completed');
  } catch (error) {
    console.error('❌ Cleanup failed:', error.message);
  }

  console.log('✅ Global teardown completed');
}

module.exports = globalTeardown;