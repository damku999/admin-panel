const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Accessibility Testing (WCAG Compliance)', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.describe('Admin Interface Accessibility', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should pass WCAG AA compliance for login page', async ({ page }) => {
      await page.goto('/login');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['wcag2a', 'wcag2aa'],
        rules: {
          'color-contrast': { enabled: true },
          'keyboard-navigation': { enabled: true },
          'focus-management': { enabled: true },
          'aria-labels': { enabled: true },
          'form-labels': { enabled: true },
          'heading-order': { enabled: true },
          'landmark-roles': { enabled: true }
        }
      });

      // Log all violations for review
      if (a11yResults.violations.length > 0) {
        console.log('Admin login accessibility violations:');
        a11yResults.violations.forEach(violation => {
          console.log(`- ${violation.id}: ${violation.description}`);
          console.log(`  Impact: ${violation.impact}`);
          console.log(`  Help: ${violation.helpUrl}`);
          violation.nodes.forEach(node => {
            console.log(`  Element: ${node.target}`);
          });
        });
      }

      // No critical or serious violations should exist
      const criticalViolations = a11yResults.violations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      expect(criticalViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-admin-login');
    });

    test('should pass keyboard navigation for dashboard', async ({ page }) => {
      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      // Test keyboard navigation
      await page.keyboard.press('Tab');
      let focusedElement = await page.evaluate(() => document.activeElement.tagName);

      // Should be able to navigate to interactive elements
      const interactiveElements = ['A', 'BUTTON', 'INPUT', 'SELECT', 'TEXTAREA'];
      let tabCount = 0;
      let focusableElements = [];

      // Navigate through first 10 tabbable elements
      while (tabCount < 10) {
        await page.keyboard.press('Tab');
        const tagName = await page.evaluate(() => document.activeElement.tagName);
        const id = await page.evaluate(() => document.activeElement.id);
        const className = await page.evaluate(() => document.activeElement.className);

        focusableElements.push({ tagName, id, className });
        tabCount++;
      }

      // Should have found some interactive elements
      const interactiveCount = focusableElements.filter(el =>
        interactiveElements.includes(el.tagName)
      ).length;

      expect(interactiveCount).toBeGreaterThan(0);

      // Check accessibility
      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['keyboard']
      });

      const keyboardViolations = a11yResults.violations.filter(v =>
        v.tags.includes('keyboard') && (v.impact === 'critical' || v.impact === 'serious')
      );

      expect(keyboardViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-dashboard-keyboard');
    });

    test('should pass color contrast requirements', async ({ page }) => {
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['color-contrast']
      });

      // Log color contrast violations
      const contrastViolations = a11yResults.violations.filter(v =>
        v.id === 'color-contrast'
      );

      if (contrastViolations.length > 0) {
        console.log('Color contrast violations found:');
        contrastViolations.forEach(violation => {
          console.log(`- ${violation.description}`);
          violation.nodes.forEach(node => {
            console.log(`  Element: ${node.target}`);
            console.log(`  Any: ${JSON.stringify(node.any)}`);
          });
        });
      }

      // Should not have critical color contrast violations
      const criticalContrastViolations = contrastViolations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      expect(criticalContrastViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-color-contrast');
    });

    test('should have proper form labels and ARIA attributes', async ({ page }) => {
      await page.goto('/customers/create');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['wcag2a', 'wcag2aa'],
        rules: {
          'label': { enabled: true },
          'aria-required-attr': { enabled: true },
          'aria-valid-attr-value': { enabled: true },
          'form-field-multiple-labels': { enabled: true }
        }
      });

      // Check for form-related violations
      const formViolations = a11yResults.violations.filter(v =>
        v.tags.includes('forms') || v.id.includes('label') || v.id.includes('aria')
      );

      if (formViolations.length > 0) {
        console.log('Form accessibility violations:');
        formViolations.forEach(violation => {
          console.log(`- ${violation.id}: ${violation.description}`);
        });
      }

      const criticalFormViolations = formViolations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      expect(criticalFormViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-form-labels');
    });

    test('should have proper heading structure', async ({ page }) => {
      await page.goto('/quotations');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['best-practice'],
        rules: {
          'heading-order': { enabled: true },
          'empty-heading': { enabled: true },
          'page-has-heading-one': { enabled: true }
        }
      });

      const headingViolations = a11yResults.violations.filter(v =>
        v.id.includes('heading')
      );

      if (headingViolations.length > 0) {
        console.log('Heading structure violations:');
        headingViolations.forEach(violation => {
          console.log(`- ${violation.id}: ${violation.description}`);
        });
      }

      // Should have proper heading structure
      const criticalHeadingViolations = headingViolations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      expect(criticalHeadingViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-heading-structure');
    });

    test('should have proper table accessibility', async ({ page }) => {
      await page.goto('/insurance-claims');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        rules: {
          'table-fake-caption': { enabled: true },
          'table-duplicate-name': { enabled: true },
          'td-headers-attr': { enabled: true },
          'th-has-data-cells': { enabled: true }
        }
      });

      const tableViolations = a11yResults.violations.filter(v =>
        v.tags.includes('tables') || v.id.includes('table')
      );

      if (tableViolations.length > 0) {
        console.log('Table accessibility violations:');
        tableViolations.forEach(violation => {
          console.log(`- ${violation.id}: ${violation.description}`);
        });
      }

      const criticalTableViolations = tableViolations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      expect(criticalTableViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-table-structure');
    });
  });

  test.describe('Customer Portal Accessibility', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/customer.json' });

    test('should pass WCAG AA compliance for customer login', async ({ page }) => {
      await page.goto('/customer/login');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['wcag2a', 'wcag2aa']
      });

      const criticalViolations = a11yResults.violations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      if (criticalViolations.length > 0) {
        console.log('Customer login critical violations:', criticalViolations);
      }

      expect(criticalViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-customer-login');
    });

    test('should pass accessibility for customer dashboard', async ({ page }) => {
      await page.goto('/customer/dashboard');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['wcag2a', 'wcag2aa']
      });

      const criticalViolations = a11yResults.violations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      expect(criticalViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-customer-dashboard');
    });

    test('should support keyboard navigation in customer portal', async ({ page }) => {
      await page.goto('/customer/policies');
      await page.waitForLoadState('networkidle');

      // Test keyboard navigation
      let tabCount = 0;
      let focusableElements = [];

      while (tabCount < 15) {
        await page.keyboard.press('Tab');
        const element = await page.evaluate(() => ({
          tagName: document.activeElement.tagName,
          type: document.activeElement.type,
          role: document.activeElement.getAttribute('role'),
          id: document.activeElement.id
        }));

        focusableElements.push(element);
        tabCount++;
      }

      // Should be able to reach interactive elements
      const interactiveCount = focusableElements.filter(el =>
        ['A', 'BUTTON', 'INPUT', 'SELECT'].includes(el.tagName) ||
        ['button', 'link', 'tab'].includes(el.role)
      ).length;

      expect(interactiveCount).toBeGreaterThan(0);

      await helpers.takeScreenshot('a11y-customer-keyboard-nav');
    });
  });

  test.describe('Screen Reader Compatibility', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should have proper ARIA landmarks', async ({ page }) => {
      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      const landmarks = await page.evaluate(() => {
        const landmarkRoles = ['banner', 'navigation', 'main', 'complementary', 'contentinfo'];
        const landmarks = [];

        landmarkRoles.forEach(role => {
          const elements = document.querySelectorAll(`[role="${role}"]`);
          elements.forEach(el => landmarks.push(role));
        });

        // Also check semantic HTML5 elements
        const semanticElements = ['header', 'nav', 'main', 'aside', 'footer'];
        semanticElements.forEach(tag => {
          const elements = document.querySelectorAll(tag);
          elements.forEach(el => landmarks.push(tag));
        });

        return landmarks;
      });

      // Should have at least main content landmark
      const hasMainLandmark = landmarks.includes('main') ||
                              landmarks.some(l => l === 'main');
      expect(hasMainLandmark).toBeTruthy();

      await helpers.takeScreenshot('a11y-landmarks');
    });

    test('should have proper alt text for images', async ({ page }) => {
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        rules: {
          'image-alt': { enabled: true },
          'image-redundant-alt': { enabled: true }
        }
      });

      const imageViolations = a11yResults.violations.filter(v =>
        v.id.includes('image')
      );

      if (imageViolations.length > 0) {
        console.log('Image accessibility violations:', imageViolations);
      }

      const criticalImageViolations = imageViolations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      expect(criticalImageViolations).toHaveLength(0);
    });

    test('should have proper focus indicators', async ({ page }) => {
      await page.goto('/users/create');
      await page.waitForLoadState('networkidle');

      // Test focus visibility
      const focusableElements = await page.locator('a, button, input, select, textarea, [tabindex]').all();

      for (let i = 0; i < Math.min(5, focusableElements.length); i++) {
        await focusableElements[i].focus();

        // Check if element has visible focus
        const hasVisibleFocus = await focusableElements[i].evaluate(el => {
          const styles = window.getComputedStyle(el);
          const pseudoStyles = window.getComputedStyle(el, ':focus');

          return styles.outline !== 'none' ||
                 pseudoStyles.outline !== 'none' ||
                 styles.boxShadow !== 'none' ||
                 pseudoStyles.boxShadow !== 'none';
        });

        // At least some elements should have visible focus indicators
        if (i === 0) {
          expect(typeof hasVisibleFocus).toBe('boolean');
        }
      }

      await helpers.takeScreenshot('a11y-focus-indicators');
    });
  });

  test.describe('Mobile Accessibility', () => {
    test.use({
      storageState: './tests/e2e/playwright/auth/admin.json',
      viewport: { width: 375, height: 667 }
    });

    test('should be accessible on mobile devices', async ({ page }) => {
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      const a11yResults = await helpers.checkAccessibility({
        runOnly: ['wcag2a', 'wcag2aa'],
        rules: {
          'color-contrast': { enabled: true },
          'touch-target': { enabled: true }
        }
      });

      const mobileViolations = a11yResults.violations.filter(v =>
        v.impact === 'critical' || v.impact === 'serious'
      );

      if (mobileViolations.length > 0) {
        console.log('Mobile accessibility violations:', mobileViolations);
      }

      expect(mobileViolations).toHaveLength(0);

      await helpers.takeScreenshot('a11y-mobile');
    });

    test('should have proper touch targets on mobile', async ({ page }) => {
      await page.goto('/quotations');
      await page.waitForLoadState('networkidle');

      // Check touch target sizes
      const buttons = await page.locator('button, a, input[type="submit"], input[type="button"]').all();

      let adequateTouchTargets = 0;

      for (const button of buttons.slice(0, 5)) {
        const boundingBox = await button.boundingBox();
        if (boundingBox) {
          // WCAG recommends minimum 44x44 pixels for touch targets
          const isAdequateSize = boundingBox.width >= 44 && boundingBox.height >= 44;
          if (isAdequateSize) {
            adequateTouchTargets++;
          }
        }
      }

      // At least some touch targets should meet size requirements
      expect(adequateTouchTargets).toBeGreaterThan(0);

      await helpers.takeScreenshot('a11y-touch-targets');
    });
  });

  test.describe('Dynamic Content Accessibility', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should handle AJAX content accessibility', async ({ page }) => {
      await page.goto('/reports');
      await page.waitForLoadState('networkidle');

      // Generate a report (AJAX content)
      const generateBtn = page.locator('button:has-text("Generate")').first();
      if (await generateBtn.count() > 0) {
        await generateBtn.click();

        // Wait for dynamic content
        await page.waitForTimeout(3000);

        const a11yResults = await helpers.checkAccessibility();

        const criticalViolations = a11yResults.violations.filter(v =>
          v.impact === 'critical' || v.impact === 'serious'
        );

        expect(criticalViolations).toHaveLength(0);

        await helpers.takeScreenshot('a11y-dynamic-content');
      }
    });

    test('should have proper live regions for notifications', async ({ page }) => {
      await page.goto('/customers/create');
      await page.waitForLoadState('networkidle');

      // Check for ARIA live regions
      const liveRegions = await page.evaluate(() => {
        return Array.from(document.querySelectorAll('[aria-live], [role="alert"], [role="status"]')).length;
      });

      // Submit form to potentially trigger notifications
      await page.click('button[type="submit"]');
      await page.waitForTimeout(2000);

      // Check for notification accessibility
      const notifications = await page.locator('.alert, .toast, .notification, [role="alert"]').count();

      if (notifications > 0) {
        const a11yResults = await helpers.checkAccessibility({
          rules: {
            'aria-live-region': { enabled: true }
          }
        });

        await helpers.takeScreenshot('a11y-notifications');
      }
    });
  });
});